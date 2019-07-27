package main

import (
	"tingle-demo/tingle"
)

func main() {
	var ping tingle.UserHandler
	ping = func(c *tingle.Context) {
		c.JSON("hello tingle!")
	}
	t := tingle.NewWithDefaultMW()
	t.Handle("get", "/ping", &ping)
	t.Run(":8088")
}
