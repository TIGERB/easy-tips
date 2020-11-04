package normal

import (
	"fmt"
	"net/http"
	"reflect"
)

//------------------------------------------------------------
//我的代码没有`else`系列
//组合模式
//@auhtor TIGERB<https://github.com/TIGERB>
//------------------------------------------------------------

// Context 上下文
type Context struct{}

// Component 组件接口
type Component interface {
	// 添加一个子组件
	Mount(c Component, components ...Component) error
	// 移除一个子组件
	Remove(c Component) error
	// 执行当前组件业务和执行子组件
	// ctx 业务上下文
	// currentConponent 当前组件
	Do(ctx *Context, currentConponent Component) error
	// 执行当前组件业务业务逻辑
	BusinessLogicDo(ctx *Context) error
	// 执行子组件
	ChildsDo(ctx *Context) error
}

// BaseComponent 基础组件
// 实现Add:添加一个子组件
// 实现Remove:移除一个子组件
type BaseComponent struct {
	// 子组件列表
	ChildComponents []Component
}

// Mount 挂载一个子组件
func (bc *BaseComponent) Mount(c Component, components ...Component) (err error) {
	bc.ChildComponents = append(bc.ChildComponents, c)
	if len(components) == 0 {
		return
	}
	bc.ChildComponents = append(bc.ChildComponents, components...)
	return
}

// Remove 移除一个子组件
func (bc *BaseComponent) Remove(c Component) (err error) {
	if len(bc.ChildComponents) == 0 {
		return
	}
	for k, childComponent := range bc.ChildComponents {
		if c == childComponent {
			fmt.Println(runFuncName(), "移除:", reflect.TypeOf(childComponent))
			bc.ChildComponents = append(bc.ChildComponents[:k], bc.ChildComponents[k+1:]...)
		}
	}
	return
}

// Do 执行子组件
// ctx 业务上下文
// currentConponent 当前组件
func (bc *BaseComponent) Do(ctx *Context, currentConponent Component) (err error) {
	//执行当前组件业务代码
	err = currentConponent.BusinessLogicDo(ctx)
	if err != nil {
		return err
	}
	// 执行子组件
	return currentConponent.ChildsDo(ctx)
}

// BusinessLogicDo 当前组件业务逻辑代码填充处
func (bc *BaseComponent) BusinessLogicDo() (err error) {
	// do nothing
	return
}

// ChildsDo 执行子组件
func (bc *BaseComponent) ChildsDo(ctx *Context) (err error) {
	// 执行子组件
	for _, childComponent := range bc.ChildComponents {
		if err = childComponent.Do(ctx, childComponent); err != nil {
			return err
		}
	}
	return
}

// CheckoutPageComponent 订单结算页面组件
type CheckoutPageComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 执行组件业务逻辑
func (bc *CheckoutPageComponent) BusinessLogicDo(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	// fmt.Println(runFuncName(), "订单结算页面组件...")
	return
}

// AddressComponent 地址组件
type AddressComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 执行组件业务逻辑
func (bc *AddressComponent) BusinessLogicDo(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	// fmt.Println(runFuncName(), "地址组件...")
	// 模拟远程调用地址服务
	http.Get("http://example.com/")
	return
}

// PayMethodComponent 支付方式组件
type PayMethodComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 执行组件业务逻辑
func (bc *PayMethodComponent) BusinessLogicDo(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	// fmt.Println(runFuncName(), "支付方式组件...")
	return
}

// StoreComponent 店铺组件
type StoreComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 执行组件业务逻辑
func (bc *StoreComponent) BusinessLogicDo(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	// fmt.Println(runFuncName(), "店铺组件...")
	return
}

// SkuComponent 商品组件
type SkuComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 执行组件业务逻辑
func (bc *SkuComponent) BusinessLogicDo(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	// fmt.Println(runFuncName(), "商品组件...")
	return
}

// PromotionComponent 优惠信息组件
type PromotionComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 执行组件业务逻辑
func (bc *PromotionComponent) BusinessLogicDo(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	// fmt.Println(runFuncName(), "优惠信息组件...")
	return
}

// ExpressComponent 物流组件
type ExpressComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 执行组件业务逻辑
func (bc *ExpressComponent) BusinessLogicDo(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	// fmt.Println(runFuncName(), "物流组件...")
	return
}

// AftersaleComponent 售后组件
type AftersaleComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 执行组件业务逻辑
func (bc *AftersaleComponent) BusinessLogicDo(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	// fmt.Println(runFuncName(), "售后组件...")
	return
}

// InvoiceComponent 发票组件
type InvoiceComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 执行组件业务逻辑
func (bc *InvoiceComponent) BusinessLogicDo(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	// fmt.Println(runFuncName(), "发票组件...")
	return
}

// CouponComponent 优惠券组件
type CouponComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 执行组件业务逻辑
func (bc *CouponComponent) BusinessLogicDo(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	// fmt.Println(runFuncName(), "优惠券组件...")
	// 模拟远程调用优惠券服务
	http.Get("http://example.com/")
	return
}

// GiftCardComponent 礼品卡组件
type GiftCardComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 执行组件业务逻辑
func (bc *GiftCardComponent) BusinessLogicDo(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	// fmt.Println(runFuncName(), "礼品卡组件...")
	return
}

// OrderComponent 订单金额详细信息组件
type OrderComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 执行组件业务逻辑
func (bc *OrderComponent) BusinessLogicDo(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	// fmt.Println(runFuncName(), "订单金额详细信息组件...")
	return
}

// Demo 示例
func Demo() {
	// 初始化订单结算页面 这个大组件
	checkoutPage := &CheckoutPageComponent{}

	// 挂载子组件
	storeComponent := &StoreComponent{}
	skuComponent := &SkuComponent{}
	skuComponent.Mount(
		&PromotionComponent{},
		&AftersaleComponent{},
	)
	storeComponent.Mount(
		skuComponent,
		&ExpressComponent{},
	)

	// 挂载组件
	checkoutPage.Mount(
		&AddressComponent{},
		&PayMethodComponent{},
		storeComponent,
		&InvoiceComponent{},
		&CouponComponent{},
		&GiftCardComponent{},
		&OrderComponent{},
	)

	// checkoutPage.Remove(storeComponent)

	// 开始构建页面组件数据
	checkoutPage.ChildsDo(&Context{})
}

// func main() {
// 	runtime.GOMAXPROCS(runtime.NumCPU() - 1)
// 	// trace.Start(os.Stderr)
// 	// defer trace.Stop()
// 	Demo()
// }

// 获取正在运行的函数名
func runFuncName() string {
	// pc := make([]uintptr, 1)
	// runtime.Callers(2, pc)
	// f := runtime.FuncForPC(pc[0])
	// return f.Name()
	return ""
}
