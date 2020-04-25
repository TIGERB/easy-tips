package main

import (
	"fmt"
	"net"
)

// GODEBUG=scheddetail=1,schedtrace=1000 ./go-learn
//
//
//
//
//
//
//
//
//
//
func main() {
	// fmt.Println("runtime.NumCPU()", runtime.NumCPU())
	// runtime.GOMAXPROCS(runtime.NumCPU())

	// wg := &sync.WaitGroup{}
	// numbers := 10
	// wg.Add(numbers)
	// for range make([]byte, numbers) {
	// 	go func(wg *sync.WaitGroup) {
	// 		var counter int
	// 		for i := 0; i < 1e10; i++ {
	// 			counter++
	// 		}
	// 		wg.Done()
	// 	}(wg)
	// }

	// wg.Wait()
	l, _ := net.Listen("tcp", ":9999")
	for {
		c, _ := l.Accept()
		if c == nil {
			continue
		}
		fmt.Println("c.RemoteAddr()", c.RemoteAddr())
		go func(c net.Conn) {
			defer c.Close()
			var body []byte
			for {
				_, err := c.Read(body)
				if err != nil {
					break
				}
				fmt.Println("body", string(body))
				c.Write(body)
				break
			}
		}(c)
	}
}
