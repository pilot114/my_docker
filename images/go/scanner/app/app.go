package main

import (
	"crypto/tls"
	"encoding/json"
	"fmt"
	"net/http"
	"os"
	"strconv"
	"strings"
	"time"
)

// ResponseInfo : ответ
type ResponseInfo struct {
	headers map[string]string
	time    time.Duration
	ip      string
	error   string
}

func getHeaders(url string) ResponseInfo {

	transport := &http.Transport{
		TLSClientConfig: &tls.Config{InsecureSkipVerify: true},
	}
	timeout := time.Duration(3 * time.Second)
	client := &http.Client{Transport: transport, Timeout: timeout}

	start := time.Now()
	response, err := client.Get(fmt.Sprintf("http://%s", url))
	duration := time.Since(start)

	info := ResponseInfo{make(map[string]string), duration, url, ""}

	if err != nil {
		info.error = fmt.Sprintf("Error download: %s", err)
		return info
	}

	if response.StatusCode != http.StatusOK {
		info.error = fmt.Sprintf("Error HTTP Status: %s", response.Status)
		return info
	}

	for k, v := range response.Header {
		info.headers[strings.ToLower(k)] = string(v[0])
	}
	return info
}

func worker(wid int, ips <-chan string, headers chan<- ResponseInfo) {
	for ip := range ips {
		//log.Debugf("worker %d get ip %s", wid, ip)
		headers <- getHeaders(ip)
	}
}

func main() {
	a := os.Args[1]
	b := os.Args[2]
	workerLimit, _ := strconv.Atoi(os.Args[3])

	// каналы: источник адресов и получатель заголовков
	ips := make(chan string, 66000)       // over 65535
	resInfo := make(chan ResponseInfo, 3) // 1 достаточно, но возьмём с запасом

	// стартуем воркеров
	for wid := 1; wid <= workerLimit; wid++ {
		go worker(wid, ips, resInfo)
	}

	start := time.Now()
	ip := ""

	// https://ant.isi.edu/address/
	go func() {
		for c := 0; c <= 255; c++ {
			for d := 0; d <= 255; d++ {
				ip = fmt.Sprintf("%s.%s.%s.%s", a, b, strconv.Itoa(c), strconv.Itoa(d))
				ips <- ip
			}
		}
		close(ips)
	}()

	count := 0
	for i := 1; i <= 256*256; i++ {
		info := <-resInfo
		if len(info.headers) > 0 {
			jsonHeaders, _ := json.Marshal(info.headers)
			fmt.Printf("%s %d %s\n", jsonHeaders, info.time.Nanoseconds()/1e6, info.ip) // milliseconds
			count = count + 1
		}
		if info.error != "" {
			fmt.Fprintf(os.Stderr, "%s %d %s\n", info.error, info.time.Nanoseconds()/1e6, info.ip)
		}
	}

	duration := time.Since(start)
	fmt.Printf("Total time: %s, found: %d\n", duration, count)
}
