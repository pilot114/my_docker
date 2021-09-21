package main

import (
	"flag"
	"log"
	"fmt"
	"os"
	"net/http"
	"io/ioutil"
	"net/url"
	"os/signal"
	"time"
	"bytes"

	"github.com/gin-gonic/gin"
	"github.com/gorilla/websocket"
)

var (
	argv struct {
		host string
		key  string
		help bool
	}
	upgrader = websocket.Upgrader{
    ReadBufferSize:  1024,
    WriteBufferSize: 1024,
    CheckOrigin: func(r *http.Request) bool {
	        return true
	    },
	}
)

func CORSMiddleware(c *gin.Context) {
    c.Writer.Header().Set("Access-Control-Allow-Origin", "*")
    c.Writer.Header().Set("Access-Control-Allow-Credentials", "true")
    c.Writer.Header().Set("Access-Control-Allow-Headers", "Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization, accept, origin, Cache-Control, X-Requested-With")
    c.Writer.Header().Set("Access-Control-Allow-Methods", "POST, OPTIONS, GET, PUT")

    if c.Request.Method == "OPTIONS" {
      c.AbortWithStatus(204)
      return
    }
    c.Next()
}

func processArgs() (needStop bool) {
	needStop = true

	if argv.help {
		flag.Usage()
	} else if len(argv.host) == 0 {
		fmt.Fprintln(os.Stderr, "Hey! -host is required")
		flag.Usage()
	} else if len(argv.key) == 0 {
		fmt.Fprintln(os.Stderr, "Hey! -key is required")
		flag.Usage()
	} else {
		needStop = false
	}

	return
}

func init() {
	flag.StringVar(&argv.host, `host`, ``, `streaming api host. REQUIRED`)
	flag.StringVar(&argv.key, `key`, ``, `client key. REQUIRED`)
	flag.BoolVar(&argv.help, `h`, false, `show this help`)

	flag.Parse()
}

func main() {
	if processArgs() {
		return
	}

	vkToMyChannel := make(chan []byte)

	http.HandleFunc("/stream", func(w http.ResponseWriter, r *http.Request) {
		wsClient, err := upgrader.Upgrade(w, r, nil)
		if err != nil {
			log.Print("upgrade:", err)
			return
		}
		defer wsClient.Close()
		for {
			message := <- vkToMyChannel

			err = wsClient.WriteMessage(websocket.TextMessage, message)
			if err != nil {
				log.Println("write error:", err)
				break
			}
		}
	})
	
	// ws server
	go func() {
		log.Fatal(http.ListenAndServe("0.0.0.0:8888", nil))
	}()

	router := gin.Default()
	router.Use(CORSMiddleware)

	// получить список правил
	router.GET("/rule", func(c *gin.Context) {
        url := fmt.Sprintf("https://%s/rules/?key=%s", argv.host, argv.key)
        req, err := http.NewRequest("GET", url, nil)

        client := &http.Client{}
        resp, err := client.Do(req)
        if err != nil {
            log.Fatal("http request error:", err)
        }
        defer resp.Body.Close()

        bodyBuf, err := ioutil.ReadAll(resp.Body)
        if err != nil {
            log.Fatal("response body read error:", err)
        }

		c.JSON(200, gin.H{"result": string(bodyBuf)})
	})

	// создать или удалить правило
	// GET параметры:
	// tag, rule
	router.POST("/rule", func(c *gin.Context) {
    	get := c.Request.URL.Query()

    	url := fmt.Sprintf("https://%s/rules/?key=%s", argv.host, argv.key)

        if get["rule"] != nil {
        	json := `{"rule":{"value":"` + get["rule"][0] + `","tag":"` + get["tag"][0] + `"}}`
        	req, err := http.NewRequest("POST", url, bytes.NewBuffer([]byte(json)))
        	req.Header.Set("Content-Type", "application/json")

            client := &http.Client{}
            resp, err := client.Do(req)
            if err != nil {
                log.Fatal("http request error:", err)
            }
            defer resp.Body.Close()

            bodyBuf, err := ioutil.ReadAll(resp.Body)
            if err != nil {
                log.Fatal("response body read error:", err)
            }

            c.JSON(200, gin.H{"result": string(bodyBuf)})
        } else {
            json := `{"tag":"` + get["tag"][0] + `"}`
            req, err := http.NewRequest("DELETE", url, bytes.NewBuffer([]byte(json)))
        	req.Header.Set("Content-Type", "application/json")

            client := &http.Client{}
            resp, err := client.Do(req)
            if err != nil {
                log.Fatal("http request error:", err)
            }
            defer resp.Body.Close()

            bodyBuf, err := ioutil.ReadAll(resp.Body)
            if err != nil {
                log.Fatal("response body read error:", err)
            }

            c.JSON(200, gin.H{"result": string(bodyBuf)})
        }
	})
	go func() {
		router.Run(":8889")
	}()

	// VK ws connect
	u := url.URL{Scheme: "wss", Host: argv.host, Path: "/stream/", RawQuery: "key=" + argv.key}
	log.Printf("connecting to VK: %s\n", u.String())
	vkwsClient, wsResp, err := websocket.DefaultDialer.Dial(u.String(), nil)
	if err != nil {
		if err == websocket.ErrBadHandshake {
			log.Printf("handshake failed with status %d\n", wsResp.StatusCode)
			bodyBuf, _ := ioutil.ReadAll(wsResp.Body)
			log.Println("respBody:", string(bodyBuf))
		}
		log.Fatal("dial error:", err)
	}
	log.Println("VK: connection established")
	defer vkwsClient.Close()

	done := make(chan struct{})
	defer close(done)

	go func() {
		for {
			_, message, err := vkwsClient.ReadMessage()
			if err != nil {
				log.Println("read error:", err)
				done <- struct{}{}
				return
			}
			vkToMyChannel <- message
		}
	}()

	interrupt := make(chan os.Signal, 1)
	signal.Notify(interrupt, os.Interrupt)
	select {
		case <-interrupt:
			log.Println("interrupt")
			err := vkwsClient.WriteMessage(websocket.CloseMessage, websocket.FormatCloseMessage(websocket.CloseNormalClosure, ""))
			if err != nil {
				log.Println("write close error: ", err)
				return
			}
			select {
			case <-done:
			case <-time.After(time.Second):
			}
		case <-done:
	}
}
