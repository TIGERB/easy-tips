package main

import (
	"flow/app/user"
	"net/http"
)

var GlobalAPP *App

func main() {

	// 初始化app
	GlobalAPP := &App{
		Server: http.DefaultServeMux,
	}

	http.Handle("/user", user.DemoHandle{})
	http.ListenAndServe(":7000", nil)
}
