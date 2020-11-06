# 订阅通知 | Go设计模式实战

> 嗯，Go设计模式实战系列，一个设计模式业务真实使用的golang系列。

## 前言

本系列主要分享，如何在我们的真实业务场景中使用设计模式。

本系列文章主要采用如下结构：

- 什么是「XX设计模式」？
- 什么真实业务场景可以使用「XX设计模式」？
- 怎么用「XX设计模式」？

虽然本文的题目叫做“订阅通知”，但是呢，本文却主要介绍「观察者模式」如何在真实业务场景中使用。是不是有些不理解？解释下：

- 原因一，「观察者模式」其实看起来像“订阅通知”
- 原因二，“订阅通知”更容易被理解

## 什么是「观察者模式」？

> 观察者观察被观察者，被观察者通知观察者

我们用“订阅通知”翻译下「观察者模式」的概念，结果：

> “订阅者订阅主题，主题通知订阅者”

是不是容易理解多了，我们再来拆解下这句话，得到：

- 两个对象
    + 被观察者 -> 主题
    + 观察者 -> 订阅者
- 两个动作
    + 订阅 -> 订阅者**订阅**主题
    + 通知 -> 主题发生变动**通知**订阅者

观察者模式的优势：

- 高内聚 -> 不同业务代码变动互不影响
- 可复用 -> 新的业务(就是新的订阅者)订阅不同接口(主题，就是这里的接口)
- 极易扩展 -> 新增接口(就是新增主题)；新增业务(就是新增订阅者)；

其实说白了，就是分布式架构中使用消息机制MQ解耦业务的优势，是不是这么一想很容易理解了。

## 什么真实业务场景可以用「观察者模式」？

> 所有发生变更，需要通知的业务场景

详细说：只要发生了某些变化，需要通知依赖了这些变化的具体事物的业务场景。

> 我们有哪些真实业务场景可以用「观察者模式」呢？

比如，订单逆向流，也就是订单成立之后的各种取消操作(本文不讨论售后)，主要有如下取消类型：

|订单取消类型|
|-------|
|未支付取消订单|
|超时关单|
|已支付取消订单|
|取消发货单|
|拒收|

在触发这些**取消操作**都要进行各种各样的子操作，显而易见不同的**取消操作**所涉及的子操作是存在交集的。其次，已支付取消订单的子操作应该是所有订单取消类型最全的，其他类型的复用代码即可，除了分装成函数片段，还有什么更好的封装方式吗？答案：「观察者模式」。

接着我们来分析下订单逆向流业务中的**变**与**不变**：

- 变
    + 新增取消类型
    + 新增子操作
    + 修改某个子操作的逻辑
    + 取消类型和子操作的对应关系
- 不变
    + 已存在的取消类型
    + 已存在的子操作(在外界看来)



## 怎么用「观察者模式」？

关于怎么用，完全可以生搬硬套我总结的使用设计模式的四个步骤：

- 业务梳理
- 业务流程图
- 代码建模
- 代码demo

#### 业务梳理

```
注：本文于单体架构背景探讨业务的实现过程，简单容易理解。
```

第一步，梳理出所有存在的的逆向业务的子操作，如下：

|所有子操作|
|-------|
|修改订单状态|
|记录订单状态变更日志|
|退优惠券|
|还优惠活动资格|
|还库存|
|还礼品卡|
|退钱包余额|
|修改发货单状态|
|记录发货单状态变更日志|
|生成退款单|
|生成发票-红票|
|发邮件|
|发短信|
|发微信消息|

第二步，找到不同订单取消类型和这些子操作的关系，如下：

订单取消类型(“主题”)(被观察者)|子操作(“订阅者”)(观察者)
-------|-------
取消未支付订单|-
-|修改订单状态
-|记录订单状态变更日志
-|退优惠券
-|还优惠活动资格
-|还库存
超时关单|-
-|修改订单状态
-|记录订单状态变更日志
-|退优惠券
-|还优惠活动资格
-|还库存
-|发邮件
-|发短信
-|发微信消息
已支付取消订单(未生成发货单)|-
-|修改订单状态
-|记录订单状态变更日志
-|还优惠活动资格(看情况)
-|还库存
-|还礼品卡
-|退钱包余额
-|生成退款单
-|生成发票-红票
-|发邮件
-|发短信
-|发微信消息
取消发货单(未发货)|-
-|修改订单状态
-|记录订单状态变更日志
-|修改发货单状态
-|记录发货单状态变更日志
-|还库存
-|还礼品卡
-|退钱包余额
-|生成退款单
-|生成发票-红票
-|发邮件
-|发短信
-|发微信消息
拒收|-
-|修改订单状态
-|记录订单状态变更日志
-|修改发货单状态
-|记录发货单状态变更日志
-|还库存
-|还礼品卡
-|退钱包余额
-|生成退款单
-|生成发票-红票
-|发邮件
-|发短信
-|发微信消息


> 注：流程不一定完全准确、全面。

结论：

- 不同的订单取消类型的子操作存在交集，子操作可被复用。
- 子操作可被看作“订阅者”(也就是观察者)
- 订单取消类型可被看作是“主题”(也就是被观察者)
- 不同子操作(“订阅者”)(观察者)**订阅**订单取消类型(“主题”)(被观察者)
- 订单取消类型(“主题”)(被观察者)**通知**子操作(“订阅者”)(观察者)

#### 业务流程图

我们通过梳理的文本业务流程得到了如下的业务流程图：

```
注：本文于单体架构背景探讨业务的实现过程，简单容易理解。
```

![](http://cdn.tigerb.cn/20200410131427.png)

#### 代码建模

「观察者模式」的核心是两个接口：

- “主题”(被观察者)接口`Observable`
    +  抽象方法`Attach`: 增加“订阅者”
    +  抽象方法`Detach`: 删除“订阅者”
    +  抽象方法`Notify`: 通知“订阅者”
- “订阅者”(观察者)接口`ObserverInterface`
    +  抽象方法`Do`: 自身的业务

订单逆向流的业务下，我们需要实现这两个接口:

- 具体订单取消的动作实现“主题”接口`Observable`
- 子逻辑实现“订阅者”接口`ObserverInterface`

伪代码如下：

```
// ------------这里实现一个具体的“主题”------------

具体订单取消的动作实现“主题”(被观察者)接口`Observable`。得到一个具体的“主题”:

- 订单取消的动作的“主题”结构体`ObservableConcrete`
    +  成员属性`observerList []ObserverInterface`:订阅者列表
    +  具体方法`Attach`: 增加子逻辑
    +  具体方法`Detach`: 删除子逻辑
    +  具体方法`Notify`: 通知子逻辑

// ------------这里实现所有具体的“订阅者”------------

子逻辑实现“订阅者”接口`ObserverInterface`:

- 具体“订阅者”也就是子逻辑`OrderStatus`
    +  实现方法`Do`: 修改订单状态
- 具体“订阅者”也就是子逻辑`OrderStatusLog`
    +  实现方法`Do`: 记录订单状态变更日志
- 具体“订阅者”也就是子逻辑`CouponRefund`
    +  实现方法`Do`: 退优惠券
- 具体“订阅者”也就是子逻辑`PromotionRefund`
    +  实现方法`Do`: 还优惠活动资格
- 具体“订阅者”也就是子逻辑`StockRefund`
    +  实现方法`Do`: 还库存
- 具体“订阅者”也就是子逻辑`GiftCardRefund`
    +  实现方法`Do`: 还礼品卡
- 具体“订阅者”也就是子逻辑`WalletRefund`
    +  实现方法`Do`: 退钱包余额
- 具体“订阅者”也就是子逻辑`DeliverBillStatus`
    +  实现方法`Do`: 修改发货单状态
- 具体“订阅者”也就是子逻辑`DeliverBillStatusLog`
    +  实现方法`Do`: 记录发货单状态变更日志
- 具体“订阅者”也就是子逻辑`Refund`
    +  实现方法`Do`: 生成退款单
- 具体“订阅者”也就是子逻辑`Invoice`
    +  实现方法`Do`: 生成发票-红票
- 具体“订阅者”也就是子逻辑`Email`
    +  实现方法`Do`: 发邮件
- 具体“订阅者”也就是子逻辑`Sms`
    +  实现方法`Do`: 发短信
- 具体“订阅者”也就是子逻辑`WechatNotify`
    +  实现方法`Do`: 发微信消息
```

同时得到了我们的UML图：

![](http://cdn.tigerb.cn/20200411181215.jpg)

#### 代码demo

```go
package main

//------------------------------------------------------------
//我的代码没有`else`系列
//观察者模式
//@auhtor TIGERB<https://github.com/TIGERB>
//------------------------------------------------------------

import (
	"fmt"
	"reflect"
	"runtime"
)

// Observable 被观察者
type Observable interface {
	Attach(observer ...ObserverInterface) Observable
	Detach(observer ObserverInterface) Observable
	Notify() error
}

// ObservableConcrete 一个具体的 订单状态变化的被观察者
type ObservableConcrete struct {
	observerList []ObserverInterface
}

// Attach 注册观察者
// @param $observer ObserverInterface 观察者列表
func (o *ObservableConcrete) Attach(observer ...ObserverInterface) Observable {
	o.observerList = append(o.observerList, observer...)
	return o
}

// Detach 注销观察者
// @param $observer ObserverInterface 待注销的观察者
func (o *ObservableConcrete) Detach(observer ObserverInterface) Observable {
	if len(o.observerList) == 0 {
		return o
	}
	for k, observerItem := range o.observerList {
		if observer == observerItem {
			fmt.Println(runFuncName(), "注销:", reflect.TypeOf(observer))
			o.observerList = append(o.observerList[:k], o.observerList[k+1:]...)
		}
	}
	return o
}

// Notify 通知观察者
func (o *ObservableConcrete) Notify() (err error) {
	// code ...
	for _, observer := range o.observerList {
		if err = observer.Do(o); err != nil {
			return err
		}
	}
	return nil
}

// ObserverInterface 定义一个观察者的接口
type ObserverInterface interface {
	// 自身的业务
	Do(o Observable) error
}

// OrderStatus 修改订单状态
type OrderStatus struct {
}

// Do 具体业务
func (observer *OrderStatus) Do(o Observable) (err error) {
	// code...
	fmt.Println(runFuncName(), "修改订单状态...")
	return
}

// OrderStatusLog 记录订单状态变更日志
type OrderStatusLog struct {
}

// Do 具体业务
func (observer *OrderStatusLog) Do(o Observable) (err error) {
	// code...
	fmt.Println(runFuncName(), "记录订单状态变更日志...")
	return
}

// CouponRefund 退优惠券
type CouponRefund struct {
}

// Do 具体业务
func (observer *CouponRefund) Do(o Observable) (err error) {
	// code...
	fmt.Println(runFuncName(), "退优惠券...")
	return
}

// PromotionRefund 还优惠活动资格
type PromotionRefund struct {
}

// Do 具体业务
func (observer *PromotionRefund) Do(o Observable) (err error) {
	// code...
	fmt.Println(runFuncName(), "还优惠活动资格...")
	return
}

// StockRefund 还库存
type StockRefund struct {
}

// Do 具体业务
func (observer *StockRefund) Do(o Observable) (err error) {
	// code...
	fmt.Println(runFuncName(), "还库存...")
	return
}

// GiftCardRefund 还礼品卡
type GiftCardRefund struct {
}

// Do 具体业务
func (observer *GiftCardRefund) Do(o Observable) (err error) {
	// code...
	fmt.Println(runFuncName(), "还礼品卡...")
	return
}

// WalletRefund 退钱包余额
type WalletRefund struct {
}

// Do 具体业务
func (observer *WalletRefund) Do(o Observable) (err error) {
	// code...
	fmt.Println(runFuncName(), "退钱包余额...")
	return
}

// DeliverBillStatus 修改发货单状态
type DeliverBillStatus struct {
}

// Do 具体业务
func (observer *DeliverBillStatus) Do(o Observable) (err error) {
	// code...
	fmt.Println(runFuncName(), "修改发货单状态...")
	return
}

// DeliverBillStatusLog 记录发货单状态变更日志
type DeliverBillStatusLog struct {
}

// Do 具体业务
func (observer *DeliverBillStatusLog) Do(o Observable) (err error) {
	// code...
	fmt.Println(runFuncName(), "记录发货单状态变更日志...")
	return
}

// Refund 生成退款单
type Refund struct {
}

// Do 具体业务
func (observer *Refund) Do(o Observable) (err error) {
	// code...
	fmt.Println(runFuncName(), "生成退款单...")
	return
}

// Invoice 生成发票-红票
type Invoice struct {
}

// Do 具体业务
func (observer *Invoice) Do(o Observable) (err error) {
	// code...
	fmt.Println(runFuncName(), "生成发票-红票...")
	return
}

// Email 发邮件
type Email struct {
}

// Do 具体业务
func (observer *Email) Do(o Observable) (err error) {
	// code...
	fmt.Println(runFuncName(), "发邮件...")
	return
}

// Sms 发短信
type Sms struct {
}

// Do 具体业务
func (observer *Sms) Do(o Observable) (err error) {
	// code...
	fmt.Println(runFuncName(), "发短信...")
	return
}

// WechatNotify 发微信消息
type WechatNotify struct {
}

// Do 具体业务
func (observer *WechatNotify) Do(o Observable) (err error) {
	// code...
	fmt.Println(runFuncName(), "发微信消息...")
	return
}

// 客户端调用
func main() {

	// 创建 未支付取消订单 “主题”
	fmt.Println("----------------------- 未支付取消订单 “主题”")
	orderUnPaidCancelSubject := &ObservableConcrete{}
	orderUnPaidCancelSubject.Attach(
		&OrderStatus{},
		&OrderStatusLog{},
		&CouponRefund{},
		&PromotionRefund{},
		&StockRefund{},
	)
	orderUnPaidCancelSubject.Notify()

	// 创建 超时关单 “主题”
	fmt.Println("----------------------- 超时关单 “主题”")
	orderOverTimeSubject := &ObservableConcrete{}
	orderOverTimeSubject.Attach(
		&OrderStatus{},
		&OrderStatusLog{},
		&CouponRefund{},
		&PromotionRefund{},
		&StockRefund{},
		&Email{},
		&Sms{},
		&WechatNotify{},
	)
	orderOverTimeSubject.Notify()

	// 创建 已支付取消订单 “主题”
	fmt.Println("----------------------- 已支付取消订单 “主题”")
	orderPaidCancelSubject := &ObservableConcrete{}
	orderPaidCancelSubject.Attach(
		&OrderStatus{},
		&OrderStatusLog{},
		&CouponRefund{},
		&PromotionRefund{},
		&StockRefund{},
		&GiftCardRefund{},
		&WalletRefund{},
		&Refund{},
		&Invoice{},
		&Email{},
		&Sms{},
		&WechatNotify{},
	)
	orderPaidCancelSubject.Notify()

	// 创建 取消发货单 “主题”
	fmt.Println("----------------------- 取消发货单 “主题”")
	deliverBillCancelSubject := &ObservableConcrete{}
	deliverBillCancelSubject.Attach(
		&OrderStatus{},
		&OrderStatusLog{},
		&DeliverBillStatus{},
		&DeliverBillStatusLog{},
		&StockRefund{},
		&GiftCardRefund{},
		&WalletRefund{},
		&Refund{},
		&Invoice{},
		&Email{},
		&Sms{},
		&WechatNotify{},
	)
	deliverBillCancelSubject.Notify()

	// 创建 拒收 “主题”
	fmt.Println("----------------------- 拒收 “主题”")
	deliverBillRejectSubject := &ObservableConcrete{}
	deliverBillRejectSubject.Attach(
		&OrderStatus{},
		&OrderStatusLog{},
		&DeliverBillStatus{},
		&DeliverBillStatusLog{},
		&StockRefund{},
		&GiftCardRefund{},
		&WalletRefund{},
		&Refund{},
		&Invoice{},
		&Email{},
		&Sms{},
		&WechatNotify{},
	)
	deliverBillRejectSubject.Notify()

	// 未来可以快速的根据业务的变化 创建新的主题 从而快速构建新的业务接口
	fmt.Println("----------------------- 未来的扩展...")

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
[Running] go run "../easy-tips/go/src/patterns/observer/observer.go"
----------------------- 未支付取消订单 “主题”
main.(*OrderStatus).Do 修改订单状态...
main.(*OrderStatusLog).Do 记录订单状态变更日志...
main.(*CouponRefund).Do 退优惠券...
main.(*PromotionRefund).Do 还优惠活动资格...
main.(*StockRefund).Do 还库存...
----------------------- 超时关单 “主题”
main.(*OrderStatus).Do 修改订单状态...
main.(*OrderStatusLog).Do 记录订单状态变更日志...
main.(*CouponRefund).Do 退优惠券...
main.(*PromotionRefund).Do 还优惠活动资格...
main.(*StockRefund).Do 还库存...
main.(*Email).Do 发邮件...
main.(*Sms).Do 发短信...
main.(*WechatNotify).Do 发微信消息...
----------------------- 已支付取消订单 “主题”
main.(*OrderStatus).Do 修改订单状态...
main.(*OrderStatusLog).Do 记录订单状态变更日志...
main.(*CouponRefund).Do 退优惠券...
main.(*PromotionRefund).Do 还优惠活动资格...
main.(*StockRefund).Do 还库存...
main.(*GiftCardRefund).Do 还礼品卡...
main.(*WalletRefund).Do 退钱包余额...
main.(*Refund).Do 生成退款单...
main.(*Invoice).Do 生成发票-红票...
main.(*Email).Do 发邮件...
main.(*Sms).Do 发短信...
main.(*WechatNotify).Do 发微信消息...
----------------------- 取消发货单 “主题”
main.(*OrderStatus).Do 修改订单状态...
main.(*OrderStatusLog).Do 记录订单状态变更日志...
main.(*DeliverBillStatus).Do 修改发货单状态...
main.(*DeliverBillStatusLog).Do 记录发货单状态变更日志...
main.(*StockRefund).Do 还库存...
main.(*GiftCardRefund).Do 还礼品卡...
main.(*WalletRefund).Do 退钱包余额...
main.(*Refund).Do 生成退款单...
main.(*Invoice).Do 生成发票-红票...
main.(*Email).Do 发邮件...
main.(*Sms).Do 发短信...
main.(*WechatNotify).Do 发微信消息...
----------------------- 拒收 “主题”
main.(*OrderStatus).Do 修改订单状态...
main.(*OrderStatusLog).Do 记录订单状态变更日志...
main.(*DeliverBillStatus).Do 修改发货单状态...
main.(*DeliverBillStatusLog).Do 记录发货单状态变更日志...
main.(*StockRefund).Do 还库存...
main.(*GiftCardRefund).Do 还礼品卡...
main.(*WalletRefund).Do 退钱包余额...
main.(*Refund).Do 生成退款单...
main.(*Invoice).Do 生成发票-红票...
main.(*Email).Do 发邮件...
main.(*Sms).Do 发短信...
main.(*WechatNotify).Do 发微信消息...

```

## 结语

最后总结下，「观察者模式」抽象过程的核心是：

- 被依赖的“主题”
- 被通知的“订阅者”
- “订阅者”按需**订阅**“主题”
- “主题”变化**通知**“订阅者”

```
特别说明：
1. 我的代码没有`else`，只是一个在代码合理设计的情况下自然而然无限接近或者达到的结果，并不是一个硬性的目标，务必较真。
2. 本系列的一些设计模式的概念可能和原概念存在差异，因为会结合实际使用，取其精华，适当改变，灵活使用。
3. 观察者模式与订阅通知实际还是有差异，本文均加上了双引号。订阅通知：订阅方不是直接依赖主题方(联想下mq等消息中间件的使用)；而观察者模式：观察者是直接依赖了被观察者，从上面的代码我们也可以清晰的看出来这个差异。
```

# 文章列表

- [代码模板 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/template)
- [链式调用 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/responsibility)
- [代码组件 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/composite)
- [订阅通知 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/observer)
- [客户决策 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/strategy)
- [状态变换 | Go设计模式实战](https://github.com/TIGERB/easy-tips/tree/master/go/patterns/state)

> [Go设计模式实战系列 更多文章 点击此处查看](https://github.com/TIGERB/easy-tips/tree/master/go/patterns)

