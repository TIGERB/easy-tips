package main

import (
	"fmt"
	"log"
	"time"
)

func main() {
	var i int
	ticker := time.NewTicker(1 * time.Second)
	for v := range ticker.C {
		fmt.Println(v, i)
		i = i + 1
		// 模拟业务中某些情况才会执行下面的代码块
		if i == 6 {
			res, err := Simulate(i)
			// 有时候打业务log的时候 获取错误信息 err.Error() 的代码忘了写在err != nil里 导致空指针
			log.Println(fmt.Sprintf("res:%t i:%d err:%s", res, i, err.Error()))
			if err != nil {
				return
			}
		}
	}
}

func Simulate(i int) (b bool, err error) {
	return true, nil
}
