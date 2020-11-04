# iris源码阅读

![](http://cdn.tigerb.cn/20190704211456.png)

```
package main

import "github.com/kataras/iris"

func main() {
	app := iris.Default()
	app.Get("/ping", func(ctx iris.Context) {
		ctx.JSON(iris.Map{
			"message": "pong",
		})
	})
	app.Run(iris.Addr(":8888"))
}

```

```
// Application
type Application struct {
    *router.APIBuilder
    *router.Router
    ContextPool    *context.Pool
    config    *Configuration
    logger    *golog.Logger
    view    view.View
    once    sync.Once
    mu    sync.Mutex
    Hosts            []*host.Supervisor
    hostConfigurators    []host.Configurator
}

// 创建了一个iris应用实例 
// 为什么不直接New呢？
// 因为Default里面注册了两个handle 
// 1. recover panic的方法，
// 2. 请求日志
app := iris.Default()

func Default() *Application {
	app := New()
    // 合成复用*APIBuilder的Use
	app.Use(recover.New())
    // 合成复用*APIBuilder的Use
	app.Use(requestLogger.New())

	return app
}

// api.middleware
func (api *APIBuilder) Use(handlers ...context.Handler) {
	api.middleware = append(api.middleware, handlers...)
}

// app
app := &Application{
    config:     &config,
    logger:     golog.Default,
    APIBuilder: router.NewAPIBuilder(),
    Router:     router.NewRouter(),
}

// APIBuilder
api := &APIBuilder{
    macros:            macro.Defaults,
    errorCodeHandlers: defaultErrorCodeHandlers(),
    reporter:          errors.NewReporter(),
    relativePath:      "/",
    routes:            new(repository),
}

// repository
type repository struct {
	routes []*Route
}

---

//router
func (api *APIBuilder) Get(relativePath string, handlers ...context.Handler) *Route {
	return api.Handle(http.MethodGet, relativePath, handlers...)
}

route := &Route{
    Name:            defaultName,
    Method:          method,
    methodBckp:      method,
    Subdomain:       subdomain,
    tmpl:            tmpl,
    Path:            path,
    Handlers:        handlers,
    MainHandlerName: mainHandlerName,
    FormattedPath:   formattedPath,
}

---

// Server
app.Run(iris.Addr(":8888"))

app.Router.BuildRouter(app.ContextPool, routerHandler, app.APIBuilder, false)

// iris.Addr(":8888")
func Addr(addr string, hostConfigs ...host.Configurator) Runner {
	return func(app *Application) error {
		return app.NewHost(&http.Server{Addr: addr}).
			Configure(hostConfigs...).
			ListenAndServe()
	}
}

// app.NewHost(&http.Server{Addr: addr})
if srv.Handler == nil {
    srv.Handler = app.Router
}

type Router struct {
	mu sync.Mutex // for Downgrade, WrapRouter & BuildRouter,
	// not indeed but we don't to risk its usage by third-parties.
	requestHandler RequestHandler   // build-accessible, can be changed to define a custom router or proxy, used on RefreshRouter too.
	mainHandler    http.HandlerFunc // init-accessible
	wrapperFunc    func(http.ResponseWriter, *http.Request, http.HandlerFunc)

	cPool          *context.Pool // used on RefreshRouter
	routesProvider RoutesProvider
}

// implement ServeHTTP
func (router *Router) ServeHTTP(w http.ResponseWriter, r *http.Request) {
	router.mainHandler(w, r)
}

// router.mainHandler(w, r)

// mainHandler    http.HandlerFunc // init-accessible

// the important
router.mainHandler = func(w http.ResponseWriter, r *http.Request) {
    // 构建请求上下文
    ctx := cPool.Acquire(w, r)
    // 处理请求
    router.requestHandler.HandleRequest(ctx)
    // 释放请求上下文
    cPool.Release(ctx)
}

func (h *routerHandler) HandleRequest(ctx context.Context)

---

// route

```

--------------

```go

// ---------------------- 应用模型 ----------------------

// App
type App struct {
    router EasyRouters
    loger EasyLoger
    server &http.Server{}
}

// ---------------------- 路由模型 ----------------------

// PreStartupHandler 前置**启动**handler 
// 可以定义一些前置(同步或异步任务或同步+异步) 
// 比如异步更新内存缓存
type PreStartupHandler func()

// PreRequestHandler 前置**请求**handler 
// 可以定义一些接口请求的前置逻辑(同步或异步任务或同步+异步) 
// 比如校验用户是否登陆逻辑
type PreRequestHandler func()

// AfterRequestHandler 
// 后置**请求**handler 
// 可以定义一些接口请求的后置逻辑(同步或异步任务或同步+异步) 比如对一致性要求不高的 异步刷新缓存到db
type AfterRequestHandler func()

type BusinessHandler func()

// EasyRouter 路由结构体
type EasyRouter struct  {
    Method string
    Path string
    PreStartupHandles   []PreStartupHandler
    PreRequestHandles   []PreRequestHandler
    AfterRequestHandles []AfterRequestHandler
    BusinessHandle BusinessHandler
}

// EasyRouters 路由列表
EasyRouters []EasyRouter

// ---------------------- 日志模型 ----------------------

// EasyLoger
type EasyLoger struct {

}
```