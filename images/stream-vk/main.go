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

// 	"github.com/gin-gonic/gin"
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

// 	router := gin.Default()
// 	router.LoadHTMLFiles("index.html")
//
// 	router.GET("/", func(c *gin.Context) {
// 		c.HTML(http.StatusOK, "index.html", gin.H{
// 			"title": "VK steaming API",
// 			"wsHost": "ws://127.0.0.1:8888/stream",
// 		})
// 	})
//
// 	router.GET("/rule", func(c *gin.Context) {
// 		c.JSON(200, gin.H{
// 			"message": "GET",
// 		})
// 	})
// 	router.POST("/rule", func(c *gin.Context) {
// 		c.JSON(200, gin.H{
// 			"message": "POST",
// 		})
// 	})
// 	router.DELETE("/rule", func(c *gin.Context) {
// 		c.JSON(200, gin.H{
// 			"message": "DELETE",
// 		})
// 	})
// 	go func() {
// 		router.Run() // listen and serve on 0.0.0.0:8080
// 	}()

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
