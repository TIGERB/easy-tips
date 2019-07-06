package main

import (
	"fmt"
	"sync"
	"time"
)

// 导入net/http包

func init() {

}

func main() {
	var wg sync.WaitGroup

	// fmt.Println("waiting")
	// // wg.Wait()
	// fmt.Println("end")

	for index := 0; index < 10; index++ {
		wg.Add(1)
		go func(index int) {
			time.Sleep(10 * time.Second)
			fmt.Println(index)
			wg.Done()
		}(index)
	}

	fmt.Println("waiting")
	wg.Wait()
	fmt.Println("end")
}
