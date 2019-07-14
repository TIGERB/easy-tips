package tingle

import (
	"fmt"
	"net/http"
	"sync"
)

const (
	// DefalutPort 默认端口
	DefalutPort = "6666"
)

// Tingle Golang Framework
// 名称的灵感来自于《蜘蛛侠》中的 “peter tingle”
type Tingle struct {
	router      *Router
	logger      *Logger
	server      *http.Server
	contextPool *sync.Pool
}

// Handle 注册用户路由请求
// method http method
// path http path
// handle UserHandler
func (tingle *Tingle) Handle(method string, path string, handles ...*UserHandler) {
	tingle.router.Add(method, path, handles)
}

// Run 启动框架
func (tingle *Tingle) Run(addr string) {
	if addr == "" {
		addr = DefalutPort
	}
	tingle.server.Addr = addr
	tingle.server.Handler = tingle
	tingle.server.ListenAndServe()
}

// ServeHTTP 实现http.handler接口
func (tingle *Tingle) ServeHTTP(w http.ResponseWriter, r *http.Request) {
	fmt.Println("1")
	context := tingle.contextPool.Get().(*Context)
	context.Request = r
	context.Response = w
	tingle.handleHTTPRequest(context)
}

// handleHTTPRequest
func (tingle *Tingle) handleHTTPRequest(context *Context) {
	tree, ok := tingle.router.Trees[context.Request.Method+"-"+context.Request.URL.Path]
	fmt.Println(*tree)
	if !ok {
		context.Response.Header().Set("Status Code", "404 Not Found")
	}
	for _, h := range tree.UserHandles {
		(*h)(context)
	}
}

// New 创建Tingle框架实例
func New() *Tingle {
	t := &Tingle{
		router: &Router{
			Trees: make(map[string]*tree),
		},
		logger:      new(Logger),
		server:      &http.Server{},
		contextPool: new(sync.Pool),
	}
	t.contextPool.New = func() interface{} {
		return new(Context)
	}
	return t
}
