# beego框架代码分析

## 前言

也许beego框架在国内应该是众多PHPer转go的首选，因为beego的MVC、ORM、完善的中文文档让PHPer们得心应手，毫无疑问我也是。这种感觉就像当年入门PHP时使用ThinkPHP一样。

也许随着你的认知的提升，你会讨厌现在东西，比如某一天你可能慢慢的开始讨厌beego，你会发现go语言里**包**的真正意义，你开始反思MVC真的适合go吗，或者你开始觉着ORM在静态语言里的鸡肋，等等。我只想说：“也许你成长了～”。但是这些都不重要，每一个受欢迎的事物自然有我们值的学习的地方。今天这篇文章很简单，像一篇笔记，记录了我这几天抽空读beego源码的记录。

> 如何读一个框架？

毫无疑问读go的框架和PHP框架也是一样的：

1. 配置加载：如何加载配置文件的。
2. **路由**：分析框架如何通过URI执行对应业务的。
3. ORM：ORM如何实现的。

这里（1.）和（3.）无非就是加载个文件和sql解析器的实现，我就忽略了，重点就看看路由的实现。

## 安装

简单带过：

```
// Step1: 安装beego
go get github.com/astaxie/beego

// Step2: 安装bee
go get github.com/beego/bee

// Step3: 用bee工具创建一个新的项目
bee new beego-code-read
```

## 代码分析

go有自己实现的http包，大多go框架也是基于这个http包，所以看beego之前我们先补充或者复习下这个知识点。如下：

### go如何启动一个http server

```go
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
	http.ListenAndServe(":8888", nil)
}

// 测试我们的服务
// --------------------
// 启动：bee run
// 访问： curl "http://127.0.0.1:8888/v1/demo"
// 响应结果：Hello TIGERB !
```

ListenAndServe是对http.Server的进一步封装，除了上面的方式，还可以使用http.Server直接启服务，这个需要设置Handler，这个Handler要实现Server.Handler这个接口。当请求来了会执行这个Handler的`ServeHTTP`方法，如下：

```go
package main

// 导入net/http包
import (
	"net/http"
)

// DemoHandle server handle示例
type DemoHandle struct {
}

// ServeHTTP 匹配到路由后执行的方法
func (DemoHandle) ServeHTTP(w http.ResponseWriter, r *http.Request) {
	w.Write([]byte("Hello TIGERB !\n"))
}

func main() {
	// ------------------ 使用http包的Server启动一个http服务 方式二 ------------------
	// 初始化一个http.Server
	server := &http.Server{}
	// 初始化handler并赋值给server.Handler
	server.Handler = DemoHandle{}
	// 绑定地址
	server.Addr = ":8888"

	// 启动一个http服务
	server.ListenAndServe()

}

// 测试我们的服务
// --------------------
// 启动：bee run
// 访问： curl "http://127.0.0.1:8888/v1/demo"
// 响应结果：Hello TIGERB !
```

### beego路由分析

接下里我们开始看beego的代码。拿访问`"http://127.0.0.1:8080/"`来说，对于beego代码来说有三个关键点，分别如下：

1. 启动：main.go -> `beego.Run()`

2. 注册路由：routers\router.go -> `beego.Router("/", &controllers.MainController{})`

3. 控制器：controllers\default.go -> `Get()`

下面来看3个关键点的详细分析：

#### beego.Run()主要的工作

```go
// github.com/astaxie/beego/beego.go
func Run(params ...string) {
	// 启动http服务之前的一些初始化 忽略 往下看
	initBeforeHTTPRun()

	// http服务的ip&port设置
	if len(params) > 0 && params[0] != "" {
		strs := strings.Split(params[0], ":")
		if len(strs) > 0 && strs[0] != "" {
			BConfig.Listen.HTTPAddr = strs[0]
		}
		if len(strs) > 1 && strs[1] != "" {
			BConfig.Listen.HTTPPort, _ = strconv.Atoi(strs[1])
		}
	}

	// 又一个run 往下看
	BeeApp.Run()
}
```

```go
// github.com/astaxie/beego/app.go
func (app *App) Run(mws ...MiddleWare) {
	// ... 省略 

	// 看了下这里app.Server的类型就是*http.Server 也就是说用的原生http包 且是上面“go如何启动一个http server”中的第二种方式
	app.Server.Handler = app.Handlers

	// ... 省略

	if BConfig.Listen.EnableHTTP {
		go func() {
			app.Server.Addr = addr
			logs.Info("http server Running on http://%s", app.Server.Addr)

			// 默认配置false不强制tcp4
			if BConfig.Listen.ListenTCP4 {
				//...
				// 忽略 默认false
			} else {
				// 关键点 ListenAndServe: app.Server的类型就是*http.Server 所以这里就启动了http服务 
				if err := app.Server.ListenAndServe(); err != nil {
					logs.Critical("ListenAndServe: ", err)
					time.Sleep(100 * time.Microsecond)
					endRunning <- true
				}
			}
		}()
	}
	// 阻塞到服务启动
	<-endRunning
}

// 看到这里http已经启动了 而且是注册Handler的方式
```

接着去找这个Handler的ServeHTTP方法，通过上面的这段代码`app.Server.Handler = app.Handlers`，我们找到了下面的定义，Handler即是`ControllerRegister`的值，所以每次亲求来了就会去执行`ControllerRegister.ServeHTTP(rw http.ResponseWriter, r *http.Request)`。

```go
// src/github.com/astaxie/beego/app.go
func init() {
	// 调用 创建beego框架实例的方法
	BeeApp = NewApp()
}

// App结构体
type App struct {
	// 关键的请求回调Handler
	Handlers *ControllerRegister
	// http包的服务
	Server   *http.Server
}

func NewApp() *App {
	// 初始化http handler
	cr := NewControllerRegister()
	// 创建beego 实例
	app := &App{Handlers: cr, Server: &http.Server{}}
	return app
}

```



通过我们追`beego.Run()`的代码，目前我们得到的结论就是：

1. 使用的http包启动的服务
2. 没有使用`http.HandleFun()`的定义路由策略，而是注册Handler的方式

所以beego就是通过`beego.Router()`自己管理路由，如果http请求来了，回调`ControllerRegister.ServeHTTP(rw http.ResponseWriter, r *http.Request)`方法，在`ControllerRegister.ServeHTTP(rw http.ResponseWriter, r *http.Request)`方法中去匹配路由并执行对应的controller 也就是beego`ControllerInterface`类型的控制器的方法，比如RESTFUL或者自定义啊等。

#### beego.Router() 如何注册路由

首先路由文件是如何加载的，我们发现在`main.go`文件里导入了路由包：

```go
package main

import (
	// 导入routers包 只执行init方法
	_ "beego-code-read/routers"

	"github.com/astaxie/beego"
)

func main() {
	beego.Run()
}
```

上面我们启动了http服务，接着关键就是`beego.Router()`如何注册路由了。追了下代码如下：

```
beego.Router() 
-> BeeApp.Handlers.Add(rootpath, c, mappingMethods...) 
-> ControllerRegister.addWithMethodParams(pattern, c, nil, mappingMethods...) 
-> ControllerRegister.addToRouter(method, pattern string, r *ControllerInfo) 
-> *Tree.AddRouter(pattern string, runObject interface{})
```

最后就是在`*Tree.AddRouter()`完成了路由注册，这里的代码逻辑暂时就先不看了，至此这个beego框架的流程就其本理顺了，最后我们在回头总结下整个流程如下图：

> 备注：go导入包相当于入栈过程，先import后执行init

![http://cdn.tigerb.cn/beego_2.png](http://cdn.tigerb.cn/beego_2.png)

