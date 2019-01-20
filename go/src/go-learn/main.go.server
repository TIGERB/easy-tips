package main

// 导入net/http包
import (
	"net/http"
)

// DemoHandle http handle示例
type DemoHandle struct {
}

// ServeHTTP 匹配到路由后执行的方法
func (DemoHandle) ServeHTTP(w http.ResponseWriter, r *http.Request) {
	w.Write([]byte("Hello TIGERB !\n"))
}

func Server() {
	// // ------------------ 使用http包启动一个http服务 方式一 ------------------
	// // *http.Request http请求内容实例的指针
	// // http.ResponseWriter 写http响应内容的实例
	// http.HandleFunc("/v1/demo", func(w http.ResponseWriter, r *http.Request) {
	// 	// 写入响应内容
	// 	w.Write([]byte("Hello TIGERB !\n"))
	// })
	// // 启动一个http服务并监听8888端口
	// http.ListenAndServe(":8888", nil)

	// ------------------ 使用http包的Server启动一个http服务 方式二 ------------------
	// 初始化一个http.Server
	server := &http.Server{}
	// // 初始化handler并赋值给server.Handler
	server.Handler = DemoHandle{}
	// // 绑定地址
	server.Addr = ":8888"

	// // 启动一个http服务
	server.ListenAndServe()
}

// 测试我们的服务
// --------------------
// 启动：bee run
// 访问： curl "http://127.0.0.1:8888/v1/demo"
// 响应结果：Hello TIGERB !
