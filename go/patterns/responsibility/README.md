# 链式调用 | Go设计模式实战

> 嗯，Go设计模式实战系列，一个设计模式业务真实使用的golang系列。

## 前言

本系列主要分享，如何在我们的真实业务场景中使用设计模式。

本系列文章主要采用如下结构：

- 什么是「XX设计模式」？
- 什么真实业务场景可以使用「XX设计模式」？
- 怎么用「XX设计模式」？

本文主要介绍「责任链模式」如何在真实业务场景中使用。

## 什么是「责任链模式」？

> 首先把一系列业务按职责划分成不同的对象，接着把这一系列对象构成一个链，然后在这一系列对象中传递请求对象，直到被处理为止。

我们从概念中可以看出责任链模式有如下明显的优势：

- 按职责划分：解耦
- 对象链：逻辑清晰

但是有一点`直到被处理为止`，代表最终只会被一个实际的业务对象执行了实际的业务逻辑，明显适用的场景并不多。但是除此之外，上面的那两点优势还是让人很心动，所以，为了适用于目前所接触的绝大多数业务场景，把概念进行了简单的调整，如下：

> 首先把一系列业务按职责划分成不同的对象，接着把这一系列对象构成一个链，直到“链路结束”为止。(结束：异常结束，或链路执行完毕结束)

简单的`直到“链路结束”为止`转换可以让我们把责任链模式适用于任何复杂的业务场景。

以下是责任链模式的具体优势：

- 直观：一眼可观的业务调用过程
- 无限扩展：可无限扩展的业务逻辑
- 高度封装：复杂业务代码依然高度封装
- 极易被修改：复杂业务代码下修改代码只需要专注对应的业务类(结构体)文件即可，以及极易被调整的业务执行顺序

## 什么真实业务场景可以用「责任链模式(改)」？

满足如下要求的场景:

> 业务极度复杂的所有场景

任何杂乱无章的业务代码，都可以使用责任链模式(改)去重构、设计。

> 我们有哪些真实业务场景可以用「责任链模式(改)」呢？

比如电商系统的下单接口，随着业务发展不断的发展，该接口会充斥着各种各样的业务逻辑。

## 怎么用「责任链模式(改)」？

关于怎么用，完全可以生搬硬套我总结的使用设计模式的四个步骤：

- 业务梳理
- 业务流程图
- 代码建模
- 代码demo

#### 业务梳理

步骤|逻辑
-------|-------
1|参数校验
2|获取地址信息
3|地址信息校验
4|获取购物车数据
5|获取商品库存信息
6|商品库存校验
7|获取优惠信息
8|获取运费信息
9|使用优惠信息
10|扣库存
11|清理购物车
12|写订单表
13|写订单商品表
14|写订单优惠信息表
XX|以及未来会增加的逻辑...

业务的不断发展变化的：

- 新的业务被增加
- 旧的业务被修改

比如增加的新的业务，订金预售：

- 在`4|获取购物车数据`后，需要校验商品参见订金预售活动的有效性等逻辑。
- 等等逻辑

> 注：流程不一定完全准确

#### 业务流程图

我们通过梳理的文本业务流程得到了如下的业务流程图：

![](http://cdn.tigerb.cn/20200327205310.png)

#### 代码建模

责任链模式主要类主要包含如下特性：

- 成员属性
	+ `nextHandler`: 下一个等待被调用的对象实例 -> 稳定不变的
- 成员方法
	+ `SetNext`: 把下一个对象的实例绑定到当前对象的`nextHandler`属性上 -> 稳定不变的
	+ `Do`: 当前对象业务逻辑入口 -> 变化的
	+ `Run`: 调用当前对象的`Do`，`nextHandler`不为空则调用`nextHandler.Do` -> 稳定不变的

套用到下单接口伪代码实现如下：
```
一个父类(抽象类)：

- 成员属性
	+ `nextHandler`: 下一个等待被调用的对象实例
- 成员方法
	+ 实体方法`SetNext`: 实现把下一个对象的实例绑定到当前对象的`nextHandler`属性上
	+ 抽象方法`Do`: 当前对象业务逻辑入口
	+ 实体方法`Run`: 实现调用当前对象的`Do`，`nextHandler`不为空则调用`nextHandler.Do`

子类一(参数校验)
- 继承抽象类父类
- 实现抽象方法`Do`：具体的参数校验逻辑

子类二(获取地址信息)
- 继承抽象类父类
- 实现抽象方法`Do`：具体获取地址信息的逻辑

子类三(获取购物车数据)
- 继承抽象类父类
- 实现抽象方法`Do`：具体获取购物车数据的逻辑

......略

子类X(以及未来会增加的逻辑)
- 继承抽象类父类
- 实现抽象方法`Do`：以及未来会增加的逻辑
```

但是，golang里没有的继承的概念，要复用成员属性`nextHandler`、成员方法`SetNext`、成员方法`Run`怎么办呢？我们使用`合成复用`的特性变相达到“继承复用”的目的，如下：

```
一个接口(interface)：

- 抽象方法`SetNext`: 待实现把下一个对象的实例绑定到当前对象的`nextHandler`属性上
- 抽象方法`Do`: 待实现当前对象业务逻辑入口
- 抽象方法`Run`: 待实现调用当前对象的`Do`，`nextHandler`不为空则调用`nextHandler.Do`

一个基础结构体：

- 成员属性
	+ `nextHandler`: 下一个等待被调用的对象实例
- 成员方法
	+ 实体方法`SetNext`: 实现把下一个对象的实例绑定到当前对象的`nextHandler`属性上
	+ 实体方法`Run`: 实现调用当前对象的`Do`，`nextHandler`不为空则调用`nextHandler.Do`

子类一(参数校验)
- 合成复用基础结构体
- 实现抽象方法`Do`：具体的参数校验逻辑

子类二(获取地址信息)
- 合成复用基础结构体
- 实现抽象方法`Do`：具体获取地址信息的逻辑

子类三(获取购物车数据)
- 合成复用基础结构体
- 实现抽象方法`Do`：具体获取购物车数据的逻辑

......略

子类X(以及未来会增加的逻辑)
- 合成复用基础结构体
- 实现抽象方法`Do`：以及未来会增加的逻辑
```

同时得到了我们的UML图：

![](http://cdn.tigerb.cn/20200328220913.jpg)

#### 代码demo

```go
package main

//---------------
//我的代码没有`else`系列
//责任链模式
//@auhtor TIGERB<https://github.com/TIGERB>
//---------------

import (
	"fmt"
	"runtime"
)

// Context Context
type Context struct {
}

// Handler 处理
type Handler interface {
	// 自身的业务
	Do(c *Context) error
	// 设置下一个对象
	SetNext(h Handler) Handler
	// 执行
	Run(c *Context) error
}

// Next 抽象出来的 可被合成复用的结构体
type Next struct {
	// 下一个对象
	nextHandler Handler
}

// SetNext 实现好的 可被复用的SetNext方法
// 返回值是下一个对象 方便写成链式代码优雅
// 例如 nullHandler.SetNext(argumentsHandler).SetNext(signHandler).SetNext(frequentHandler)
func (n *Next) SetNext(h Handler) Handler {
	n.nextHandler = h
	return h
}

// Run 执行
func (n *Next) Run(c *Context) (err error) {
	// 由于go无继承的概念 这里无法执行当前handler的Do
	// n.Do(c)
	if n.nextHandler != nil {
		// 合成复用下的变种
		// 执行下一个handler的Do
		if err = (n.nextHandler).Do(c); err != nil {
			return
		}
		// 执行下一个handler的Run
		return (n.nextHandler).Run(c)
	}
	return
}

// NullHandler 空Handler
// 由于go无继承的概念 作为链式调用的第一个载体 设置实际的下一个对象
type NullHandler struct {
	// 合成复用Next的`nextHandler`成员属性、`SetNext`成员方法、`Run`成员方法
	Next
}

// Do 空Handler的Do
func (h *NullHandler) Do(c *Context) (err error) {
	// 空Handler 这里什么也不做 只是载体 do nothing...
	return
}

// ArgumentsHandler 校验参数的handler
type ArgumentsHandler struct {
	// 合成复用Next
	Next
}

// Do 校验参数的逻辑
func (h *ArgumentsHandler) Do(c *Context) (err error) {
	fmt.Println(runFuncName(), "校验参数成功...")
	return
}

// AddressInfoHandler 地址信息handler
type AddressInfoHandler struct {
	// 合成复用Next
	Next
}

// Do 校验参数的逻辑
func (h *AddressInfoHandler) Do(c *Context) (err error) {
	fmt.Println(runFuncName(), "获取地址信息...")
	fmt.Println(runFuncName(), "地址信息校验...")
	return
}

// CartInfoHandler 获取购物车数据handler
type CartInfoHandler struct {
	// 合成复用Next
	Next
}

// Do 校验参数的逻辑
func (h *CartInfoHandler) Do(c *Context) (err error) {
	fmt.Println(runFuncName(), "获取购物车数据...")
	return
}

// StockInfoHandler 商品库存handler
type StockInfoHandler struct {
	// 合成复用Next
	Next
}

// Do 校验参数的逻辑
func (h *StockInfoHandler) Do(c *Context) (err error) {
	fmt.Println(runFuncName(), "获取商品库存信息...")
	fmt.Println(runFuncName(), "商品库存校验...")
	return
}

// PromotionInfoHandler 获取优惠信息handler
type PromotionInfoHandler struct {
	// 合成复用Next
	Next
}

// Do 校验参数的逻辑
func (h *PromotionInfoHandler) Do(c *Context) (err error) {
	fmt.Println(runFuncName(), "获取优惠信息...")
	return
}

// ShipmentInfoHandler 获取运费信息handler
type ShipmentInfoHandler struct {
	// 合成复用Next
	Next
}

// Do 校验参数的逻辑
func (h *ShipmentInfoHandler) Do(c *Context) (err error) {
	fmt.Println(runFuncName(), "获取运费信息...")
	return
}

// PromotionUseHandler 使用优惠信息handler
type PromotionUseHandler struct {
	// 合成复用Next
	Next
}

// Do 校验参数的逻辑
func (h *PromotionUseHandler) Do(c *Context) (err error) {
	fmt.Println(runFuncName(), "使用优惠信息...")
	return
}

// StockSubtractHandler 库存操作handler
type StockSubtractHandler struct {
	// 合成复用Next
	Next
}

// Do 校验参数的逻辑
func (h *StockSubtractHandler) Do(c *Context) (err error) {
	fmt.Println(runFuncName(), "扣库存...")
	return
}

// CartDelHandler 清理购物车handler
type CartDelHandler struct {
	// 合成复用Next
	Next
}

// Do 校验参数的逻辑
func (h *CartDelHandler) Do(c *Context) (err error) {
	fmt.Println(runFuncName(), "清理购物车...")
	// err = fmt.Errorf("CartDelHandler.Do fail")
	return
}

// DBTableOrderHandler 写订单表handler
type DBTableOrderHandler struct {
	// 合成复用Next
	Next
}

// Do 校验参数的逻辑
func (h *DBTableOrderHandler) Do(c *Context) (err error) {
	fmt.Println(runFuncName(), "写订单表...")
	return
}

// DBTableOrderSkusHandler 写订单商品表handler
type DBTableOrderSkusHandler struct {
	// 合成复用Next
	Next
}

// Do 校验参数的逻辑
func (h *DBTableOrderSkusHandler) Do(c *Context) (err error) {
	fmt.Println(runFuncName(), "写订单商品表...")
	return
}

// DBTableOrderPromotionsHandler 写订单优惠信息表handler
type DBTableOrderPromotionsHandler struct {
	// 合成复用Next
	Next
}

// Do 校验参数的逻辑
func (h *DBTableOrderPromotionsHandler) Do(c *Context) (err error) {
	fmt.Println(runFuncName(), "写订单优惠信息表...")
	return
}

// 获取正在运行的函数名
func runFuncName() string {
	pc := make([]uintptr, 1)
	runtime.Callers(2, pc)
	f := runtime.FuncForPC(pc[0])
	return f.Name()
}

func main() {
	// 初始化空handler
	nullHandler := &NullHandler{}

	// 链式调用 代码是不是很优雅
	// 很明显的链 逻辑关系一览无余
	nullHandler.SetNext(&ArgumentsHandler{}).
		SetNext(&AddressInfoHandler{}).
		SetNext(&CartInfoHandler{}).
		SetNext(&StockInfoHandler{}).
		SetNext(&PromotionInfoHandler{}).
		SetNext(&ShipmentInfoHandler{}).
		SetNext(&PromotionUseHandler{}).
		SetNext(&StockSubtractHandler{}).
		SetNext(&CartDelHandler{}).
		SetNext(&DBTableOrderHandler{}).
		SetNext(&DBTableOrderSkusHandler{}).
		SetNext(&DBTableOrderPromotionsHandler{})
		//无限扩展代码...

	// 开始执行业务
	if err := nullHandler.Run(&Context{}); err != nil {
		// 异常
		fmt.Println("Fail | Error:" + err.Error())
		return
	}
	// 成功
	fmt.Println("Success")
	return
}
```

代码运行结果：

```
[Running] go run "../easy-tips/go/src/patterns/responsibility/responsibility-order-submit.go"
main.(*ArgumentsHandler).Do 校验参数成功...
main.(*AddressInfoHandler).Do 获取地址信息...
main.(*AddressInfoHandler).Do 地址信息校验...
main.(*CartInfoHandler).Do 获取购物车数据...
main.(*StockInfoHandler).Do 获取商品库存信息...
main.(*StockInfoHandler).Do 商品库存校验...
main.(*PromotionInfoHandler).Do 获取优惠信息...
main.(*ShipmentInfoHandler).Do 获取运费信息...
main.(*PromotionUseHandler).Do 使用优惠信息...
main.(*StockSubtractHandler).Do 扣库存...
main.(*CartDelHandler).Do 清理购物车...
main.(*DBTableOrderHandler).Do 写订单表...
main.(*DBTableOrderSkusHandler).Do 写订单商品表...
main.(*DBTableOrderPromotionsHandler).Do 写订单优惠信息表...
Success
```

## 结语

最后总结下，「责任链模式(改)」抽象过程的核心是：

- 按职责划分：业务逻辑归类，收敛的过程。
- 对象链：把收敛之后的业务对象构成对象链，依次被执行。

```
特别说明：
1. 我的代码没有`else`，只是一个在代码合理设计的情况下自然而然无限接近或者达到的结果，并不是一个硬性的目标，务必较真。
2. 本系列的一些设计模式的概念可能和原概念存在差异，因为会结合实际使用，取其精华，适当改变，灵活使用。
```

# 文章列表

- [代码模板 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/template)
- [链式调用 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/responsibility)
- [代码组件 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/composite)
- [订阅通知 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/observer)
- [客户决策 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/strategy)
- [状态变换 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/state)

> [Go设计模式实战系列 更多文章 点击此处查看](https://github.com/TIGERB/easy-tips/tree/master/go/patterns)