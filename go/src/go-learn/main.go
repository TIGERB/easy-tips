package main

// GODEBUG=scheddetail=1,schedtrace=1000 ./go-learn
//
//
//
//
//
//
//
//
//
//
func main() {

	// goroutine 顺序打印

	// // context 控制多个 goroutine 超时
	// ctx, cancelFunc := context.WithTimeout(context.Background(), 2*time.Second)
	// defer cancelFunc()

	// go func(ctx context.Context) {
	// 	requestHandleResult := make(chan string, 1)
	// 	go func(resChan chan<- string) {
	// 		time.Sleep(3 * time.Second)
	// 		resChan <- "success"
	// 	}(requestHandleResult)
	// 	select {
	// 	case res := <-requestHandleResult:
	// 		fmt.Println("requestHandleResult: " + res)
	// 	case <-ctx.Done():
	// 		fmt.Println("child | context.WithTimeout | ctx err: " + ctx.Err().Error())
	// 	}
	// }(ctx)

	// select {
	// case <-ctx.Done():
	// 	fmt.Println("main | context.WithTimeout | ctx err: " + ctx.Err().Error())
	// }

	// runtime.GOMAXPROCS(runtime.NumCPU())
	// debug.SetMaxThreads(runtime.NumCPU())
	// go (func() {
	// 	fmt.Println("aa")
	// })()
	// var a int32 = 1
	// atomic.AddInt32(&a, 1)

	// fmt.Println(a)

	// fmt.Println("runtime.NumCPU()", runtime.NumCPU())
	// runtime.GOMAXPROCS(runtime.NumCPU())

	// wg := &sync.WaitGroup{}
	// numbers := 10
	// wg.Add(numbers)
	// for range make([]byte, numbers) {
	// 	go func(wg *sync.WaitGroup) {
	// 		var counter int
	// 		for i := 0; i < 1e10; i++ {
	// 			counter++
	// 		}
	// 		wg.Done()
	// 	}(wg)
	// }

	// wg.Wait()
	// l, _ := net.Listen("tcp", ":9999")
	// for {
	// 	c, _ := l.Accept()
	// 	if c == nil {
	// 		continue
	// 	}
	// 	fmt.Println("c.RemoteAddr()", c.RemoteAddr())
	// 	go func(c net.Conn) {
	// 		defer c.Close()
	// 		var body []byte
	// 		for {
	// 			_, err := c.Read(body)
	// 			if err != nil {
	// 				break
	// 			}
	// 			fmt.Println("body", string(body))
	// 			c.Write(body)
	// 			break
	// 		}
	// 	}(c)
	// }

	// // ------------------ 使用http包启动一个http服务 方式一 ------------------
	// // *http.Request http请求内容实例的指针
	// // http.ResponseWriter 写http响应内容的实例
	// http.HandleFunc("/v1/demo", func(w http.ResponseWriter, r *http.Request) {
	// 	// 写入响应内容
	// 	w.Write([]byte("Hello TIGERB !\n"))
	// })
	// // 启动一个http服务并监听8888端口 这里第二个参数可以指定handler
	// http.ListenAndServe(":8888", nil)
}
