package main

import "fmt"

// BehaviorOrderCreateInterface 定义一个订单创建行为的接口
type BehaviorOrderCreateInterface interface {
	// 自身的业务
	Do(robot *RobotOrderCreate) error
}

// Param 参数校验
type Param struct {
}

// Do 参数校验
func (behavior *Param) Do(robot *RobotOrderCreate) error {
	// code...
	fmt.Println("Param Code...")
	return nil
}

// Address 地址校验等
type Address struct {
}

// Do 地址校验等
func (behavior *Address) Do(robot *RobotOrderCreate) error {
	// code...
	fmt.Println("Address Code...")
	return nil
}

// Check 其他校验
type Check struct {
}

// Do 其他校验
func (behavior *Check) Do(robot *RobotOrderCreate) error {
	// code...
	fmt.Println("Check Code...")
	return nil
}

// Order 写订单表等
type Order struct {
}

// Do 写订单表等
func (behavior *Order) Do(robot *RobotOrderCreate) error {
	// code...
	fmt.Println("Order Code...")
	return nil
}

// OrderItem 写订单商品信息表等
type OrderItem struct {
}

// Do 写订单商品信息表等
func (behavior *OrderItem) Do(robot *RobotOrderCreate) error {
	// code...
	fmt.Println("OrderItem Code...")
	return nil
}

// Log 日志
type Log struct {
}

// Do 日志
func (behavior *Log) Do(robot *RobotOrderCreate) error {
	// code...
	fmt.Println("Log Code...")
	return nil
}

// 等等行为... 不再赘述

// Request 请求数据对象
type Request struct {
}

// RobotOrderCreate 一个具体的 创建订单的机器人
type RobotOrderCreate struct {
	InfoUser     interface{}
	InfoProduct  interface{}
	InfoAddress  interface{}
	InfoCoupon   interface{}
	behaviorList []BehaviorOrderCreateInterface
}

// Init 根据请求参数、购物车数据 初始化机器人的属性
func (robot *RobotOrderCreate) Init(r *Request) *RobotOrderCreate {
	// code ...
	return robot
}

// RegisterBehavior 注册行为
// @param $behavior Closure 行为闭包列表
func (robot *RobotOrderCreate) RegisterBehavior(behavior ...BehaviorOrderCreateInterface) *RobotOrderCreate {
	robot.behaviorList = append(robot.behaviorList, behavior...)
	return robot
}

// Create 创建订单
func (robot *RobotOrderCreate) Create() error {
	// code ...
	for _, behavior := range robot.behaviorList {
		behavior.Do(robot)
	}
	return nil
}

func main() {
	// 具体使用
	(&RobotOrderCreate{}).Init(&Request{}).
		RegisterBehavior(
			&Param{},
			&Address{},
			&Check{},
			&Order{},
			&OrderItem{},
			&Address{},
			&Log{},
		).
		Create()
}
