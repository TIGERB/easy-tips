# 代码模板 | 我的代码没有else

> 嗯，我的代码没有`else`系列，一个设计模式业务真实使用的golang系列。

## 前言

本系列主要分享，如何在我们的真实业务场景中使用设计模式。

本系列文章主要采用如下结构：

- 什么是「XX设计模式」？
- 什么真实业务场景可以使用「XX设计模式」？
- 怎么用「XX设计模式」？

本文主要介绍「模板模式」如何在真实业务场景中使用。

## 什么是「模板模式」？

抽象类里定义好**算法的执行步骤**和**具体算法**，以及可能发生变化的算法定义为**抽象方法**。不同的子类继承该抽象类，并实现父类的抽象方法。

模板模式的优势：

- 不变的算法被继承复用：不变的部分高度封装、复用。
- 变化的算法子类继承并具体实现：变化的部分子类只需要具体实现抽象的部分即可，方便扩展，且可无限扩展。

## 什么真实业务场景可以用「模板模式」？

满足如下要求的所有场景:

> 算法执行的步骤是稳定**不变的**，但是具体的某些算法可能存在**变**化的场景。

怎么理解，举个例子：`比如说你煮个面，必然需要先烧水，水烧开之后再放面进去`，以上的流程我们称之为`煮面过程`。可知：这个`煮面过程`的步骤是稳定不变的，但是在不同的环境烧水的方式可能不尽相同，也许有的人用天然气烧水、有的人用电磁炉烧水、有的人用柴火烧水，等等。我们可以得到以下结论：

- `煮面过程`的步骤是稳定不变的
- `煮面过程`的烧水方式是可变的

> 我们有哪些真实业务场景可以用「模板模式」呢？

比如抽奖系统的抽奖接口，为什么：

- 抽奖的步骤是稳定不变的 -> **不变的**算法执行步骤
- 不同抽奖类型活动在某些逻辑处理方式可能不同 -> **变的**某些算法

## 怎么用「模板模式」？

关于怎么用，完全可以生搬硬套我总结的使用设计模式的四个步骤：

- 业务梳理
- 业务流程图
- 代码建模
- 代码demo

#### 业务梳理

我通过历史上接触过的各种抽奖场景（红包雨、糖果雨、打地鼠、大转盘(九宫格)、考眼力、答题闯关、游戏闯关、支付刮刮乐、积分刮刮乐等等），按照真实业务需求梳理了以下抽奖业务抽奖接口的大致文本流程。

了解具体业务请点击[《通用抽奖工具之需求分析 | SkrShop》](http://tigerb.cn/2019/12/23/skr-lottery/)

主步骤|主逻辑|抽奖类型|子步骤|子逻辑
---|-------|---|-------|-------
1|校验活动编号(serial_no)是否存在、并获取活动信息|-|-|-
2|校验活动、场次是否正在进行|-|-|-
3|其他参数校验(**不同活动类型实现不同**)|-|-|-
4|活动抽奖次数校验(同时扣减)|-|-|-
5|活动是否需要消费积分|-|-|-
6|场次抽奖次数校验(同时扣减)|-|-|-
7|获取场次奖品信息|-|-|-
8|获取node奖品信息(**不同活动类型实现不同**)|**按时间抽奖类型**|1|do nothing(抽取该场次的奖品即可，无需其他逻辑)
8||**按抽奖次数抽奖类型**|1|判断是该用户第几次抽奖
8|||2|获取对应node的奖品信息
8|||3|复写原所有奖品信息(抽取该node节点的奖品)
8||**按数额范围区间抽奖**|1|判断属于哪个数额区间
8|||2|获取对应node的奖品信息
8|||3|复写原所有奖品信息(抽取该node节点的奖品)
9|抽奖|-|-|-
10|奖品数量判断|-|-|-
11|组装奖品信息|-|-|-

> 注：流程不一定完全准确

结论：

- `主逻辑`是稳定不变的
- `其他参数校验`和`获取node奖品信息`的算法是可变的

#### 业务流程图

我们通过梳理的文本业务流程得到了如下的业务流程图：

![](http://cdn.tigerb.cn/20200325205347.png)

#### 代码建模

通过上面的分析我们可以得到：

```
一个抽象类
- 具体共有方法`Run`，里面定义了算法的执行步骤
- 具体私有方法，不会发生变化的具体方法
- 抽象方法，会发生变化的方法

子类一(按时间抽奖类型)
- 继承抽象类父类
- 实现抽象方法

子类二(按抽奖次数抽奖类型)
- 继承抽象类父类
- 实现抽象方法

子类三(按数额范围区间抽奖)
- 继承抽象类父类
- 实现抽象方法
```

但是golang里面没有继承的概念，我们就把对抽象类里抽象方法的依赖转化成对接口`interface`里抽象方法的依赖，同时也可以利用`合成复用`的方式“继承”模板:

```
抽象行为的接口`BehaviorInterface`(包含如下需要实现的方法)
- 其他参数校验的方法`checkParams`
- 获取node奖品信息的方法`getPrizesByNode`

抽奖结构体类
- 具体共有方法`Run`，里面定义了算法的执行步骤
- 具体私有方法`checkParams` 里面的逻辑实际依赖的接口BehaviorInterface.checkParams(ctx)的抽象方法
- 具体私有方法`getPrizesByNode` 里面的逻辑实际依赖的接口BehaviorInterface.getPrizesByNode(ctx)的抽象方法
- 其他具体私有方法，不会发生变化的具体方法

实现`BehaviorInterface`的结构体一(按时间抽奖类型)
- 实现接口方法

实现`BehaviorInterface`的结构体二(按抽奖次数抽奖类型)
- 实现接口方法

实现`BehaviorInterface`的结构体三(按数额范围区间抽奖)
- 实现接口方法
```

同时得到了我们的UML图：

![](http://cdn.tigerb.cn/20200326201327.jpg)

#### 代码demo

```go
package main

import (
	"fmt"
	"runtime"
)

//------------------------------------------------------------
//我的代码没有`else`系列
//模板模式
//@auhtor TIGERB<https://github.com/TIGERB>
//------------------------------------------------------------

const (
	// ConstActTypeTime 按时间抽奖类型
	ConstActTypeTime int32 = 1
	// ConstActTypeTimes 按抽奖次数抽奖
	ConstActTypeTimes int32 = 2
	// ConstActTypeAmount 按数额范围区间抽奖
	ConstActTypeAmount int32 = 3
)

// Context 上下文
type Context struct {
	ActInfo *ActInfo
}

// ActInfo 上下文
type ActInfo struct {
	// 活动抽奖类型1: 按时间抽奖 2: 按抽奖次数抽奖 3:按数额范围区间抽奖
	ActivityType int32
	// 其他字段略
}

// BehaviorInterface 不同抽奖类型的行为差异的抽象接口
type BehaviorInterface interface {
	// 其他参数校验(不同活动类型实现不同)
	checkParams(ctx *Context) error
	// 获取node奖品信息(不同活动类型实现不同)
	getPrizesByNode(ctx *Context) error
}

// TimeDraw 具体抽奖行为
// 按时间抽奖类型 比如红包雨
type TimeDraw struct{}

// checkParams 其他参数校验(不同活动类型实现不同)
func (draw TimeDraw) checkParams(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "按时间抽奖类型:特殊参数校验...")
	return
}

// getPrizesByNode 获取node奖品信息(不同活动类型实现不同)
func (draw TimeDraw) getPrizesByNode(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "do nothing(抽取该场次的奖品即可，无需其他逻辑)...")
	return
}

// TimesDraw 具体抽奖行为
// 按抽奖次数抽奖类型 比如答题闯关
type TimesDraw struct{}

// checkParams 其他参数校验(不同活动类型实现不同)
func (draw TimesDraw) checkParams(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "按抽奖次数抽奖类型:特殊参数校验...")
	return
}

// getPrizesByNode 获取node奖品信息(不同活动类型实现不同)
func (draw TimesDraw) getPrizesByNode(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "1. 判断是该用户第几次抽奖...")
	fmt.Println(runFuncName(), "2. 获取对应node的奖品信息...")
	fmt.Println(runFuncName(), "3. 复写原所有奖品信息(抽取该node节点的奖品)...")
	return
}

// AmountDraw 具体抽奖行为
// 按数额范围区间抽奖 比如订单金额刮奖
type AmountDraw struct{}

// checkParams 其他参数校验(不同活动类型实现不同)
func (draw *AmountDraw) checkParams(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "按数额范围区间抽奖:特殊参数校验...")
	return
}

// getPrizesByNode 获取node奖品信息(不同活动类型实现不同)
func (draw *AmountDraw) getPrizesByNode(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "1. 判断属于哪个数额区间...")
	fmt.Println(runFuncName(), "2. 获取对应node的奖品信息...")
	fmt.Println(runFuncName(), "3. 复写原所有奖品信息(抽取该node节点的奖品)...")
	return
}

// Lottery 抽奖模板
type Lottery struct {
	// 不同抽奖类型的抽象行为
	concreteBehavior BehaviorInterface
}

// Run 抽奖算法
// 稳定不变的算法步骤
func (lottery *Lottery) Run(ctx *Context) (err error) {
	// 具体方法：校验活动编号(serial_no)是否存在、并获取活动信息
	if err = lottery.checkSerialNo(ctx); err != nil {
	return err
	}

	// 具体方法：校验活动、场次是否正在进行
	if err = lottery.checkStatus(ctx); err != nil {
	return err
	}

	// ”抽象方法“：其他参数校验
	if err = lottery.checkParams(ctx); err != nil {
	return err
	}

	// 具体方法：活动抽奖次数校验(同时扣减)
	if err = lottery.checkTimesByAct(ctx); err != nil {
	return err
	}

	// 具体方法：活动是否需要消费积分
	if err = lottery.consumePointsByAct(ctx); err != nil {
	return err
	}

	// 具体方法：场次抽奖次数校验(同时扣减)
	if err = lottery.checkTimesBySession(ctx); err != nil {
	return err
	}

	// 具体方法：获取场次奖品信息
	if err = lottery.getPrizesBySession(ctx); err != nil {
	return err
	}

	// ”抽象方法“：获取node奖品信息
	if err = lottery.getPrizesByNode(ctx); err != nil {
	return err
	}

	// 具体方法：抽奖
	if err = lottery.drawPrizes(ctx); err != nil {
	return err
	}

	// 具体方法：奖品数量判断
	if err = lottery.checkPrizesStock(ctx); err != nil {
	return err
	}

	// 具体方法：组装奖品信息
	if err = lottery.packagePrizeInfo(ctx); err != nil {
	return err
	}
	return
}

// checkSerialNo 校验活动编号(serial_no)是否存在
func (lottery *Lottery) checkSerialNo(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "校验活动编号(serial_no)是否存在、并获取活动信息...")
	// 获取活动信息伪代码
	ctx.ActInfo = &ActInfo{
	// 假设当前的活动类型为按抽奖次数抽奖
	ActivityType: ConstActTypeTimes,
	}

	// 获取当前抽奖类型的具体行为
	switch ctx.ActInfo.ActivityType {
	case 1:
	// 按时间抽奖
	lottery.concreteBehavior = &TimeDraw{}
	case 2:
	// 按抽奖次数抽奖
	lottery.concreteBehavior = &TimesDraw{}
	case 3:
	// 按数额范围区间抽奖
	lottery.concreteBehavior = &AmountDraw{}
	default:
	return fmt.Errorf("不存在的活动类型")
	}
	return
}

// checkStatus 校验活动、场次是否正在进行
func (lottery *Lottery) checkStatus(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "校验活动、场次是否正在进行...")
	return
}

// checkParams 其他参数校验(不同活动类型实现不同)
// 不同场景变化的算法 转化为依赖抽象
func (lottery *Lottery) checkParams(ctx *Context) (err error) {
	// 实际依赖的接口的抽象方法
	return lottery.concreteBehavior.checkParams(ctx)
}

// checkTimesByAct 活动抽奖次数校验
func (lottery *Lottery) checkTimesByAct(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "活动抽奖次数校验...")
	return
}

// consumePointsByAct 活动是否需要消费积分
func (lottery *Lottery) consumePointsByAct(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "活动是否需要消费积分...")
	return
}

// checkTimesBySession 活动抽奖次数校验
func (lottery *Lottery) checkTimesBySession(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "活动抽奖次数校验...")
	return
}

// getPrizesBySession 获取场次奖品信息
func (lottery *Lottery) getPrizesBySession(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "获取场次奖品信息...")
	return
}

// getPrizesByNode 获取node奖品信息(不同活动类型实现不同)
// 不同场景变化的算法 转化为依赖抽象
func (lottery *Lottery) getPrizesByNode(ctx *Context) (err error) {
	// 实际依赖的接口的抽象方法
	return lottery.concreteBehavior.getPrizesByNode(ctx)
}

// drawPrizes 抽奖
func (lottery *Lottery) drawPrizes(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "抽奖...")
	return
}

// checkPrizesStock 奖品数量判断
func (lottery *Lottery) checkPrizesStock(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "奖品数量判断...")
	return
}

// packagePrizeInfo 组装奖品信息
func (lottery *Lottery) packagePrizeInfo(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "组装奖品信息...")
	return
}

func main() {
	(&Lottery{}).Run(&Context{})
}

// 获取正在运行的函数名
func runFuncName() string {
	pc := make([]uintptr, 1)
	runtime.Callers(2, pc)
	f := runtime.FuncForPC(pc[0])
	return f.Name()
}


```

以下是代码执行结果:
```
[Running] go run ".../easy-tips/go/src/patterns/template/template.go"
main.(*Lottery).checkSerialNo 校验活动编号(serial_no)是否存在、并获取活动信息...
main.(*Lottery).checkStatus 校验活动、场次是否正在进行...
main.TimesDraw.checkParams 按抽奖次数抽奖类型:特殊参数校验...
main.(*Lottery).checkTimesByAct 活动抽奖次数校验...
main.(*Lottery).consumePointsByAct 活动是否需要消费积分...
main.(*Lottery).checkTimesBySession 活动抽奖次数校验...
main.(*Lottery).getPrizesBySession 获取场次奖品信息...
main.TimesDraw.getPrizesByNode 1. 判断是该用户第几次抽奖...
main.TimesDraw.getPrizesByNode 2. 获取对应node的奖品信息...
main.TimesDraw.getPrizesByNode 3. 复写原所有奖品信息(抽取该node节点的奖品)...
main.(*Lottery).drawPrizes 抽奖...
main.(*Lottery).checkPrizesStock 奖品数量判断...
main.(*Lottery).packagePrizeInfo 组装奖品信息...
```

demo代码地址：<https://github.com/TIGERB/easy-tips/blob/master/go/src/patterns/template/template.go>

#### 代码demo2(利用golang的`合成复用`特性实现)

```go
package main

import (
	"fmt"
	"runtime"
)

//------------------------------------------------------------
//我的代码没有`else`系列
//模板模式
//@auhtor TIGERB<https://github.com/TIGERB>
//------------------------------------------------------------

const (
	// ConstActTypeTime 按时间抽奖类型
	ConstActTypeTime int32 = 1
	// ConstActTypeTimes 按抽奖次数抽奖
	ConstActTypeTimes int32 = 2
	// ConstActTypeAmount 按数额范围区间抽奖
	ConstActTypeAmount int32 = 3
)

// Context 上下文
type Context struct {
	ActInfo *ActInfo
}

// ActInfo 上下文
type ActInfo struct {
	// 活动抽奖类型1: 按时间抽奖 2: 按抽奖次数抽奖 3:按数额范围区间抽奖
	ActivityType int32
	// 其他字段略
}

// BehaviorInterface 不同抽奖类型的行为差异的抽象接口
type BehaviorInterface interface {
	// 其他参数校验(不同活动类型实现不同)
	checkParams(ctx *Context) error
	// 获取node奖品信息(不同活动类型实现不同)
	getPrizesByNode(ctx *Context) error
}

// TimeDraw 具体抽奖行为
// 按时间抽奖类型 比如红包雨
type TimeDraw struct {
	// 合成复用模板
	Lottery
}

// checkParams 其他参数校验(不同活动类型实现不同)
func (draw TimeDraw) checkParams(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "按时间抽奖类型:特殊参数校验...")
	return
}

// getPrizesByNode 获取node奖品信息(不同活动类型实现不同)
func (draw TimeDraw) getPrizesByNode(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "do nothing(抽取该场次的奖品即可，无需其他逻辑)...")
	return
}

// TimesDraw 具体抽奖行为
// 按抽奖次数抽奖类型 比如答题闯关
type TimesDraw struct {
	// 合成复用模板
	Lottery
}

// checkParams 其他参数校验(不同活动类型实现不同)
func (draw TimesDraw) checkParams(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "按抽奖次数抽奖类型:特殊参数校验...")
	return
}

// getPrizesByNode 获取node奖品信息(不同活动类型实现不同)
func (draw TimesDraw) getPrizesByNode(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "1. 判断是该用户第几次抽奖...")
	fmt.Println(runFuncName(), "2. 获取对应node的奖品信息...")
	fmt.Println(runFuncName(), "3. 复写原所有奖品信息(抽取该node节点的奖品)...")
	return
}

// AmountDraw 具体抽奖行为
// 按数额范围区间抽奖 比如订单金额刮奖
type AmountDraw struct {
	// 合成复用模板
	Lottery
}

// checkParams 其他参数校验(不同活动类型实现不同)
func (draw *AmountDraw) checkParams(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "按数额范围区间抽奖:特殊参数校验...")
	return
}

// getPrizesByNode 获取node奖品信息(不同活动类型实现不同)
func (draw *AmountDraw) getPrizesByNode(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "1. 判断属于哪个数额区间...")
	fmt.Println(runFuncName(), "2. 获取对应node的奖品信息...")
	fmt.Println(runFuncName(), "3. 复写原所有奖品信息(抽取该node节点的奖品)...")
	return
}

// Lottery 抽奖模板
type Lottery struct {
	// 不同抽奖类型的抽象行为
	ConcreteBehavior BehaviorInterface
}

// Run 抽奖算法
// 稳定不变的算法步骤
func (lottery *Lottery) Run(ctx *Context) (err error) {
	// 具体方法：校验活动编号(serial_no)是否存在、并获取活动信息
	if err = lottery.checkSerialNo(ctx); err != nil {
	return err
	}

	// 具体方法：校验活动、场次是否正在进行
	if err = lottery.checkStatus(ctx); err != nil {
	return err
	}

	// ”抽象方法“：其他参数校验
	if err = lottery.checkParams(ctx); err != nil {
	return err
	}

	// 具体方法：活动抽奖次数校验(同时扣减)
	if err = lottery.checkTimesByAct(ctx); err != nil {
	return err
	}

	// 具体方法：活动是否需要消费积分
	if err = lottery.consumePointsByAct(ctx); err != nil {
	return err
	}

	// 具体方法：场次抽奖次数校验(同时扣减)
	if err = lottery.checkTimesBySession(ctx); err != nil {
	return err
	}

	// 具体方法：获取场次奖品信息
	if err = lottery.getPrizesBySession(ctx); err != nil {
	return err
	}

	// ”抽象方法“：获取node奖品信息
	if err = lottery.getPrizesByNode(ctx); err != nil {
	return err
	}

	// 具体方法：抽奖
	if err = lottery.drawPrizes(ctx); err != nil {
	return err
	}

	// 具体方法：奖品数量判断
	if err = lottery.checkPrizesStock(ctx); err != nil {
	return err
	}

	// 具体方法：组装奖品信息
	if err = lottery.packagePrizeInfo(ctx); err != nil {
	return err
	}
	return
}

// checkSerialNo 校验活动编号(serial_no)是否存在
func (lottery *Lottery) checkSerialNo(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "校验活动编号(serial_no)是否存在、并获取活动信息...")
	return
}

// checkStatus 校验活动、场次是否正在进行
func (lottery *Lottery) checkStatus(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "校验活动、场次是否正在进行...")
	return
}

// checkParams 其他参数校验(不同活动类型实现不同)
// 不同场景变化的算法 转化为依赖抽象
func (lottery *Lottery) checkParams(ctx *Context) (err error) {
	// 实际依赖的接口的抽象方法
	return lottery.ConcreteBehavior.checkParams(ctx)
}

// checkTimesByAct 活动抽奖次数校验
func (lottery *Lottery) checkTimesByAct(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "活动抽奖次数校验...")
	return
}

// consumePointsByAct 活动是否需要消费积分
func (lottery *Lottery) consumePointsByAct(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "活动是否需要消费积分...")
	return
}

// checkTimesBySession 活动抽奖次数校验
func (lottery *Lottery) checkTimesBySession(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "活动抽奖次数校验...")
	return
}

// getPrizesBySession 获取场次奖品信息
func (lottery *Lottery) getPrizesBySession(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "获取场次奖品信息...")
	return
}

// getPrizesByNode 获取node奖品信息(不同活动类型实现不同)
// 不同场景变化的算法 转化为依赖抽象
func (lottery *Lottery) getPrizesByNode(ctx *Context) (err error) {
	// 实际依赖的接口的抽象方法
	return lottery.ConcreteBehavior.getPrizesByNode(ctx)
}

// drawPrizes 抽奖
func (lottery *Lottery) drawPrizes(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "抽奖...")
	return
}

// checkPrizesStock 奖品数量判断
func (lottery *Lottery) checkPrizesStock(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "奖品数量判断...")
	return
}

// packagePrizeInfo 组装奖品信息
func (lottery *Lottery) packagePrizeInfo(ctx *Context) (err error) {
	fmt.Println(runFuncName(), "组装奖品信息...")
	return
}

func main() {
	ctx := &Context{
	ActInfo: &ActInfo{
	ActivityType: ConstActTypeAmount,
	},
	}

	switch ctx.ActInfo.ActivityType {
	case ConstActTypeTime: // 按时间抽奖类型
	instance := &TimeDraw{}
	instance.ConcreteBehavior = instance
	instance.Run(ctx)
	case ConstActTypeTimes: // 按抽奖次数抽奖
	instance := &TimesDraw{}
	instance.ConcreteBehavior = instance
	instance.Run(ctx)
	case ConstActTypeAmount: // 按数额范围区间抽奖
	instance := &AmountDraw{}
	instance.ConcreteBehavior = instance
	instance.Run(ctx)
	default:
	// 报错
	return
	}
}

// 获取正在运行的函数名
func runFuncName() string {
	pc := make([]uintptr, 1)
	runtime.Callers(2, pc)
	f := runtime.FuncForPC(pc[0])
	return f.Name()
}

```

以下是代码执行结果:

```
[Running] go run ".../easy-tips/go/src/patterns/template/templateOther.go"
main.(*Lottery).checkSerialNo 校验活动编号(serial_no)是否存在、并获取活动信息...
main.(*Lottery).checkStatus 校验活动、场次是否正在进行...
main.(*AmountDraw).checkParams 按数额范围区间抽奖:特殊参数校验...
main.(*Lottery).checkTimesByAct 活动抽奖次数校验...
main.(*Lottery).consumePointsByAct 活动是否需要消费积分...
main.(*Lottery).checkTimesBySession 活动抽奖次数校验...
main.(*Lottery).getPrizesBySession 获取场次奖品信息...
main.(*AmountDraw).getPrizesByNode 1. 判断属于哪个数额区间...
main.(*AmountDraw).getPrizesByNode 2. 获取对应node的奖品信息...
main.(*AmountDraw).getPrizesByNode 3. 复写原所有奖品信息(抽取该node节点的奖品)...
main.(*Lottery).drawPrizes 抽奖...
main.(*Lottery).checkPrizesStock 奖品数量判断...
main.(*Lottery).packagePrizeInfo 组装奖品信息...
```

demo2代码地址：<https://github.com/TIGERB/easy-tips/blob/master/go/src/patterns/template/templateOther.go>

## 结语

最后总结下，「模板模式」抽象过程的核心是把握**不变**与**变**：

- 不变：`Run`方法里的抽奖步骤 -> `被继承复用`
- 变：不同场景下 -> `被具体实现`
	+ `checkParams`参数校验逻辑
	+ `getPrizesByNode`获取该节点奖品的逻辑

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


