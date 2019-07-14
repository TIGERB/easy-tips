package main

import (
	"fmt"
	"tingle-demo/tingle"
)

func main() {
	var ping tingle.UserHandler
	ping = func(c *tingle.Context) {
		c.JSON("hello tingle!")
	}
	t := tingle.New()
	t.Handle("get", "/ping", &ping)
	t.Run(":6666")
	fmt.Println("aaa")
}
