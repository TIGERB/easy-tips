# 代码组件 | 我的代码没有else

> 嗯，我的代码没有`else`系列，一个设计模式业务真实使用的golang系列。

## 前言

本系列主要分享，如何在我们的真实业务场景中使用设计模式。

本系列文章主要采用如下结构：

- 什么是「XX设计模式」？
- 什么真实业务场景可以使用「XX设计模式」？
- 怎么用「XX设计模式」？

本文主要介绍「组合模式」如何在真实业务场景中使用。

## 什么是「组合模式」？

> 一个具有层级关系的对象由一系列拥有父子关系的对象通过树形结构组成。

组合模式的优势：

- 所见即所码：你所看见的代码结构就是业务真实的层级关系，比如Ui界面你真实看到的那样。
- 高度封装：单一职责。
- 可复用：不同业务场景，相同的组件可被重复使用。

## 什么真实业务场景可以用「组合模式」？

满足如下要求的所有场景:

> Get请求获取页面数据的所有接口

前端大行组件化的当今，我们在写后端接口代码的时候还是按照业务思路一头写到尾吗？我们是否可以思索，「后端接口业务代码如何可以简单快速组件化？」，答案是肯定的，这就是「组合模式」的作用。

我们利用「组合模式」的定义和前端模块的划分去构建后端业务代码结构：

- 前端单个模块 -> 对应后端：具体单个类 -> 封装的过程
- 前端模块父子组件 ->  对应后端：父类内部持有多个子类(非继承关系，合成复用关系) -> 父子关系的树形结构

> 我们有哪些真实业务场景可以用「组合模式」呢？

比如我们以“复杂的订单结算页面”为例，下面是某东的订单结算页面：

<p align="center">
  <img src="http://cdn.tigerb.cn/20200331124724.jpeg" style="width:38%">
</p>

从页面的展示形式上，可以看出：

- 页面由多个模块构成，比如：
	+ 地址模块
	+ 支付方式模块
	+ 店铺模块
	+ 发票模块
	+ 优惠券模块
	+ 某豆模块
	+ 礼品卡模块
	+ 订单详细金额模块
- 单个模块可以由多个子模块构成
	+ 店铺模块，又由如下模块构成：
		* 商品模块
		* 售后模块
		* 优惠模块
		* 物流模块

## 怎么用「组合模式」？

关于怎么用，完全可以生搬硬套我总结的使用设计模式的四个步骤：

- 业务梳理
- 业务流程图
- 代码建模
- 代码demo

#### 业务梳理

按照如上某东的订单结算页面的示例，我们得到了如下的订单结算页面模块组成图：

<p align="center">
  <img src="http://cdn.tigerb.cn/20200329222214.png" style="width:46%">
</p>

> 注：模块不一定完全准确

#### 代码建模

责任链模式主要类主要包含如下特性：

- 成员属性
	+ `ChildComponents`: 子组件列表 -> 稳定不变的
- 成员方法
	+ `Mount`: 添加一个子组件 -> 稳定不变的
	+ `Remove`: 移除一个子组件 -> 稳定不变的
	+ `Do`: 执行组件&子组件 -> 变化的

套用到订单结算页面信息接口伪代码实现如下：
```
一个父类(抽象类)：
- 成员属性
	+ `ChildComponents`: 子组件列表
- 成员方法
	+ `Mount`: 实现添加一个子组件
	+ `Remove`: 实现移除一个子组件
	+ `Do`: 抽象方法

组件一，订单结算页面组件类(继承父类、看成一个大的组件)： 
- 成员方法
	+ `Do`: 执行当前组件的逻辑，执行子组件的逻辑

组件二，地址组件(继承父类)：
- 成员方法
	+ `Do`: 执行当前组件的逻辑，执行子组件的逻辑

组件三，支付方式组件(继承父类)：
- 成员方法
	+ `Do`: 执行当前组件的逻辑，执行子组件的逻辑

组件四，店铺组件(继承父类)：
- 成员方法
	+ `Do`: 执行当前组件的逻辑，执行子组件的逻辑

组件五，商品组件(继承父类)：
- 成员方法
	+ `Do`: 执行当前组件的逻辑，执行子组件的逻辑

组件六，优惠信息组件(继承父类)：
- 成员方法
	+ `Do`: 执行当前组件的逻辑，执行子组件的逻辑

组件七，物流组件(继承父类)：
- 成员方法
	+ `Do`: 执行当前组件的逻辑，执行子组件的逻辑

组件八，发票组件(继承父类)：
- 成员方法
	+ `Do`: 执行当前组件的逻辑，执行子组件的逻辑

组件九，优惠券组件(继承父类)：
- 成员方法
	+ `Do`: 执行当前组件的逻辑，执行子组件的逻辑

组件十，礼品卡组件(继承父类)：
- 成员方法
	+ `Do`: 执行当前组件的逻辑，执行子组件的逻辑

组件十一，订单金额详细信息组件(继承父类)：
- 成员方法
	+ `Do`: 执行当前组件的逻辑，执行子组件的逻辑
组件十二，售后组件(继承父类，未来扩展的组件)：
- 成员方法
	+ `Do`: 执行当前组件的逻辑，执行子组件的逻辑
```

但是，golang里没有的继承的概念，要复用成员属性`ChildComponents`、成员方法`Mount`、成员方法`Remove`怎么办呢？我们使用`合成复用`的特性变相达到“继承复用”的目的，如下：

```
一个接口(interface)：
+ 抽象方法`Mount`: 添加一个子组件
+ 抽象方法`Remove`: 移除一个子组件
+ 抽象方法`Do`: 执行组件&子组件

一个基础结构体`BaseComponent`：
- 成员属性
	+ `ChildComponents`: 子组件列表
- 成员方法
	+ 实体方法`Mount`: 添加一个子组件
	+ 实体方法`Remove`: 移除一个子组件
	+ 实体方法`ChildsDo`: 执行子组件

组件一，订单结算页面组件类： 
- 合成复用基础结构体`BaseComponent` 
- 成员方法
	+ `Do`: 执行当前组件的逻辑，执行子组件的逻辑

组件二，地址组件：
- 合成复用基础结构体`BaseComponent` 
- 成员方法
	+ `Do`: 执行当前组件的逻辑，执行子组件的逻辑

组件三，支付方式组件：
- 合成复用基础结构体`BaseComponent` 
- 成员方法
	+ `Do`: 执行当前组件的逻辑，执行子组件的逻辑

...略

组件十一，订单金额详细信息组件：
- 合成复用基础结构体`BaseComponent` 
- 成员方法
	+ `Do`: 执行当前组件的逻辑，执行子组件的逻辑

```

同时得到了我们的UML图：

<p align="center">
  <img src="http://cdn.tigerb.cn/20200403125814.jpg" style="width:100%">
</p>

#### 代码demo

```go
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
}

// Do 执行组件&子组件
func (bc *AddressComponent) Do(ctx *Context) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "地址组件...")

	// 执行子组件
	bc.ChildsDo(ctx)

	// 当前组件的业务逻辑写这

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

	// 移除组件测试
	// checkoutPage.Remove(storeComponent)

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


```

代码运行结果：

```
[Running] go run "../easy-tips/go/src/patterns/composite/composite.go"
main.(*CheckoutPageComponent).Do 订单结算页面组件...
main.(*AddressComponent).Do 地址组件...
main.(*PayMethodComponent).Do 支付方式组件...
main.(*StoreComponent).Do 店铺组件...
main.(*SkuComponent).Do 商品组件...
main.(*PromotionComponent).Do 优惠信息组件...
main.(*AftersaleComponent).Do 售后组件...
main.(*ExpressComponent).Do 物流组件...
main.(*InvoiceComponent).Do 发票组件...
main.(*CouponComponent).Do 优惠券组件...
main.(*GiftCardComponent).Do 礼品卡组件...
main.(*OrderComponent).Do 订单金额详细信息组件...
```

## 结语

最后总结下，「组合模式」抽象过程的核心是：

- 按模块划分：业务逻辑归类，收敛的过程。
- 父子关系(树)：把收敛之后的业务对象按父子关系绑定，依次被执行。

与「责任链模式」的区别：

- 责任链模式: 链表
- 组合模式：树

```
特别说明：
1. 我的代码没有`else`，只是一个在代码合理设计的情况下自然而然无限接近或者达到的结果，并不是一个硬性的目标，务必较真。
2. 本系列的一些设计模式的概念可能和原概念存在差异，因为会结合实际使用，取其精华，适当改变，灵活使用。
```

# 文章列表

- [代码模板 | 我的代码没有else](https://github.com/TIGERB/easy-tips/tree/master/go/src/patterns/template)
- [链式调用 | 我的代码没有else](https://github.com/TIGERB/easy-tips/tree/master/go/src/patterns/responsibility)
- [代码组件 | 我的代码没有else](https://github.com/TIGERB/easy-tips/tree/master/go/src/patterns/composite)
- [订阅通知 | 我的代码没有else](https://github.com/TIGERB/easy-tips/tree/master/go/src/patterns/observer)
- [客户决策 | 我的代码没有else](https://github.com/TIGERB/easy-tips/tree/master/go/src/patterns/strategy)
- [状态变换 | 我的代码没有else](https://github.com/TIGERB/easy-tips/tree/master/go/src/patterns/state)

> [我的代码没有else系列 更多文章 点击此处查看](https://github.com/TIGERB/easy-tips/tree/master/go/src/patterns)
