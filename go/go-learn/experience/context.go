package main

import (
	"context"
	"fmt"
	"math/rand"
	"time"
)

// func main() {
// 	// DemoWithTimeout()
// 	// DemoWithDeadline()
// 	// DemoWithCancel()
// 	// demoChan()

// 	time.AfterFunc(3*time.Second, func() {
// 		fmt.Println("aaa")
// 	})

// 	time.Sleep(5 * time.Second)
// }

// DemoWithTimeout context.WithTimeout使用示例
func DemoWithTimeout() {
	// 当前时间多少秒后超时
	// 第二个参数的类型是 time.Duration
	ctx, cancelFunc := context.WithTimeout(context.Background(), 2*time.Second)
	defer cancelFunc()

	// 模拟业务代码 匿名函数
	businessLogicFunc := func(ctx context.Context, timeout time.Duration) {
		requestHandleResult := make(chan string, 1)
		go func(resChan chan<- string) {
			// 模拟业务执行的时间
			time.Sleep(timeout)
			resChan <- "success"
		}(requestHandleResult)
		select {
		case res := <-requestHandleResult:
			fmt.Println("DemoWithTimeout | requestHandleResult: " + res)
		case <-ctx.Done():
			fmt.Println("DemoWithTimeout | child | context.WithTimeout | ctx err: "+ctx.Err().Error(), fmt.Sprintf("ctx %p", ctx))
		}
	}

	go businessLogicFunc(ctx, 3*time.Second)
	go businessLogicFunc(ctx, 3*time.Second)
	go businessLogicFunc(ctx, 3*time.Second)

	select {
	case <-ctx.Done():
		fmt.Println("DemoWithTimeout | main  | context.WithTimeout | ctx err: "+ctx.Err().Error(), fmt.Sprintf("ctx %p", ctx))
	}

	// 再睡会 保证子goroutine的内容可以打印出来
	time.Sleep(10 * time.Second)
}

// DemoWithDeadline context.WithDeadline 使用示例
func DemoWithDeadline() {
	// 到哪个具体时间点超时
	// 第二个参数的类型是 time.Time
	ctx, cancelFunc := context.WithDeadline(context.Background(), time.Now().Add(2*time.Second))
	defer cancelFunc()

	// 模拟业务代码 匿名函数
	businessLogicFunc := func(ctx context.Context, timeout time.Duration) {
		requestHandleResult := make(chan string, 1)
		go func(resChan chan<- string) {
			// 模拟业务执行的时间
			time.Sleep(timeout)
			resChan <- "success"
		}(requestHandleResult)
		select {
		case res := <-requestHandleResult:
			fmt.Println("DemoWithDeadline | requestHandleResult: " + res)
		case <-ctx.Done():
			fmt.Println("DemoWithDeadline | child | context.WithDeadline | ctx err: " + ctx.Err().Error())
		}
	}

	go businessLogicFunc(ctx, 1*time.Second)
	go businessLogicFunc(ctx, 3*time.Second)

	select {
	case <-ctx.Done():
		fmt.Println("DemoWithDeadline | main | context.WithDeadline | ctx err: " + ctx.Err().Error())
	}
}

// DemoWithCancel context.WithCancel 使用示例
func DemoWithCancel() {

	// 当主goroutine执行 cancelFunc()时  <-ctx.Done()会被关闭
	ctx, cancelFunc := context.WithCancel(context.Background())
	defer cancelFunc()

	// 模拟修数据库数据
	requestHandleResult := make(chan string, 1)
	businessLogicFunc := func(ctx context.Context, resChan chan<- string) {
		for {
			select {
			case <-ctx.Done():
				fmt.Println("DemoWithCancel | child | context.WithCancel | ctx err: " + ctx.Err().Error())
				return //退出
			default:
				// 查询不到需要修正的数据
				if rand.Intn(10) == 6 { // 用随机数模拟下
					resChan <- "alldone"
					continue
				}
				// 还有数据 每次最多修500条数据

				// 开始修数据...

				// 修完当前数据
				resChan <- "done"
			}
		}
	}

	// 开始修数据
	go businessLogicFunc(ctx, requestHandleResult)

	// 需要知道业务逻辑都执行完成
	// 然后退出当前函数 触发 cancelFunc()
	for res := range requestHandleResult {
		fmt.Println("res: ", res)
		if res == "alldone" {
			cancelFunc()                 // 模拟：这里手动执行cancelFunc 触发 ctx.Done()写数据，为了可以看见子协程的打印结果
			time.Sleep(10 * time.Second) // 再睡会 保证子goroutine的内容可以打印出来
			return
		}
	}
}

func demoChan() {
	ch := make(chan struct{}, 0)

	for range make([]byte, 3) {
		go func(ch <-chan struct{}) {
			select {
			case r := <-ch:
				fmt.Println("退出", "r:", r)
			}
		}(ch)
	}

	time.Sleep(1 * time.Second)
	fmt.Println("close chan")
	close(ch)
	time.Sleep(5 * time.Second)
}
