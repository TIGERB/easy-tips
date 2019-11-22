叼叼的设计模式(Go享版)-简单工厂

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
	// CartListConst 购物车列表页面
	CartListConst = "cart/list"
	// ProductListConst 商品列表页面
	ProductListConst = "product/list"
)

// Context 请求上下文
type Context struct {
	URI string
}

// PageInterface PageInterface
type PageInterface interface {
	MakeData(c *Context) (interface{}, error)
}

// CartList 购物车页面数据对象
type CartList struct {
	header      interface{}
	cartSkuList interface{}
	footer      interface{}
}

// MakeData 构建数据对象
func (cartList *CartList) MakeData(c *Context) (interface{}, error) {
	// 构建数据的页面代码...
	fmt.Println("生成购物车页面数据...")
	return cartList, nil
}

// ProductList 购物车页面数据对象
type ProductList struct {
	header  interface{}
	SpuList interface{}
	footer  interface{}
}

// MakeData 构建数据对象
func (productList *ProductList) MakeData(c *Context) (interface{}, error) {
	// 构建数据的页面代码...
	fmt.Println("生成spu列表数据...")
	return productList, nil
}

func main() {
	c := &Context{
		URI: "cart/list",
	}
	var pageObject PageInterface
	switch c.URI {
	case CartListConst:
		pageObject = &CartList{}
	case ProductListConst:
		pageObject = &ProductList{}

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
	// CartListConst 购物车列表页面
	CartListConst = "cart/list"
	// ProductListConst 商品列表页面
	ProductListConst = "product/list"
)

// Context 请求上下文
type Context struct {
	URI string
}

// PageInterface PageInterface
type PageInterface interface {
	MakeData(c *Context) (interface{}, error)
}

// CartList 购物车页面数据对象
type CartList struct {
	header      interface{}
	cartSkuList interface{}
	footer      interface{}
}

// MakeData 构建数据对象
func (cartList *CartList) MakeData(c *Context) (interface{}, error) {
	// 构建数据的页面代码...
	fmt.Println("生成购物车页面数据...")
	return cartList, nil
}

// ProductList 购物车页面数据对象
type ProductList struct {
	header  interface{}
	SpuList interface{}
	footer  interface{}
}

// MakeData 构建数据对象
func (productList *ProductList) MakeData(c *Context) (interface{}, error) {
	// 构建数据的页面代码...
	fmt.Println("生成spu列表数据...")
	return productList, nil
}

type PageFactory struct{}


func main() {
	c := &Context{
		URI: "cart/list",
	}
	

	pageObject.MakeData(c)
}
```



