package main

import (
	_ "beego-code-read/routers"
	"fmt"
	"net/http"
	"net/http/pprof"

	"github.com/astaxie/beego"
)

func main() {
	go pprofMoniter()
	beego.Run()
}

func pprofMoniter() {
	http.HandleFunc("/debug/pprof/block", pprof.Index)
	http.HandleFunc("/debug/pprof/goroutine", pprof.Index)
	http.HandleFunc("/debug/pprof/head", pprof.Index)
	http.HandleFunc("/debug/pprof/threadcreate", pprof.Index)

	fmt.Println(http.ListenAndServe("0.0.0.0:9999", nil))
}
