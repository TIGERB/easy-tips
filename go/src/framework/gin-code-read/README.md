# iris源码阅读

![](http://cdn.tigerb.cn/20190704211526.png)

```go
package main

import "github.com/gin-gonic/gin"

func main() {
	r := gin.Default()
	r.GET("ping", func(c *gin.Context) {
		c.JSON(200, gin.H{"message": "pong"})
	})
	r.Run()
}
```

```go
gin.Default()

func Default() *Engine {
	debugPrintWARNINGDefault()
	engine := New()
	engine.Use(Logger(), Recovery())
	return engine
}

func New() *Engine {
	debugPrintWARNINGNew()
	engine := &Engine{
		RouterGroup: RouterGroup{
			Handlers: nil,
			basePath: "/",
			root:     true,
		},
		FuncMap:                template.FuncMap{},
		RedirectTrailingSlash:  true,
		RedirectFixedPath:      false,
		HandleMethodNotAllowed: false,
		ForwardedByClientIP:    true,
		AppEngine:              defaultAppEngine,
		UseRawPath:             false,
		UnescapePathValues:     true,
		MaxMultipartMemory:     defaultMultipartMemory,
		trees:                  make(methodTrees, 0, 9),
		delims:                 render.Delims{Left: "{{", Right: "}}"},
		secureJsonPrefix:       "while(1);",
	}
	engine.RouterGroup.engine = engine
	engine.pool.New = func() interface{} {
		return engine.allocateContext()
	}
	return engine
}

engine.Use(Logger(), Recovery())

func (engine *Engine) Use(middleware ...HandlerFunc) IRoutes {
	engine.RouterGroup.Use(middleware...)
	engine.rebuild404Handlers()
	engine.rebuild405Handlers()
	return engine
}

// --------------router--------------

func (group *RouterGroup) GET(relativePath string, handlers ...HandlerFunc) IRoutes {
	return group.handle("GET", relativePath, handlers)
}

func (group *RouterGroup) handle(httpMethod, relativePath string, handlers HandlersChain) IRoutes {
	absolutePath := group.calculateAbsolutePath(relativePath)
	handlers = group.combineHandlers(handlers)
	group.engine.addRoute(httpMethod, absolutePath, handlers)
	return group.returnObj()
}

func (engine *Engine) addRoute(method, path string, handlers HandlersChain) {
	assert1(path[0] == '/', "path must begin with '/'")
	assert1(method != "", "HTTP method can not be empty")
	assert1(len(handlers) > 0, "there must be at least one handler")

	debugPrintRoute(method, path, handlers)
	root := engine.trees.get(method)
	if root == nil {
		root = new(node)
		engine.trees = append(engine.trees, methodTree{method: method, root: root})
	}
	root.addRoute(path, handlers)
}

type node struct {
	path      string
	indices   string
	children  []*node
	handlers  HandlersChain
	priority  uint32
	nType     nodeType
	maxParams uint8
	wildChild bool
}

// --------------http server--------------
func (engine *Engine) Run(addr ...string) (err error) {
	defer func() { debugPrintError(err) }()

	address := resolveAddress(addr)
	debugPrint("Listening and serving HTTP on %s\n", address)
	err = http.ListenAndServe(address, engine)
	return
}

// err = http.ListenAndServe(address, engine)

// engine自身就实现了Handler接口
type Handler interface {
	ServeHTTP(ResponseWriter, *Request)
}

// ServeHTTP conforms to the http.Handler interface.
func (engine *Engine) ServeHTTP(w http.ResponseWriter, req *http.Request) {
	c := engine.pool.Get().(*Context)
	c.writermem.reset(w)
	c.Request = req
	c.reset()

	engine.handleHTTPRequest(c)

	engine.pool.Put(c)
}

// ln, err := net.Listen("tcp", addr)

// srv.Serve(tcpKeepAliveListener{ln.(*net.TCPListener)})

// rw, e := l.Accept()

// go c.serve(ctx)

// --------------handle request--------------
engine.handleHTTPRequest(c)

t := engine.trees
for i, tl := 0, len(t); i < tl; i++ {
	if t[i].method != httpMethod {
		continue
	}
	root := t[i].root
	// Find route in tree
	handlers, params, tsr := root.getValue(path, c.Params, unescape)
	if handlers != nil {
		c.handlers = handlers
		c.Params = params
		c.Next()
		c.writermem.WriteHeaderNow()
		return
	}
	...
}

type methodTree struct {
	method string
	root   *node
}

type methodTrees []methodTree

func (c *Context) Next() {
	c.index++
	for s := int8(len(c.handlers)); c.index < s; c.index++ {
		c.handlers[c.index](c)
	}
}
```

```
r := gin.Default()
⬇️
engine := New()
⬇️
engine.Use(Logger(), Recovery())
⬇️
func (group *RouterGroup) GET(relativePath string, handlers ...HandlerFunc) IRoutes
️️️⬇️
group.engine.addRoute(httpMethod, absolutePath, handlers)
⬇️
root := engine.trees.get(method) || root = new(node) engine.trees = append(engine.trees, methodTree{method: method, root: root})
⬇️
root.addRoute(path, handlers)
⬇️
r.Run()
⬇️
http.ListenAndServe(address, engine)
⬇️
func (engine *Engine) ServeHTTP(w http.ResponseWriter, req *http.Request)
⬇️
️️engine.handleHTTPRequest(c)
⬇️
handlers, params, tsr := root.getValue(path, c.Params, unescape)
⬇️
c.handlers = handlers
c.Params = params
c.Next()
⬇️
func (c *Context) Next()
⬇️
c.handlers[c.index](c)
```

--------------
