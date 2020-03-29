package main

import (
	"fmt"
	"reflect"
	"runtime"
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
	// 执行组件&子组件
	Do(ctx *Context) error
}

// BaseComponent 基础组件
// 实现Add:添加一个子组件
// 实现Remove:移除一个子组件
type BaseComponent struct {
	// 子组件列表
	ChildComponents []Component
	// 并发子组件列表
	ChildConcurrencyComponents []Component
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

// MountConcurrency 挂载一个并发子组件
func (bc *BaseComponent) MountConcurrency(c Component, components ...Component) (err error) {
	bc.ChildConcurrencyComponents = append(bc.ChildConcurrencyComponents, c)
	if len(components) == 0 {
		return
	}
	bc.ChildConcurrencyComponents = append(bc.ChildConcurrencyComponents, components...)
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

// Do 执行组件&子组件
func (bc *BaseComponent) Do(ctx *Context) (err error) {
	// do nothing
	return
}

// ChildsDo 执行子组件
func (bc *BaseComponent) ChildsDo(ctx *Context) (err error) {
	// 执行子组件
	for _, childComponent := range bc.ChildComponents {
		if err = childComponent.Do(ctx); err != nil {
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

// Do 执行组件&子组件
func (bc *CheckoutPageComponent) Do(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "订单结算页面组件...")

	// 执行子组件
	bc.ChildsDo(ctx)

	// 当前组件的业务逻辑写这

	return
}

// AddressComponent 地址组件
type AddressComponent struct {
	// 合成复用基础组件
	BaseComponent
	// AddressInfo
	AddressInfo *AddressInfo
	resChan     chan *AddressInfo
}

// AddressInfo 地址信息
type AddressInfo struct {
	AddressID  int64
	FristName  string
	SecondName string
}

// Do 执行组件&子组件
func (bc *AddressComponent) Do(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	bc.resChan = make(chan *AddressInfo, 1)
	go func(ctx *Context, resChan chan<- *AddressInfo) {
		fmt.Println(runFuncName(), "获取地址信息 ing...")
		addressInfo := &AddressInfo{
			AddressID:  9931831,
			FristName:  "hei",
			SecondName: "heihei",
		}
		resChan <- addressInfo
		fmt.Println(runFuncName(), "获取地址信息 成功...")
	}(ctx, bc.resChan)
	res := <-bc.resChan
	if res == nil {
		return fmt.Errorf("获取地址信息失败")
	}
	bc.AddressInfo = res
	return
}

// PayMethodComponent 支付方式组件
type PayMethodComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// Do 执行组件&子组件
func (bc *PayMethodComponent) Do(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "支付方式组件...")

	// 执行子组件
	bc.ChildsDo(ctx)

	// 当前组件的业务逻辑写这

	return
}

// StoreComponent 店铺组件
type StoreComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// Do 执行组件&子组件
func (bc *StoreComponent) Do(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "店铺组件...")

	// 执行子组件
	bc.ChildsDo(ctx)

	// 当前组件的业务逻辑写这

	return
}

// SkuComponent 商品组件
type SkuComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// Do 执行组件&子组件
func (bc *SkuComponent) Do(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "商品组件...")

	// 执行子组件
	bc.ChildsDo(ctx)

	// 当前组件的业务逻辑写这

	return
}

// PromotionComponent 优惠信息组件
type PromotionComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// Do 执行组件&子组件
func (bc *PromotionComponent) Do(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "优惠信息组件...")

	// 执行子组件
	bc.ChildsDo(ctx)

	// 当前组件的业务逻辑写这

	return
}

// ExpressComponent 物流组件
type ExpressComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// Do 执行组件&子组件
func (bc *ExpressComponent) Do(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "物流组件...")

	// 执行子组件
	bc.ChildsDo(ctx)

	// 当前组件的业务逻辑写这

	return
}

// AftersaleComponent 售后组件
type AftersaleComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// Do 执行组件&子组件
func (bc *AftersaleComponent) Do(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "售后组件...")

	// 执行子组件
	bc.ChildsDo(ctx)

	// 当前组件的业务逻辑写这

	return
}

// InvoiceComponent 发票组件
type InvoiceComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// Do 执行组件&子组件
func (bc *InvoiceComponent) Do(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "发票组件...")

	// 执行子组件
	bc.ChildsDo(ctx)

	// 当前组件的业务逻辑写这

	return
}

// CouponComponent 优惠券组件
type CouponComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// Do 执行组件&子组件
func (bc *CouponComponent) Do(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "优惠券组件...")

	// 执行子组件
	bc.ChildsDo(ctx)

	// 当前组件的业务逻辑写这

	return
}

// GiftCardComponent 礼品卡组件
type GiftCardComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// Do 执行组件&子组件
func (bc *GiftCardComponent) Do(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "礼品卡组件...")

	// 执行子组件
	bc.ChildsDo(ctx)

	// 当前组件的业务逻辑写这

	return
}

// OrderComponent 订单金额详细信息组件
type OrderComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// Do 执行组件&子组件
func (bc *OrderComponent) Do(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "订单金额详细信息组件...")

	// 执行子组件
	bc.ChildsDo(ctx)

	// 当前组件的业务逻辑写这

	return
}

func main() {
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

	// 开始构建页面组件数据
	checkoutPage.Do(&Context{})
}

// 获取正在运行的函数名
func runFuncName() string {
	pc := make([]uintptr, 1)
	runtime.Callers(2, pc)
	f := runtime.FuncForPC(pc[0])
	return f.Name()
}
