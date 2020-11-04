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

// type Base struct {
// }

// func (b *Base) Do() {
// 	fmt.Println("base")
// 	b.DoA()
// }

// func (b *Base) DoA() {
// 	fmt.Println("base DoA")
// }

// type Demo struct {
// 	Base
// }

// func (b *Demo) DoA() {
// 	fmt.Println("Demo DoA")
// }

func main() {
	// (&Demo{}).Do()
	// ctx, cancelFunc := context.WithTimeout(context.Background(), 3*time.Second)
	// defer cancelFunc()
	// logicResChan := make(chan bool, 1)
	// go func(logicResChan chan bool) {
	// 	fmt.Println("获取地址信息 ing1...")
	// 	// bc.AddressInfo = &AddressInfo{
	// 	// 	AddressID:  9931831,
	// 	// 	FristName:  "hei",
	// 	// 	SecondName: "heihei",
	// 	// }
	// 	fmt.Println("获取地址信息 ing2...")
	// 	time.Sleep(1 * time.Second)
	// 	logicResChan <- true
	// 	fmt.Println("获取地址信息 done...")
	// }(logicResChan)

	// fmt.Println("获取地址信息 wait...")

	// select {
	// case <-logicResChan:
	// 	// 业务执行结果
	// 	fmt.Println("获取地址信息 wait.done...")
	// 	break
	// case <-ctx.Done():
	// 	// 超时退出
	// 	fmt.Println("获取地址信息 timeout...")
	// 	break
	// }

	// wg := &sync.WaitGroup{}

	// wg.Add(1)
	// go func(wg *sync.WaitGroup) {
	// 	fmt.Println("aaa")
	// 	wg.Done()
	// }(wg)

	// wg.Add(1)
	// go func(wg *sync.WaitGroup) {
	// 	fmt.Println("aaa")
	// 	wg.Done()
	// }(wg)

	// wg.Add(1)
	// go func(wg *sync.WaitGroup) {
	// 	fmt.Println("aaa")
	// 	wg.Done()
	// }(wg)

	// fmt.Println("bbb")

	// wg.Wait()

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
