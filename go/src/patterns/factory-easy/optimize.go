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

// PageFactory 构建页面对象的简单工厂
type PageFactory struct {
	Ctx *Context
}

// Get 获取对象
func (p *PageFactory) Get() PageInterface {
	switch p.Ctx.URI {
	case CartListConst:
		return &CartList{}
	case ProductListConst:
		return &ProductList{}

	default:
		panic("不支持的页面")
	}
}

// 客户端使用示例
func main() {
	ctx := &Context{
		URI: "cart/list",
	}
	pageFactory := &PageFactory{
		Ctx: ctx,
	}
	pageFactory.Get().MakeData(ctx)
}
