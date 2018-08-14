package main

import (
	"fmt"
	"net"
)

// func demo(w http.ResponseWriter, r *http.Request) {
// 	fmt.Println("Hello World!")
// 	fmt.Fprintf(w, "Hello World!")
// }

func main() {
	// http.HandleFunc("/", demo)
	// http.ListenAndServe("127.0.0.1:8879", nil)

	// 创建一个socket

	// 绑定ip/port
	addr, _ := net.ResolveTCPAddr("tcp4", ":8989")

	// 监听port
	listener, err := net.ListenTCP("tcp", addr)

	if err != nil {
		fmt.Println("err: " + err.Error())
		return
	}

	for {
		c, err := listener.Accept()
		if err != nil {
			fmt.Println("err: " + err.Error())
			continue
		}
		go connHandle(c)

		// err = c.Close()
		// c.Write([]byte("HTTP/1.1 200 OK\r\n"))
	}

	// 接收(accept)一个客户端socket请求

	// 获取请求内容

	// 写入响应内容
}

func connHandle(c net.Conn) {
	defer c.Close()
	buf := make([]byte, 1024)
	for {
		n, err := c.Read(buf)
		fmt.Println(n, err)
		if n == 0 || err != nil {
			break
		}
		c.Write([]byte("HTTP/1.1 200 OK\r\n\r\n"))
		// c.Write(buf[:])
		// break
	}
}
