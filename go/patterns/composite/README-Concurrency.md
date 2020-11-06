# 并发组件 | Go设计模式实战

> 嗯，Go设计模式实战系列，一个设计模式业务真实使用的golang系列。

<p align="left">
  <img src="http://cdn.tigerb.cn/20201103130617.png" style="width:38%">
</p>

## 前言

本系列主要分享，如何在我们的真实业务场景中使用设计模式。

本系列文章主要采用如下结构：

- 什么是「XX设计模式」？
- 什么真实业务场景可以使用「XX设计模式」？
- 怎么用「XX设计模式」？

本文主要介绍「组合模式」结合Go语言天生的并发特性，如何在真实业务场景中使用。

之前文章[《代码组件 | Go设计模式实战》](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/composite)已经介绍了「组合模式」的概念，以及在业务中的使用。今天我们结合Go语言天生的并发特性，升级「组合模式」为「并发组合模式」。

我们先来简单回顾下「组合模式」的知识，详细可以查看上篇文章[《代码组件 | Go设计模式实战》](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/composite)

## 什么是「并发组合模式」？

组合模式的概念：

> 一个具有层级关系的对象由一系列拥有父子关系的对象通过树形结构组成。

并发组合模式的概念：

> 一个具有层级关系的对象由一系列拥有父子关系的对象通过树形结构组成，子对象即可被串行执行，也可被并发执行

并发组合模式的优势：

- 原本串行的业务(存在阻塞的部分，比如网络IO等)可以被并发执行，利用多核优势提升性能。

## 什么真实业务场景可以用「并发组合模式」？

我们还是以「组合模式」中的“订单结算页面”为例，继续来看看某东的订单结算页面：

<p align="center">
  <img src="http://cdn.tigerb.cn/20200331124724.jpeg" style="width:30%">
</p>

从页面的展示形式上，可以看出：

- 页面由多个模块构成，比如：
	+ 地址模块：获取用户地址数据
	+ 支付方式模块：获取支付方式列表
	+ 店铺模块：获取店铺、购物车选中商品等信息
	+ 发票模块：获取发票类型列表
	+ 优惠券模块：获取用户优惠券列表
	+ 某豆模块：获取用户积分信息
	+ 礼品卡模块：获取礼品卡列表列表
	+ 订单详细金额模块：获取订单金额信息
- 单个模块可以由多个子模块构成
	+ 店铺模块，又由如下模块构成：
		* 商品模块：获取购物车选中商品信息
		* 售后模块：获取商品售后信息
		* 优惠模块：获取商品参与的优惠活动信息
		* 物流模块：获取商品支持的配送方式列表

按照「组合模式」的业务逻辑执行流程：

<p align="center">
  <img src="http://cdn.tigerb.cn/20201103203539.png" style="width:38%">
</p>

但是，我们很清楚有些模块之间并没有依赖，**且该模块涉及服务远程调用等阻塞操作**，比如：
- 地址模块调用地址服务获取用户地址数据时。
- 支付方式模块也可以同时去读redis获取支付方式列表数据等等。

所以:**有的模块其实可以被并发的执行**。

如果把上面不存在依赖关系的模块修改为并发的执行，则我们得到如下的执行流程：

<p align="center">
  <img src="http://cdn.tigerb.cn/20201103203735.png" style="width:100%">
</p>

## 怎么用「并发组合模式」？

关于「并发组合模式」的建模过程完全可以参考之前文章[《代码组件 | Go设计模式实战》](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/composite)，我们这里只说说需要着重注意的地方。

「并发组合模式」的核心还是`Component`组件接口，我们先看看「组合模式」的`Component`组件接口如下(再之前的文章上做了优化，进一步封装提取了`BusinessLogicDo`方法)：

```go
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
```

再来看看「并发组合模式」的Component`组件接口，如下(重点看和「组合模式」的区别)：
```go
// Component 组件接口
type Component interface {
	// 添加一个子组件
	Mount(c Component, components ...Component) error
	// 移除一个子组件
	Remove(c Component) error
	// 执行当前组件业务:`BusinessLogicDo`和执行子组件:`ChildsDo`
	// ctx 业务上下文
	// currentConponent 当前组件
	// wg 父组件的WaitGroup对象
	// 区别1：增加了WaitGroup对象参数，目的是等待并发子组件的执行完成。
	Do(ctx *Context, currentConponent Component, wg *sync.WaitGroup) error
	// 执行当前组件业务逻辑
	// resChan 回写当前组件业务执行结果的channel
	// 区别2：增加了一个channel参数，目的是并发组件执行逻辑时引入了超时机制，需要一个channel接受组件的执行结果
	BusinessLogicDo(resChan chan interface{}) error
	// 执行子组件
	ChildsDo(ctx *Context) error
}
```

我们详细再来看，相对于「组合模式」，引入并发之后需要着重关注如下几点：

- 并发子组件需要设置超时时间：防止子组件执行时间过长，解决方案关键字`context.WithTimeout`
- 区分普通组件和并发组件：合成复用基础组件，封装为并发基础组件
- 拥有并发子组件的父组件需要等待并发子组件执行完毕(包含超时)，解决方案关键字`sync.WaitGroup`
- 并发子组件执行自身业务逻辑是需检测超时：防止子组件内部执行业务逻辑时间过长，解决方案关键字`select`和`<-ctx.Done()`

### 第一点：并发子组件需要设置超时时间

```go
// Context 业务上下文
type Context struct {
	// context.WithTimeout派生的子上下文
	TimeoutCtx context.Context
	// 超时函数
	context.CancelFunc
}
```

### 第二点：区分普通组件和并发组件

增加新的并发基础组件结构体`BaseConcurrencyComponent`，并合成复用「组合模式」中的基础组件`BaseComponent`，如下：

```go
// BaseConcurrencyComponent 并发基础组件
type BaseConcurrencyComponent struct {
	// 合成复用基础组件
	BaseComponent
	// 当前组件是否有并发子组件
	HasChildConcurrencyComponents bool
	// 并发子组件列表
	ChildConcurrencyComponents []Component
	// wg 对象
	*sync.WaitGroup
	// 当前组件业务执行结果channel
	logicResChan chan interface{}
	// 当前组件执行过程中的错误信息
	Err error
}
```

### 第三点：拥有并发子组件的父组件需要等待并发子组件执行完毕(包含超时)

修改「组合模式」中的`ChildsDo`方法，使其支持并发执行子组件，主要修改和实现如下：

- 通过`go`关键字执行子组件
- 通过`*WaitGroup.Wait()`等待子组件执行结果

```go
// ChildsDo 执行子组件
func (bc *BaseConcurrencyComponent) ChildsDo(ctx *Context) (err error) {
	if bc.WaitGroup == nil {
		bc.WaitGroup = &sync.WaitGroup{}
	}
	// 执行并发子组件
	for _, childComponent := range bc.ChildConcurrencyComponents {
		bc.WaitGroup.Add(1)
		go childComponent.Do(ctx, childComponent, bc.WaitGroup)
	}
	// 执行子组件
	for _, childComponent := range bc.ChildComponents {
		if err = childComponent.Do(ctx, childComponent, nil); err != nil {
			return err
		}
	}
	if bc.HasChildConcurrencyComponents {
		// 等待并发组件执行结果
		bc.WaitGroup.Wait()
	}
	return
}
```

### 第四点：并发子组件执行自身业务逻辑是需检测超时

`select`关键字context.WithTimeout()派生的子上下文Done()方案返回的channel，发生超时该channel会被关闭。具体实现代码如下：

```go
// Do 执行子组件
// ctx 业务上下文
// currentConponent 当前组件
// wg 父组件的waitgroup对象
func (bc *BaseConcurrencyComponent) Do(ctx *Context, currentConponent Component, wg *sync.WaitGroup) (err error) {
	defer wg.Done()
	// 初始化并发子组件channel
	if bc.logicResChan == nil {
		bc.logicResChan = make(chan interface{}, 1)
	}

	go currentConponent.BusinessLogicDo(bc.logicResChan)

	select {
	// 等待业务执行结果
	case <-bc.logicResChan:
		// 业务执行结果
		fmt.Println(runFuncName(), "bc.BusinessLogicDo wait.done...")
		break
	// 超时等待
	case <-ctx.TimeoutCtx.Done():
		// 超时退出
		fmt.Println(runFuncName(), "bc.BusinessLogicDo timeout...")
		bc.Err = ErrConcurrencyComponentTimeout
		break
	}
	// 执行子组件
	err = currentConponent.ChildsDo(ctx)
	return
}
```

#### 代码demo

```go
package main

import (
	"context"
	"errors"
	"fmt"
	"net/http"
	"reflect"
	"sync"
	"time"
)

//------------------------------------------------------------
//Go设计模式实战系列
//组合模式
//@auhtor TIGERB<https://github.com/TIGERB>
//------------------------------------------------------------

//example:
// 创建一个根组件
// 如果子组件存在并发组件则父组件必须为并发组件
// type RootComponent struct {
// 	BaseConcurrencyComponent
// }
//
// func (bc *RootComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
// 	// do nothing
// 	return
// }
//
// 创建一个并发组件
// type DemoConcurrenyComponent struct {
// 	BaseConcurrencyComponent
// }
//
// func (bc *DemoConcurrenyComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
// 	// 并发组件业务逻辑填充到这
// 	return
// }
//
// 创建一个普通组件
// type DemoComponent struct {
// 	BaseComponent
// }
//
// func (bc *DemoComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
// 	// 普通组件业务逻辑填充到这
// 	return
// }
//
// // 普通组件
// root.Mount(
// 	&DemoComponent{},
// )
//
// // 并发组件
// root := &RootComponent{}
// root.MountConcurrency(
// 	&DemoConcurrenyComponent{},
// )
//
// // 初始化业务上下文 并设置超时时间
// ctx := GetContext(5 * time.Second)
// defer ctx.CancelFunc()
// // 开始执行子组件
// root.ChildsDo(ctx)

var (
	// ErrConcurrencyComponentTimeout 并发组件业务超时
	ErrConcurrencyComponentTimeout = errors.New("Concurrency Component Timeout")
)

// Context 业务上下文
type Context struct {
	// context.WithTimeout派生的子上下文
	TimeoutCtx context.Context
	// 超时函数
	context.CancelFunc
}

// GetContext 获取业务上下文实例
// d 超时时间
func GetContext(d time.Duration) *Context {
	c := &Context{}
	c.TimeoutCtx, c.CancelFunc = context.WithTimeout(context.Background(), d)
	return c
}

// Component 组件接口
type Component interface {
	// 添加一个子组件
	Mount(c Component, components ...Component) error
	// 移除一个子组件
	Remove(c Component) error
	// 执行当前组件业务:`BusinessLogicDo`和执行子组件:`ChildsDo`
	// ctx 业务上下文
	// currentConponent 当前组件
	// wg 父组件的waitgroup对象
	Do(ctx *Context, currentConponent Component, wg *sync.WaitGroup) error
	// 执行当前组件业务逻辑
	// resChan 回写当前组件业务执行结果的channel
	BusinessLogicDo(resChan chan interface{}) error
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
// wg 父组件的waitgroup对象
func (bc *BaseComponent) Do(ctx *Context, currentConponent Component, wg *sync.WaitGroup) (err error) {
	//执行当前组件业务代码
	err = currentConponent.BusinessLogicDo(nil)
	if err != nil {
		return err
	}
	// 执行子组件
	return currentConponent.ChildsDo(ctx)
}

// BusinessLogicDo 当前组件业务逻辑代码填充处
func (bc *BaseComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
	// do nothing
	return
}

// ChildsDo 执行子组件
func (bc *BaseComponent) ChildsDo(ctx *Context) (err error) {
	// 执行子组件
	for _, childComponent := range bc.ChildComponents {
		if err = childComponent.Do(ctx, childComponent, nil); err != nil {
			return err
		}
	}
	return
}

// BaseConcurrencyComponent 并发基础组件
type BaseConcurrencyComponent struct {
	// 合成复用基础组件
	BaseComponent
	// 当前组件是否有并发子组件
	HasChildConcurrencyComponents bool
	// 并发子组件列表
	ChildConcurrencyComponents []Component
	// wg 对象
	*sync.WaitGroup
	// 当前组件业务执行结果channel
	logicResChan chan interface{}
	// 当前组件执行过程中的错误信息
	Err error
}

// Remove 移除一个子组件
func (bc *BaseConcurrencyComponent) Remove(c Component) (err error) {
	if len(bc.ChildComponents) == 0 {
		return
	}
	for k, childComponent := range bc.ChildComponents {
		if c == childComponent {
			fmt.Println(runFuncName(), "移除:", reflect.TypeOf(childComponent))
			bc.ChildComponents = append(bc.ChildComponents[:k], bc.ChildComponents[k+1:]...)
		}
	}
	for k, childComponent := range bc.ChildConcurrencyComponents {
		if c == childComponent {
			fmt.Println(runFuncName(), "移除:", reflect.TypeOf(childComponent))
			bc.ChildConcurrencyComponents = append(bc.ChildComponents[:k], bc.ChildComponents[k+1:]...)
		}
	}
	return
}

// MountConcurrency 挂载一个并发子组件
func (bc *BaseConcurrencyComponent) MountConcurrency(c Component, components ...Component) (err error) {
	bc.HasChildConcurrencyComponents = true
	bc.ChildConcurrencyComponents = append(bc.ChildConcurrencyComponents, c)
	if len(components) == 0 {
		return
	}
	bc.ChildConcurrencyComponents = append(bc.ChildConcurrencyComponents, components...)
	return
}

// ChildsDo 执行子组件
func (bc *BaseConcurrencyComponent) ChildsDo(ctx *Context) (err error) {
	if bc.WaitGroup == nil {
		bc.WaitGroup = &sync.WaitGroup{}
	}
	// 执行并发子组件
	for _, childComponent := range bc.ChildConcurrencyComponents {
		bc.WaitGroup.Add(1)
		go childComponent.Do(ctx, childComponent, bc.WaitGroup)
	}
	// 执行子组件
	for _, childComponent := range bc.ChildComponents {
		if err = childComponent.Do(ctx, childComponent, nil); err != nil {
			return err
		}
	}
	if bc.HasChildConcurrencyComponents {
		// 等待并发组件执行结果
		bc.WaitGroup.Wait()
	}
	return
}

// Do 执行子组件
// ctx 业务上下文
// currentConponent 当前组件
// wg 父组件的waitgroup对象
func (bc *BaseConcurrencyComponent) Do(ctx *Context, currentConponent Component, wg *sync.WaitGroup) (err error) {
	defer wg.Done()
	// 初始化并发子组件channel
	if bc.logicResChan == nil {
		bc.logicResChan = make(chan interface{}, 1)
	}

	go currentConponent.BusinessLogicDo(bc.logicResChan)

	select {
	// 等待业务执行结果
	case <-bc.logicResChan:
		// 业务执行结果
		fmt.Println(runFuncName(), "bc.BusinessLogicDo wait.done...")
		break
	// 超时等待
	case <-ctx.TimeoutCtx.Done():
		// 超时退出
		fmt.Println(runFuncName(), "bc.BusinessLogicDo timeout...")
		bc.Err = ErrConcurrencyComponentTimeout
		break
	}
	// 执行子组件
	err = currentConponent.ChildsDo(ctx)
	return
}

// CheckoutPageComponent 订单结算页面组件
type CheckoutPageComponent struct {
	// 合成复用基础组件
	BaseConcurrencyComponent
}

// BusinessLogicDo 当前组件业务逻辑代码填充处
func (bc *CheckoutPageComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "订单结算页面组件...")
	return
}

// AddressComponent 地址组件
type AddressComponent struct {
	// 合成复用基础组件
	BaseConcurrencyComponent
}

// BusinessLogicDo 并发组件实际填充业务逻辑的地方
func (bc *AddressComponent) BusinessLogicDo(resChan chan interface{}) error {
	fmt.Println(runFuncName(), "地址组件...")
	fmt.Println(runFuncName(), "获取地址信息 ing...")

	// 模拟远程调用地址服务
	http.Get("http://example.com/")

	resChan <- struct{}{} // 写入业务执行结果
	fmt.Println(runFuncName(), "获取地址信息 done...")
	return nil
}

// PayMethodComponent 支付方式组件
type PayMethodComponent struct {
	// 合成复用基础组件
	BaseConcurrencyComponent
}

// BusinessLogicDo 并发组件实际填充业务逻辑的地方
func (bc *PayMethodComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "支付方式组件...")
	fmt.Println(runFuncName(), "获取支付方式 ing...")
	// 模拟远程调用地址服务 略
	resChan <- struct{}{}
	fmt.Println(runFuncName(), "获取支付方式 done...")
	return nil
}

// StoreComponent 店铺组件
type StoreComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 并发组件实际填充业务逻辑的地方
func (bc *StoreComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "店铺组件...")
	return
}

// SkuComponent 商品组件
type SkuComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 并发组件实际填充业务逻辑的地方
func (bc *SkuComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "商品组件...")
	return
}

// PromotionComponent 优惠信息组件
type PromotionComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 并发组件实际填充业务逻辑的地方
func (bc *PromotionComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "优惠信息组件...")
	return
}

// ExpressComponent 物流组件
type ExpressComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 并发组件实际填充业务逻辑的地方
func (bc *ExpressComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "物流组件...")
	return
}

// AftersaleComponent 售后组件
type AftersaleComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 并发组件实际填充业务逻辑的地方
func (bc *AftersaleComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "售后组件...")
	return
}

// InvoiceComponent 发票组件
type InvoiceComponent struct {
	// 合成复用基础组件
	BaseConcurrencyComponent
}

// BusinessLogicDo 并发组件实际填充业务逻辑的地方
func (bc *InvoiceComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "发票组件...")
	fmt.Println(runFuncName(), "获取发票信息 ing...")
	// 模拟远程调用地址服务 略
	resChan <- struct{}{} // 写入业务执行结果
	fmt.Println(runFuncName(), "获取发票信息 done...")
	return
}

// CouponComponent 优惠券组件
type CouponComponent struct {
	// 合成复用基础组件
	BaseConcurrencyComponent
}

// BusinessLogicDo 并发组件实际填充业务逻辑的地方
func (bc *CouponComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "优惠券组件...")
	fmt.Println(runFuncName(), "获取最优优惠券 ing...")

	// 模拟远程调用优惠券服务
	http.Get("http://example.com/")

	// 写入业务执行结果
	resChan <- struct{}{}
	fmt.Println(runFuncName(), "获取最优优惠券 done...")
	return
}

// GiftCardComponent 礼品卡组件
type GiftCardComponent struct {
	// 合成复用基础组件
	BaseConcurrencyComponent
}

// BusinessLogicDo 并发组件实际填充业务逻辑的地方
func (bc *GiftCardComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "礼品卡组件...")
	fmt.Println(runFuncName(), "获取礼品卡信息 ing...")
	// 模拟远程调用地址服务 略
	resChan <- struct{}{} // 写入业务执行结果
	fmt.Println(runFuncName(), "获取礼品卡信息 done...")
	return
}

// OrderComponent 订单金额详细信息组件
type OrderComponent struct {
	// 合成复用基础组件
	BaseComponent
}

// BusinessLogicDo 当前组件业务逻辑代码填充处
func (bc *OrderComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
	// 当前组件的业务逻辑写这
	fmt.Println(runFuncName(), "订单金额详细信息组件...")
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

	// ---挂载组件---

	// 普通组件
	checkoutPage.Mount(
		storeComponent,
		&OrderComponent{},
	)
	// 并发组件
	checkoutPage.MountConcurrency(
		&AddressComponent{},
		&PayMethodComponent{},
		&InvoiceComponent{},
		&CouponComponent{},
		&GiftCardComponent{},
	)

	// 初始化业务上下文 并设置超时时间
	ctx := GetContext(5 * time.Second)
	defer ctx.CancelFunc()
	// 开始构建页面组件数据
	checkoutPage.ChildsDo(ctx)
}

func main() {
	runtime.GOMAXPROCS(runtime.NumCPU() - 1)
	Demo()
}

// 获取正在运行的函数名
func runFuncName() string {
	pc := make([]uintptr, 1)
	runtime.Callers(2, pc)
	f := runtime.FuncForPC(pc[0])
	return f.Name()
	return ""
}


```

代码运行结果：

```
Running] go run "../easy-tips/go/patterns/composite/concurrency/composite-concurrency.go"
main.(*StoreComponent).BusinessLogicDo 店铺组件...
main.(*SkuComponent).BusinessLogicDo 商品组件...
main.(*PromotionComponent).BusinessLogicDo 优惠信息组件...
main.(*AftersaleComponent).BusinessLogicDo 售后组件...
main.(*ExpressComponent).BusinessLogicDo 物流组件...
main.(*OrderComponent).BusinessLogicDo 订单金额详细信息组件...
main.(*PayMethodComponent).BusinessLogicDo 支付方式组件...
main.(*PayMethodComponent).BusinessLogicDo 获取支付方式 ing...
main.(*InvoiceComponent).BusinessLogicDo 发票组件...
main.(*InvoiceComponent).BusinessLogicDo 获取发票信息 ing...
main.(*GiftCardComponent).BusinessLogicDo 礼品卡组件...
main.(*GiftCardComponent).BusinessLogicDo 获取礼品卡信息 ing...
main.(*CouponComponent).BusinessLogicDo 优惠券组件...
main.(*CouponComponent).BusinessLogicDo 获取发票信息 ing...
main.(*AddressComponent).BusinessLogicDo 地址组件...
main.(*AddressComponent).BusinessLogicDo 获取地址信息 ing...
main.(*InvoiceComponent).BusinessLogicDo 获取发票信息 done...
main.(*BaseConcurrencyComponent).Do bc.BusinessLogicDo wait.done...
main.(*BaseConcurrencyComponent).Do bc.BusinessLogicDo wait.done...
main.(*PayMethodComponent).BusinessLogicDo 获取支付方式 done...
main.(*AddressComponent).BusinessLogicDo 获取地址信息 done...
main.(*BaseConcurrencyComponent).Do bc.BusinessLogicDo wait.done...
main.(*CouponComponent).BusinessLogicDo 获取发票信息 done...
main.(*BaseConcurrencyComponent).Do bc.BusinessLogicDo wait.done...
main.(*GiftCardComponent).BusinessLogicDo 获取礼品卡信息 done...
main.(*BaseConcurrencyComponent).Do bc.BusinessLogicDo wait.done...
```

#### 「组合模式」和「并发组合模式」基准测试对比

基准测试代码：
```go
package composite

import (
	"easy-tips/go/patterns/composite/concurrency"
	"easy-tips/go/patterns/composite/normal"
	"runtime"
	"testing"
)

// go test -benchmem -run=^$ easy-tips/go/patterns/composite -bench . -v -count=1 --benchtime 20s

func Benchmark_Normal(b *testing.B) {
	b.SetParallelism(runtime.NumCPU())
	b.RunParallel(func(pb *testing.PB) {
		for pb.Next() {
			normal.Demo()
		}
	})
}

func Benchmark_Concurrency(b *testing.B) {
	b.SetParallelism(runtime.NumCPU())
	b.RunParallel(func(pb *testing.PB) {
		for pb.Next() {
			concurrency.Demo()
		}
	})
}
```

本地机器Benchmark对比测试结果：

```
(TIGERB) 🤔 ➜  composite git:(master) ✗ go test -benchmem -run=^$ easy-tips/go/patterns/composite -bench . -v -count=1 --benchtime 20s 
goos: darwin
goarch: amd64
pkg: easy-tips/go/patterns/composite
Benchmark_Normal-4                   376          56666895 ns/op           35339 B/op        286 allocs/op
Benchmark_Concurrency-4              715          32669301 ns/op           36445 B/op        299 allocs/op
PASS
ok      easy-tips/go/patterns/composite 68.835s
```

从上面的基准测试结果可以看出来`Benchmark_Concurrency-4`平均每次的执行时间是`32669301 ns`是要优于`Benchmark_Normal`的`56666895 ns`。

## 结语

「并发组合模式」是一个由特定的设计模式结合Go语言天生的并发特性，通过适当封装形成的“新模式”。

## 附录「并发组合模式」的基础代码模板与使用说明

```go
//------------------------------------------------------------
//Go设计模式实战系列
//组合模式
//@auhtor TIGERB<https://github.com/TIGERB>
//------------------------------------------------------------

//example:
// 创建一个根组件
// 如果子组件存在并发组件则父组件必须为并发组件
// type RootComponent struct {
// 	BaseConcurrencyComponent
// }
//
// func (bc *RootComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
// 	// do nothing
// 	return
// }
//
// 创建一个并发组件
// type DemoConcurrenyComponent struct {
// 	BaseConcurrencyComponent
// }
//
// func (bc *DemoConcurrenyComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
// 	// 并发组件业务逻辑填充到这
// 	return
// }
//
// 创建一个普通组件
// type DemoComponent struct {
// 	BaseComponent
// }
//
// func (bc *DemoComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
// 	// 普通组件业务逻辑填充到这
// 	return
// }
//
// // 普通组件
// root.Mount(
// 	&DemoComponent{},
// )
//
// // 并发组件
// root := &RootComponent{}
// root.MountConcurrency(
// 	&DemoConcurrenyComponent{},
// )
//
// // 初始化业务上下文 并设置超时时间
// ctx := GetContext(5 * time.Second)
// defer ctx.CancelFunc()
// // 开始执行子组件
// root.ChildsDo(ctx)

var (
	// ErrConcurrencyComponentTimeout 并发组件业务超时
	ErrConcurrencyComponentTimeout = errors.New("Concurrency Component Timeout")
)

// Context 业务上下文
type Context struct {
	// context.WithTimeout派生的子上下文
	TimeoutCtx context.Context
	// 超时函数
	context.CancelFunc
}

// GetContext 获取业务上下文实例
// d 超时时间
func GetContext(d time.Duration) *Context {
	c := &Context{}
	c.TimeoutCtx, c.CancelFunc = context.WithTimeout(context.Background(), d)
	return c
}

// Component 组件接口
type Component interface {
	// 添加一个子组件
	Mount(c Component, components ...Component) error
	// 移除一个子组件
	Remove(c Component) error
	// 执行当前组件业务:`BusinessLogicDo`和执行子组件:`ChildsDo`
	// ctx 业务上下文
	// currentConponent 当前组件
	// wg 父组件的waitgroup对象
	Do(ctx *Context, currentConponent Component, wg *sync.WaitGroup) error
	// 执行当前组件业务逻辑
	// resChan 回写当前组件业务执行结果的channel
	BusinessLogicDo(resChan chan interface{}) error
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
// wg 父组件的waitgroup对象
func (bc *BaseComponent) Do(ctx *Context, currentConponent Component, wg *sync.WaitGroup) (err error) {
	//执行当前组件业务代码
	err = currentConponent.BusinessLogicDo(nil)
	if err != nil {
		return err
	}
	// 执行子组件
	return currentConponent.ChildsDo(ctx)
}

// BusinessLogicDo 当前组件业务逻辑代码填充处
func (bc *BaseComponent) BusinessLogicDo(resChan chan interface{}) (err error) {
	// do nothing
	return
}

// ChildsDo 执行子组件
func (bc *BaseComponent) ChildsDo(ctx *Context) (err error) {
	// 执行子组件
	for _, childComponent := range bc.ChildComponents {
		if err = childComponent.Do(ctx, childComponent, nil); err != nil {
			return err
		}
	}
	return
}

// BaseConcurrencyComponent 并发基础组件
type BaseConcurrencyComponent struct {
	// 合成复用基础组件
	BaseComponent
	// 当前组件是否有并发子组件
	HasChildConcurrencyComponents bool
	// 并发子组件列表
	ChildConcurrencyComponents []Component
	// wg 对象
	*sync.WaitGroup
	// 当前组件业务执行结果channel
	logicResChan chan interface{}
	// 当前组件执行过程中的错误信息
	Err error
}

// Remove 移除一个子组件
func (bc *BaseConcurrencyComponent) Remove(c Component) (err error) {
	if len(bc.ChildComponents) == 0 {
		return
	}
	for k, childComponent := range bc.ChildComponents {
		if c == childComponent {
			fmt.Println(runFuncName(), "移除:", reflect.TypeOf(childComponent))
			bc.ChildComponents = append(bc.ChildComponents[:k], bc.ChildComponents[k+1:]...)
		}
	}
	for k, childComponent := range bc.ChildConcurrencyComponents {
		if c == childComponent {
			fmt.Println(runFuncName(), "移除:", reflect.TypeOf(childComponent))
			bc.ChildConcurrencyComponents = append(bc.ChildComponents[:k], bc.ChildComponents[k+1:]...)
		}
	}
	return
}

// MountConcurrency 挂载一个并发子组件
func (bc *BaseConcurrencyComponent) MountConcurrency(c Component, components ...Component) (err error) {
	bc.HasChildConcurrencyComponents = true
	bc.ChildConcurrencyComponents = append(bc.ChildConcurrencyComponents, c)
	if len(components) == 0 {
		return
	}
	bc.ChildConcurrencyComponents = append(bc.ChildConcurrencyComponents, components...)
	return
}

// ChildsDo 执行子组件
func (bc *BaseConcurrencyComponent) ChildsDo(ctx *Context) (err error) {
	if bc.WaitGroup == nil {
		bc.WaitGroup = &sync.WaitGroup{}
	}
	// 执行并发子组件
	for _, childComponent := range bc.ChildConcurrencyComponents {
		bc.WaitGroup.Add(1)
		go childComponent.Do(ctx, childComponent, bc.WaitGroup)
	}
	// 执行子组件
	for _, childComponent := range bc.ChildComponents {
		if err = childComponent.Do(ctx, childComponent, nil); err != nil {
			return err
		}
	}
	if bc.HasChildConcurrencyComponents {
		// 等待并发组件执行结果
		bc.WaitGroup.Wait()
	}
	return
}

// Do 执行子组件
// ctx 业务上下文
// currentConponent 当前组件
// wg 父组件的waitgroup对象
func (bc *BaseConcurrencyComponent) Do(ctx *Context, currentConponent Component, wg *sync.WaitGroup) (err error) {
	defer wg.Done()
	// 初始化并发子组件channel
	if bc.logicResChan == nil {
		bc.logicResChan = make(chan interface{}, 1)
	}

	go currentConponent.BusinessLogicDo(bc.logicResChan)

	select {
	// 等待业务执行结果
	case <-bc.logicResChan:
		// 业务执行结果
		fmt.Println(runFuncName(), "bc.BusinessLogicDo wait.done...")
		break
	// 超时等待
	case <-ctx.TimeoutCtx.Done():
		// 超时退出
		fmt.Println(runFuncName(), "bc.BusinessLogicDo timeout...")
		bc.Err = ErrConcurrencyComponentTimeout
		break
	}
	// 执行子组件
	err = currentConponent.ChildsDo(ctx)
	return
}
```

```
特别说明：
本系列的一些设计模式的概念可能和原概念存在差异，因为会结合实际使用，取其精华，适当改变，灵活使用。
```

# 文章列表

- [代码模板 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/template)
- [链式调用 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/responsibility)
- [代码组件 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/composite)
- [订阅通知 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/observer)
- [客户决策 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/strategy)
- [状态变换 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/state)

> [Go设计模式实战系列 更多文章 点击此处查看](https://github.com/TIGERB/easy-tips/tree/master/go/patterns)
