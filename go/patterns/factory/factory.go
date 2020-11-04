package main

//------------------------------------------------------------
//我的代码没有`else`系列
//工厂模式
//@auhtor TIGERB<https://github.com/TIGERB>
//------------------------------------------------------------

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

// PageFactoryInterface 页面简单工厂接口
type PageFactoryInterface interface {
	Get() PageInterface
}

// CartPageFactory 构建购物车页面对象的简单工厂
type CartPageFactory struct {
	Ctx *Context
}

// Get 获取对象
func (p *CartPageFactory) Get() PageInterface {
	return &Cart{}
}

// ProductPageFactory 构建购物车页面对象的简单工厂
type ProductPageFactory struct {
	Ctx *Context
}

// Get 获取对象
func (p *ProductPageFactory) Get() PageInterface {
	return &Cart{}
}

// 客户端使用示例
func main() {
	ctx := &Context{}

	// 生成购物车页面数据对象
	(&CartPageFactory{
		Ctx: ctx,
	}).Get().MakeData(ctx)

	// 生成spu详情页面数据对象
	(&ProductPageFactory{
		Ctx: ctx,
	}).Get().MakeData(ctx)
}
