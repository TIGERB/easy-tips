package main

import (
	// 导入net/http包
	"net/http"
)

func main() {
	// ------------------ 使用http包启动一个http服务 方式一 ------------------
	// *http.Request http请求内容实例的指针
	// http.ResponseWriter 写http响应内容的实例
	http.HandleFunc("/v1/demo", func(w http.ResponseWriter, r *http.Request) {
		// 写入响应内容
		w.Write([]byte("Hello TIGERB !\n"))
	})
	// 启动一个http服务并监听8888端口 这里第二个参数可以指定handler
	http.ListenAndServe(":6060", nil)
}
