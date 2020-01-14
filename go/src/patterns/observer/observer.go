package main

//------------------------------------------------------------
//叼叼的设计模式(Go享版)
//观察者模式
//@auhtor TIGERB<https://github.com/TIGERB>
//------------------------------------------------------------

import "fmt"

// ObserverOrderCreateInterface 定义一个订单创建观察者的接口
type ObserverOrderCreateInterface interface {
	// 自身的业务
	Do(Observable *ObservableOrderCreate) error
}

// Param 参数校验
type Param struct {
}

// Do 参数校验
func (observer *Param) Do(Observable *ObservableOrderCreate) error {
	// code...
	fmt.Println("Param Code...")
	return nil
}

// Address 地址校验等
type Address struct {
}

// Do 地址校验等
func (observer *Address) Do(Observable *ObservableOrderCreate) error {
	// code...
	fmt.Println("Address Code...")
	return nil
}

// Check 其他校验
type Check struct {
}

// Do 其他校验
func (observer *Check) Do(Observable *ObservableOrderCreate) error {
	// code...
	fmt.Println("Check Code...")
	return nil
}

// Order 写订单表等
type Order struct {
}

// Do 写订单表等
func (observer *Order) Do(Observable *ObservableOrderCreate) error {
	// code...
	fmt.Println("Order Code...")
	return nil
}

// OrderItem 写订单商品信息表等
type OrderItem struct {
}

// Do 写订单商品信息表等
func (observer *OrderItem) Do(Observable *ObservableOrderCreate) error {
	// code...
	fmt.Println("OrderItem Code...")
	return nil
}

// Log 日志
type Log struct {
}

// Do 日志
func (observer *Log) Do(Observable *ObservableOrderCreate) error {
	// code...
	fmt.Println("Log Code...")
	return nil
}

// 等等观察者... 不再赘述

// Request 请求数据对象
type Request struct {
}

// Observable 被观察者
type Observable interface {
	Attach(observer ...ObserverOrderCreateInterface) *ObservableOrderCreate
	Notify() error
}

// ObservableOrderCreate 一个具体的 创建订单的被观察者
type ObservableOrderCreate struct {
	InfoUser     interface{}
	InfoProduct  interface{}
	InfoAddress  interface{}
	InfoCoupon   interface{}
	observerList []ObserverOrderCreateInterface
}

// Init 根据请求参数、购物车数据 初始化机器人的属性
func (Observable *ObservableOrderCreate) Init(r *Request) *ObservableOrderCreate {
	// code ...
	return Observable
}

// Attach 注册观察者
// @param $observer ObserverOrderCreateInterface 观察者列表
func (Observable *ObservableOrderCreate) Attach(observer ...ObserverOrderCreateInterface) *ObservableOrderCreate {
	Observable.observerList = append(Observable.observerList, observer...)
	return Observable
}

// Notify 通知观察者
func (Observable *ObservableOrderCreate) Notify() error {
	// code ...
	for _, observer := range Observable.observerList {
		observer.Do(Observable)
	}
	return nil
}

// 客户端调用
func main() {
	// 具体使用
	(&ObservableOrderCreate{}).Init(&Request{}).
		Attach( // 观察者观察被观察者
			&Param{},
			&Address{},
			&Check{},
			&Order{},
			&OrderItem{},
			&Address{},
			&Log{},
		).
		Notify() // 被观察者通知观察者
}
