```go
// ---------------------- 应用模型 ----------------------

// App
type Tingle struct {
    router Router
    logger Logger
    server *http.Server
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

// Router 路由结构体
type Router struct  {
    trees []*tree
    BeforeStartupHandles   []BeforeStartupHandler
    BeforeRequestHandles   []BeforeRequestHandler
    AfterRequestHandles []AfterRequestHandler
}

type tree struct {
    Method string
    Path string
    BusinessHandle BusinessHandler
}

// Routers 路由列表
Routers []Router

// ---------------------- 上线文模型 ----------------------

type Context struct{
    Request *http.Request
    Response http.ResponseWriter
}

// ---------------------- 日志模型 ----------------------

// Logger
type Logger struct {

}
```