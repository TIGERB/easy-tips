# 客户决策 | Go设计模式实战

> 嗯，Go设计模式实战系列，一个设计模式业务真实使用的golang系列。

## 前言

本系列主要分享，如何在我们的真实业务场景中使用设计模式。

本系列文章主要采用如下结构：

- 什么是「XX设计模式」？
- 什么真实业务场景可以使用「XX设计模式」？
- 怎么用「XX设计模式」？

本文主要介绍「策略模式」如何在真实业务场景中使用。

## 什么是「策略模式」？

「策略模式」比较简单，大家平常工作中应该经常使用到，所以本文作为复习，帮助大家温故知新。我们先来看下定义：

> 不同的算法按照统一的标准封装，客户端根据不同的场景，决策使用何种算法。

上面的概念的关键词：

- 算法：就是行为
- 标准：就是interface
- 客户端：客户端是相对的，谁调用谁就是客户端
- 场景：判断条件
- 决策：判断的过程

概念很容易理解，不多说。

「策略模式」的优势：

- 典型的高内聚：算法和算法之间完全独立、互不干扰
- 典型的松耦合：客户端依赖的是接口的抽象方法
- 沉淀：每一个封装好的算法都是这个技术团队的财富，且未来可以被轻易的修改、复用

## 什么真实业务场景可以用「策略模式」？

> 每一行代码下面的十字路口

当代码的下一步面临选择的时候都可以使用「策略模式」，我们把不同选择的算法按照统一的标准封装，得到一类算法集的过程，就是实现「策略模式」的过程。

> 我们有哪些真实业务场景可以用「策略模式」呢？

比如：

- 缓存: 使用什么样的nosql
- 存储: 使用什么样的DB
- 支付: 使用什么样的支付方式
- 等等... 

本文以**支付接口**举例，说明「策略模式」的具体使用。

## 怎么用「策略模式」？

关于怎么用，完全可以生搬硬套我总结的使用设计模式的四个步骤：

- 业务梳理
- 业务流程图
- 代码建模
- 代码demo

#### 业务梳理

我们以某团的订单支付页面为例，页面上的每一个支付选项都是一个支付策略。如下：

用户可以使用：

- 美团支付(策略)
- 微信支付(策略)
- 支付宝支付(策略)

<p align="center">
  <img src="http://cdn.tigerb.cn/20200424131625.png" style="width:50%">
</p>

用户决定使用美团支付下的银行卡支付方式的参数
<p align="center">
  <img src="http://cdn.tigerb.cn/20200424132214.png" style="width:50%">
</p>

用户决定使用支付宝网页版支付方式的参数

<p align="center">
  <img src="http://cdn.tigerb.cn/20200424132232.png" style="width:50%">
</p>

> 注：不一定完全准确。

#### 业务流程图

我们通过梳理的文本业务流程得到了如下的业务流程图：

<p align="center">
  <img src="http://cdn.tigerb.cn/20200425192752.png" style="width:100%">
</p>

> 注：流程不一定完全准确。

#### 代码建模

「策略模式」的核心是接口：

- `PaymentInterface`
    + `Pay(ctx *Context) error` 当前支付方式的支付逻辑
	+ `Refund(ctx *Context) error` 当前支付方式的退款逻辑


伪代码如下：

```
// 定义一个支付接口
- `PaymentInterface`
    + 抽象方法`Pay(ctx *Context) error`: 当前支付方式的支付逻辑
	+ 抽象方法`Refund(ctx *Context) error`: 当前支付方式的退款逻辑

// 定义具体的支付方式 实现接口`PaymentInterface`

- 具体的微信支付方式`WechatPay`
    +  实现方法`Pay`: 支付逻辑
	+  实现方法`Refund`: 支付逻辑
- 具体的支付宝支付网页版方式`AliPayWap`
    +  实现方法`Pay`: 支付逻辑
	+  实现方法`Refund`: 支付逻辑
- 具体的支付宝支付网页版方式`BankPay`
    +  实现方法`Pay`: 支付逻辑
	+  实现方法`Refund`: 支付逻辑

// 客户端代码
通过接口参数pay_type的值判断是哪种支付方式策略
```

同时得到了我们的UML图：

<p align="center">
  <img src="http://cdn.tigerb.cn/20200425151733.jpg" style="width:100%">
</p>

#### 代码demo

```go
package main

import (
	"fmt"
	"runtime"
)

//------------------------------------------------------------
//我的代码没有`else`系列
//策略模式
//@auhtor TIGERB<https://github.com/TIGERB>
//------------------------------------------------------------

const (
	// ConstWechatPay 微信支付
	ConstWechatPay = "wechat_pay"
	// ConstAliPayWap 支付宝支付 网页版
	ConstAliPayWap = "AliPayWapwap"
	// ConstBankPay 银行卡支付
	ConstBankPay = "quickbank"
)

// Context 上下文
type Context struct {
	// 用户选择的支付方式
	PayType string `json:"pay_type"`
}

// PaymentInterface 支付方式接口
type PaymentInterface interface {
	Pay(ctx *Context) error    // 支付
	Refund(ctx *Context) error // 退款
}

// WechatPay 微信支付
type WechatPay struct {
}

// Pay 当前支付方式的支付逻辑
func (p *WechatPay) Pay(ctx *Context) (err error) {
	// 当前策略的业务逻辑写这
	fmt.Println(runFuncName(), "使用微信支付...")
	return
}

// Refund 当前支付方式的支付逻辑
func (p *WechatPay) Refund(ctx *Context) (err error) {
	// 当前策略的业务逻辑写这
	fmt.Println(runFuncName(), "使用微信退款...")
	return
}

// AliPayWap 支付宝网页版
type AliPayWap struct {
}

// Pay 当前支付方式的支付逻辑
func (p *AliPayWap) Pay(ctx *Context) (err error) {
	// 当前策略的业务逻辑写这
	fmt.Println(runFuncName(), "使用支付宝网页版支付...")
	return
}

// Refund 当前支付方式的支付逻辑
func (p *AliPayWap) Refund(ctx *Context) (err error) {
	// 当前策略的业务逻辑写这
	fmt.Println(runFuncName(), "使用支付宝网页版退款...")
	return
}

// BankPay 银行卡支付
type BankPay struct {
}

// Pay 当前支付方式的支付逻辑
func (p *BankPay) Pay(ctx *Context) (err error) {
	// 当前策略的业务逻辑写这
	fmt.Println(runFuncName(), "使用银行卡支付...")
	return
}

// Refund 当前支付方式的支付逻辑
func (p *BankPay) Refund(ctx *Context) (err error) {
	// 当前策略的业务逻辑写这
	fmt.Println(runFuncName(), "使用银行卡退款...")
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
	// 相对于被调用的支付策略 这里就是支付策略的客户端

	// 业务上下文
	ctx := &Context{
		PayType: "wechat_pay",
	}

	// 获取支付方式
	var instance PaymentInterface
	switch ctx.PayType {
	case ConstWechatPay:
		instance = &WechatPay{}
	case ConstAliPayWap:
		instance = &AliPayWap{}
	case ConstBankPay:
		instance = &BankPay{}
	default:
		panic("无效的支付方式")
	}

	// 支付
	instance.Pay(ctx)
}
```

代码运行结果：

```
[Running] go run "../easy-tips/go/src/patterns/strategy/strategy.go"
main.(*WechatPay).Pay 使用微信支付...
```

## 结语

最后总结下，「策略模式」抽象过程的核心是：

在`每一行代码下面的十字路口`

- 声明标准：定义`interface`
- 封装算法：按照标准`interface`封装分支代码，得到每一个具体策略
- 构建算法集：每一个具体策略构成策略池子 -> 这就是沉淀的过程

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

