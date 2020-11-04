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
	header interface{}

	footer interface{}
}

// MakeData 构建数据对象
func (Cart *Cart) MakeData(c *Context) (interface{}, error) {
	// 构建数据的页面代码...
	fmt.Println("生成购物车静态页面数据对象...")
	return Cart, nil
}

// Product Spu页面数据对象
type Product struct {
	header interface{}

	footer interface{}
}

// MakeData 构建数据对象
func (Product *Product) MakeData(c *Context) (interface{}, error) {
	// 构建数据的页面代码...
	fmt.Println("生成spu详情静态页面数据对象...")
	return Product, nil
}

// 客户端使用示例
func main() {
	ctx := &Context{
		URI: "cart/list",
	}
	var pageObject PageInterface
	switch ctx.URI {
	case CartConst:
		pageObject = &Cart{}
	case ProductConst:
		pageObject = &Product{}

	default:
		panic("不支持的页面")
	}

	pageObject.MakeData(ctx)
}
