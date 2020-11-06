# 状态变换 | Go设计模式实战

> 嗯，Go设计模式实战系列，一个设计模式业务真实使用的golang系列。

## 前言

本系列主要分享，如何在我们的真实业务场景中使用设计模式。

本系列文章主要采用如下结构：

- 什么是「XX设计模式」？
- 什么真实业务场景可以使用「XX设计模式」？
- 怎么用「XX设计模式」？

本文主要介绍「状态模式」如何在真实业务场景中使用。

「状态模式」比较简单，就是算法的选取取决于于自己的内部状态。相较于「策略模式」算法的选取由用户决策变成内部状态决策，「策略模式」是用户(客户端)选择具体的算法，「状态模式」只是通过内部不同的状态选择具体的算法。

## 什么是「状态模式」？

> 不同的算法按照统一的标准封装，根据不同的**内部状态**，决策使用何种算法

### 「状态模式」和「策略模式」的区别

- 策略模式：依靠客户决策
- 状态模式：依靠内部状态决策

## 什么真实业务场景可以用「状态模式」？

> 具体算法的选取是由内部状态决定的

- 首先，内部存在多种状态
- 其次，不同的状态的业务逻辑各不相同

> 我们有哪些真实业务场景可以用「状态模式」呢？

比如，发送短信接口、限流等等。

- 短信接口
	+ 服务内部根据最优算法，实时推举出最优的短信服务商，并修改**使用何种短信服务商的状态**
- 限流
	+ 服务内部根据当前的实时流量，选择不同的限流算法，并修改**使用何种限流算法的状态**

## 怎么用「状态模式」？

关于怎么用，完全可以生搬硬套我总结的使用设计模式的四个步骤：

- 业务梳理
- 业务流程图
- 代码建模
- 代码demo

#### 业务梳理

先来看看一个短信验证码登录的界面。

<p align="center">
  <img src="http://cdn.tigerb.cn/20200522131127.png" style="width:100%">
</p>

可以得到：

- 发送短信，用户只需要输入手机号即可
- 至于短信服务使用何种短信服务商，是由短信服务自身的**当前短信服务商实例的状态**决定
- **当前短信服务商实例的状态**又是由服务自身的算法修改

#### 业务流程图

我们通过梳理的文本业务流程得到了如下的业务流程图：

<p align="center">
  <img src="http://cdn.tigerb.cn/20200522130715.png" style="width:100%">
</p>

#### 代码建模

「状态模式」的核心是：

- 一个接口:
	+ 短信服务接口`SmsServiceInterface`
- 一个实体类:
	+ 状态管理实体类`StateManager`

伪代码如下：

```
// 定义一个短信服务接口
- 接口`SmsServiceInterface`
	+ 抽象方法`Send(ctx *Context) error`发送短信的抽象方法

// 定义具体的短信服务实体类 实现接口`SmsServiceInterface`

- 实体类`ServiceProviderAliyun`
	+ 成员方法`Send(ctx *Context) error`具体的发送短信逻辑
- 实体类`ServiceProviderTencent`
	+ 成员方法`Send(ctx *Context) error`具体的发送短信逻辑
- 实体类`ServiceProviderYunpian`
	+ 成员方法`Send(ctx *Context) error`具体的发送短信逻辑

// 定义状态管理实体类`StateManager`
- 成员属性
	+ `currentProviderType ProviderType`当前使用的服务提供商类型
	+ `currentProvider SmsServiceInterface`当前使用的服务提供商实例
	+ `setStateDuration time.Duration`更新状态时间间隔
- 成员方法
	+ `initState(duration time.Duration)`初始化状态
	+ `setState(t time.Time)`设置状态
```

同时得到了我们的UML图：

<p align="center">
  <img src="http://cdn.tigerb.cn/20200527141350.jpg" style="width:100%">
</p>

#### 代码demo

```go
package main

//------------------------------------------------------------
//我的代码没有`else`系列
//状态模式
//@auhtor TIGERB<https://github.com/TIGERB>
//------------------------------------------------------------

import (
	"fmt"
	"math/rand"
	"runtime"
	"time"
)

// Context 上下文
type Context struct {
	Tel        string // 手机号
	Text       string // 短信内容
	TemplateID string // 短信模板ID
}

// SmsServiceInterface 短信服务接口
type SmsServiceInterface interface {
	Send(ctx *Context) error
}

// ServiceProviderAliyun 阿里云
type ServiceProviderAliyun struct {
}

// Send Send
func (s *ServiceProviderAliyun) Send(ctx *Context) error {
	fmt.Println(runFuncName(), "【阿里云】短信发送成功，手机号:"+ctx.Tel)
	return nil
}

// ServiceProviderTencent 腾讯云
type ServiceProviderTencent struct {
}

// Send Send
func (s *ServiceProviderTencent) Send(ctx *Context) error {
	fmt.Println(runFuncName(), "【腾讯云】短信发送成功，手机号:"+ctx.Tel)
	return nil
}

// ServiceProviderYunpian 云片
type ServiceProviderYunpian struct {
}

// Send Send
func (s *ServiceProviderYunpian) Send(ctx *Context) error {
	fmt.Println(runFuncName(), "【云片】短信发送成功，手机号:"+ctx.Tel)
	return nil
}

// 获取正在运行的函数名
func runFuncName() string {
	pc := make([]uintptr, 1)
	runtime.Callers(2, pc)
	f := runtime.FuncForPC(pc[0])
	return f.Name()
}

// ProviderType 短信服务提供商类型
type ProviderType string

const (
	// ProviderTypeAliyun 阿里云
	ProviderTypeAliyun ProviderType = "aliyun"
	// ProviderTypeTencent 腾讯云
	ProviderTypeTencent ProviderType = "tencent"
	// ProviderTypeYunpian 云片
	ProviderTypeYunpian ProviderType = "yunpian"
)

var (
	// stateManagerInstance 当前使用的服务提供商实例
	// 默认aliyun
	stateManagerInstance *StateManager
)

// StateManager 状态管理
type StateManager struct {
	// CurrentProviderType 当前使用的服务提供商类型
	// 默认aliyun
	currentProviderType ProviderType

	// CurrentProvider 当前使用的服务提供商实例
	// 默认aliyun
	currentProvider SmsServiceInterface

	// 更新状态时间间隔
	setStateDuration time.Duration
}

// initState 初始化状态
func (m *StateManager) initState(duration time.Duration) {
	// 初始化
	m.setStateDuration = duration
	m.setState(time.Now())

	// 定时器更新状态
	go func() {
		for {
			// 每一段时间后根据回调的发送成功率 计算得到当前应该使用的 厂商
			select {
			case t := <-time.NewTicker(m.setStateDuration).C:
				m.setState(t)
			}
		}
	}()
}

// setState 设置状态
// 根据短信云商回调的短信发送成功率 得到下阶段发送短信使用哪个厂商的服务
func (m *StateManager) setState(t time.Time) {
	// 这里用随机模拟
	ProviderTypeArray := [3]ProviderType{
		ProviderTypeAliyun,
		ProviderTypeTencent,
		ProviderTypeYunpian,
	}
	m.currentProviderType = ProviderTypeArray[rand.Intn(len(ProviderTypeArray))]

	switch m.currentProviderType {
	case ProviderTypeAliyun:
		m.currentProvider = &ServiceProviderAliyun{}
	case ProviderTypeTencent:
		m.currentProvider = &ServiceProviderTencent{}
	case ProviderTypeYunpian:
		m.currentProvider = &ServiceProviderYunpian{}
	default:
		panic("无效的短信服务商")
	}
	fmt.Printf("时间：%s| 变更短信发送厂商为: %s \n", t.Format("2006-01-02 15:04:05"), m.currentProviderType)
}

// getState 获取当前状态
func (m *StateManager) getState() SmsServiceInterface {
	return m.currentProvider
}

// GetState 获取当前状态
func GetState() SmsServiceInterface {
	return stateManagerInstance.getState()
}

func main() {

	// 初始化状态管理
	stateManagerInstance = &StateManager{}
	stateManagerInstance.initState(300 * time.Millisecond)

	// 模拟发送短信的接口
	sendSms := func() {
		// 发送短信
		GetState().Send(&Context{
			Tel:        "+8613666666666",
			Text:       "3232",
			TemplateID: "TYSHK_01",
		})
	}

	// 模拟用户调用发送短信的接口
	sendSms()
	time.Sleep(1 * time.Second)
	sendSms()
	time.Sleep(1 * time.Second)
	sendSms()
	time.Sleep(1 * time.Second)
	sendSms()
	time.Sleep(1 * time.Second)
	sendSms()
}
```

代码运行结果：

```
[Running] go run "./easy-tips/go/src/patterns/state/state.go"
时间：2020-05-30 18:02:37| 变更短信发送厂商为: yunpian 
main.(*ServiceProviderYunpian).Send 【云片】短信发送成功，手机号:+8613666666666
时间：2020-05-30 18:02:37| 变更短信发送厂商为: aliyun 
时间：2020-05-30 18:02:38| 变更短信发送厂商为: yunpian 
时间：2020-05-30 18:02:38| 变更短信发送厂商为: yunpian 
main.(*ServiceProviderYunpian).Send 【云片】短信发送成功，手机号:+8613666666666
时间：2020-05-30 18:02:38| 变更短信发送厂商为: tencent 
时间：2020-05-30 18:02:39| 变更短信发送厂商为: aliyun 
时间：2020-05-30 18:02:39| 变更短信发送厂商为: tencent 
main.(*ServiceProviderTencent).Send 【腾讯云】短信发送成功，手机号:+8613666666666
时间：2020-05-30 18:02:39| 变更短信发送厂商为: yunpian 
时间：2020-05-30 18:02:40| 变更短信发送厂商为: tencent 
时间：2020-05-30 18:02:40| 变更短信发送厂商为: aliyun 
main.(*ServiceProviderAliyun).Send 【阿里云】短信发送成功，手机号:+8613666666666
时间：2020-05-30 18:02:40| 变更短信发送厂商为: yunpian 
时间：2020-05-30 18:02:40| 变更短信发送厂商为: tencent 
时间：2020-05-30 18:02:41| 变更短信发送厂商为: aliyun 
时间：2020-05-30 18:02:41| 变更短信发送厂商为: yunpian 
main.(*ServiceProviderYunpian).Send 【云片】短信发送成功，手机号:+8613666666666
```

## 结语

最后总结下，「状态模式」抽象过程的核心是：

- 每一个状态映射对应行为
- 行为实现同一个接口`interface`
- 行为是内部的一个状态
- 状态是不断变化的

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

