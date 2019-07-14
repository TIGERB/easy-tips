package tingle

// BeforeStartupHandler 前置**启动**handler
// 可以定义一些前置(同步或异步任务或同步+异步)
// 比如异步更新内存缓存
type BeforeStartupHandler func()

// BeforeRequestHandler 前置请求handler
// 可以定义一些接口请求的前置逻辑(同步或异步任务或同步+异步)
// 比如校验用户是否登陆逻辑
type BeforeRequestHandler func()

// AfterRequestHandler 后置请求handler
// 可以定义一些接口请求的后置逻辑(同步或异步任务或同步+异步) 比如对一致性要求不高的 异步刷新缓存到db
type AfterRequestHandler func()

// UserHandler 用户handle
type UserHandler func(*Context)

// tree 路由树
type tree struct {
	Method      string
	Path        string
	UserHandles []*UserHandler
}

// Router 路由结构体
type Router struct {
	Trees                map[string]*tree
	BeforeStartupHandles []BeforeStartupHandler
	BeforeRequestHandles []BeforeRequestHandler
	AfterRequestHandles  []AfterRequestHandler
}

// Add 绑定路由
func (router *Router) Add(method string, path string, handles []*UserHandler) {
	router.Trees[method+"-"+path] = &tree{
		Method:      method,
		Path:        path,
		UserHandles: handles,
	}
}
