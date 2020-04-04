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

// ObservableConcrete 一个具体的 创建订单的被观察者
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

// Param 参数校验
type Param struct {
}

// Do 参数校验
func (observer *Param) Do(o Observable) error {
	// code...
	fmt.Println("Param Code...")
	return nil
}

// 客户端调用
func main() {
	// 具体使用
	(&ObservableConcrete{}).
		Attach( // 观察者观察被观察者
			&Param{},
		).
		Notify() // 被观察者通知观察者
}

// 获取正在运行的函数名
func runFuncName() string {
	pc := make([]uintptr, 1)
	runtime.Callers(2, pc)
	f := runtime.FuncForPC(pc[0])
	return f.Name()
}
