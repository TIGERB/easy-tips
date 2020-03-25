我的代码没有`else`系列-简单工厂

结合实际业务谈设计模式

业务场景

```
调用一个服务生成静态页面
不同的页面拥有不同的模块
```

不同的页面的数据结构不一样
生成不同的页面对象

正常代码
```go
package main

import "fmt"

const (
	// CartConst 购物车列表页面
	CartConst = "cart/list"
	// ProductConst 商品列表页面
	ProductConst = "product/list"
)

// Context 请求上下文
type Context struct {
	URI string
}

// PageInterface PageInterface
type PageInterface interface {
	MakeData(c *Context) (interface{}, error)
}

// Cart 购物车页面数据对象
type Cart struct {
	header      interface{}
	
	footer      interface{}
}

// MakeData 构建数据对象
func (Cart *Cart) MakeData(c *Context) (interface{}, error) {
	// 构建数据的页面代码...
	fmt.Println("生成购物车静态页面数据对象...")
	return Cart, nil
}

// Product Spu页面数据对象
type Product struct {
	header  interface{}
	
	footer  interface{}
}

// MakeData 构建数据对象
func (Product *Product) MakeData(c *Context) (interface{}, error) {
	// 构建数据的页面代码...
	fmt.Println("生成spu详情静态页面数据对象...")
	return Product, nil
}

func main() {
	c := &Context{
		URI: "cart/list",
	}
	var pageObject PageInterface
	switch c.URI {
	case CartConst:
		pageObject = &Cart{}
	case ProductConst:
		pageObject = &Product{}

	default:
		panic("不支持的页面")
	}

	pageObject.MakeData(c)
}

```

简单工厂模式的概念
简单理解，一句话：
> 统一封装生产对象的过程

简单工厂模式下的代码
```go
package main

import "fmt"

const (
	// CartConst 购物车列表页面
	CartConst = "cart/list"
	// ProductConst 商品列表页面
	ProductConst = "product/list"
)

// Context 请求上下文
type Context struct {
	URI string
}

// PageInterface PageInterface
type PageInterface interface {
	MakeData(c *Context) (interface{}, error)
}

// Cart 购物车页面数据对象
type Cart struct {
	header      interface{}
	
	footer      interface{}
}

// MakeData 构建数据对象
func (Cart *Cart) MakeData(c *Context) (interface{}, error) {
	// 构建数据的页面代码...
	fmt.Println("生成购物车静态页面数据对象...")
	return Cart, nil
}

// Product Spu页面数据对象
type Product struct {
	header  interface{}
	
	footer  interface{}
}

// MakeData 构建数据对象
func (Product *Product) MakeData(c *Context) (interface{}, error) {
	// 构建数据的页面代码...
	fmt.Println("生成spu详情静态页面数据对象...")
	return Product, nil
}

type PageFactory struct{}


func main() {
	c := &Context{
		URI: "cart/list",
	}
	

	pageObject.MakeData(c)
}
```



