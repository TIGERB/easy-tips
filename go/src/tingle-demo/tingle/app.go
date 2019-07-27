package tingle

import (
	"net/http"
	"strings"
	"sync"
)

const (
	// DefalutPort 默认端口
	DefalutPort = "8088"
)

// Tingle Golang Framework
// 名称的灵感来自于《蜘蛛侠》中的 “peter tingle”
type Tingle struct {
	router      *Router
	logger      *Logger
	server      *http.Server
	contextPool *sync.Pool
	middlewares []Handler
}

// Handle 注册用户路由请求
// method http method
// path http path
// handle UserHandler
func (tingle *Tingle) Handle(method string, path string, handles ...*UserHandler) {
	tingle.router.Add(method, path, handles)
}

// RegisterMW 注册中间件
func (tingle *Tingle) RegisterMW(handlers ...Handler) {
	tingle.middlewares = append(tingle.middlewares, handlers...)
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
	context := tingle.contextPool.Get().(*Context)
	context.Request = r
	context.Response = w
	tingle.handleHTTPRequest(context)
}

// handleHTTPRequest
func (tingle *Tingle) handleHTTPRequest(context *Context) {
	key := strings.ToLower(context.Request.Method) + "-" + context.Request.URL.Path
	tree, ok := tingle.router.Trees[key]
	if !ok {
		context.Response.WriteHeader(404)
	}

	// 执行中间件
	var nullHandler Handler
	if len(tingle.middlewares) == 0 {
		panic("middlewares is empty")
	}
	for k, handler := range tingle.middlewares {
		if k == 0 {
			nullHandler = handler
			continue
		}
		tingle.middlewares[k-1].SetNext(handler)
	}
	nullHandler.Run(&Context{})

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

// NewWithDefaultMW 创建Tingle框架实例并注册默认的中间件
// 1. 默认注册goroutine panic recover中间件
// 2. 默认注册请求访问日志(access log)中间件
func NewWithDefaultMW() *Tingle {
	t := New()
	t.RegisterMW(
		&NullHandler{},
		&RecoverHandler{},
		&AccessLogHandler{})
	return t
}
