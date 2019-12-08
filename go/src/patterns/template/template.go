package main

import (
	"fmt"
	"runtime"
)

//---------------
//叼叼的设计模式(Go享版)
//模板模式
//@auhtor TIGERB<https://github.com/TIGERB>
//---------------

// Context 上下文
type Context struct {
}

// BehaviorInterface 抽奖算法的抽象算法
type BehaviorInterface interface {
	Check(ctx *Context) error
	GetNodeByRule(ctx *Context) error
	CheckJoinLimit(ctx *Context) error
	ConsumePoints(ctx *Context) error
}

// OrderPriceBehavior 具体抽奖行为
// 订单金额刮奖
type OrderPriceBehavior struct{}

// Check Check
func (orderPrice *OrderPriceBehavior) Check(ctx *Context) error {
	fmt.Println(runFuncName())
	return nil
}

// GetNodeByRule GetNodeByRule
func (orderPrice *OrderPriceBehavior) GetNodeByRule(ctx *Context) error {
	fmt.Println(runFuncName())
	return nil
}

// CheckJoinLimit CheckJoinLimit
func (orderPrice *OrderPriceBehavior) CheckJoinLimit(ctx *Context) error {
	fmt.Println(runFuncName())
	return nil
}

// ConsumePoints ConsumePoints
func (orderPrice *OrderPriceBehavior) ConsumePoints(ctx *Context) error {
	fmt.Println(runFuncName())
	return nil
}

// TimesBehavior 具体抽奖行为
// 按抽奖次数抽奖
type TimesBehavior struct{}

// Check Check
func (times *TimesBehavior) Check(ctx *Context) error {
	fmt.Println(runFuncName())
	return nil
}

// GetNodeByRule GetNodeByRule
func (times *TimesBehavior) GetNodeByRule(ctx *Context) error {
	fmt.Println(runFuncName())
	return nil
}

// CheckJoinLimit CheckJoinLimit
func (times *TimesBehavior) CheckJoinLimit(ctx *Context) error {
	fmt.Println(runFuncName())
	return nil
}

// ConsumePoints ConsumePoints
func (times *TimesBehavior) ConsumePoints(ctx *Context) error {
	fmt.Println(runFuncName())
	return nil
}

// Lottery 抽奖模板
type Lottery struct {
	ConcreteBehavior BehaviorInterface
}

// Run 抽奖算法
func (lottery *Lottery) Run(ctx *Context) (err error) {
	if err := lottery.ConcreteBehavior.Check(ctx); err != nil {
		return err
	}
	if err := lottery.GetRule(ctx); err != nil {
		return err
	}
	if err := lottery.ConcreteBehavior.GetNodeByRule(ctx); err != nil {
		return err
	}
	if err := lottery.CheckTimes(ctx); err != nil {
		return err
	}
	if err := lottery.ConcreteBehavior.CheckJoinLimit(ctx); err != nil {
		return err
	}
	if err := lottery.ConcreteBehavior.ConsumePoints(ctx); err != nil {
		return err
	}
	if err := lottery.GetPrize(ctx); err != nil {
		return err
	}
	if err := lottery.Draw(ctx); err != nil {
		return err
	}
	if err := lottery.PackagePrizeInfo(ctx); err != nil {
		return err
	}
	fmt.Println(runFuncName())
	return nil
}

// GetRule GetRule
func (lottery *Lottery) GetRule(ctx *Context) error {
	fmt.Println(runFuncName())
	return nil
}

// CheckTimes CheckTimes
func (lottery *Lottery) CheckTimes(ctx *Context) error {
	fmt.Println(runFuncName())
	return nil
}

// GetPrize GetPrize
func (lottery *Lottery) GetPrize(ctx *Context) error {
	fmt.Println(runFuncName())
	return nil
}

// Draw Draw
func (lottery *Lottery) Draw(ctx *Context) error {
	fmt.Println(runFuncName())
	return nil
}

// PackagePrizeInfo PackagePrizeInfo
func (lottery *Lottery) PackagePrizeInfo(ctx *Context) error {
	fmt.Println(runFuncName())
	return nil
}

func main() {
	// 订单金额刮奖
	(&Lottery{
		ConcreteBehavior: &OrderPriceBehavior{},
	}).Run(&Context{})

	// 按抽奖次数抽奖
	(&Lottery{
		ConcreteBehavior: &TimesBehavior{},
	}).Run(&Context{})
}

// 获取正在运行的函数名
func runFuncName() string {
	pc := make([]uintptr, 1)
	runtime.Callers(2, pc)
	f := runtime.FuncForPC(pc[0])
	return f.Name()
}
