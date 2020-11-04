package main

import (
	"fmt"
	"sync"
)

//------------------------------------------------------------
//我的代码没有`else`系列
//单例模式
//@auhtor TIGERB<https://github.com/TIGERB>
//------------------------------------------------------------

// // ---------线程不安全的单例 ---------
// var instance *singleton

// type singleton struct{}

// func GetInstance() *singleton {
// 	if instance == nil {
// 		instance = &singleton{}
// 	}
// 	return instance
// }

// // ---------线程安全的单例-加锁---------
// var instance *singleton

// type singleton struct{}

// func GetInstance() *singleton {
// 	// 加锁
// 	// 获取实例都要加锁
// 	mutex := &sync.Mutex{}
// 	mutex.Lock()
// 	defer mutex.Unlock()

// 	if instance == nil {
// 		instance = &singleton{}
// 	}
// 	return instance
// }

// // ---------线程安全的单例-实例为空时再加锁---------
// var instance *singleton

// type singleton struct{}

// func GetInstance() *singleton {
// 	// 减少加锁次数
//  // 依然线程不安全
// 	if instance != nil {
// 		return instance
// 	}
// 	// 加锁
// 	// 获取实例都要加锁
// 	mutex := &sync.Mutex{}
// 	mutex.Lock()
// 	defer mutex.Unlock()
// 	if instance == nil {
// 		instance = &singleton{}
// 	}
// 	return instance
// }

// // ---------线程安全的单例-原子操作判断实例为空时再加锁---------
// var instance *singleton
// var flag *uint32

// type singleton struct{}

// func GetInstance() *singleton {
// 	// 利用原子操作减少加锁次数
// 	if atomic.AddUint32(flag, 0) != 0 {
// 		return instance
// 	}
// 	// 加锁
// 	// 获取实例都要加锁
// 	mutex := &sync.Mutex{}
// 	mutex.Lock()
// 	defer mutex.Unlock()
// 	if instance == nil {
// 		instance = &singleton{}
// 		// 使用原子操作标示
// 		atomic.AddUint32(flag, 1)
// 	}
// 	return instance
// }

// ---------线程安全的单例-使用sync.Once---------
var instance *singleton
var once *sync.Once

func init() {
	once = &sync.Once{}
}

type singleton struct {
	Name string
}

func (s *singleton) GetName() {
	fmt.Println(s.Name)
}

func GetInstance(name string) *singleton {
	// 利用once
	once.Do(func() {
		instance = &singleton{
			Name: name,
		}
	})
	return instance
}

func main() {
	GetInstance("a").GetName()
	GetInstance("b").GetName()
	GetInstance("c").GetName()
	GetInstance("d").GetName()
}
