# PHP到Go速转手册

## 前言

最近由于组内有不少的同学是刚PHP转Go的，所以整理了一份简要的手册，帮助大家高效的上手Go语言，主要是通过对比PHP和Go的不同点来强化理解，内容主要分为以下四部分：

- 语言层面差异
- 基础语法差异
- 避坑指南
- 进阶使用

## 语言层面差异

```
备注：下文基于PHP主流php-fpm模式。
```

对比项|PHP|Go
------|------|------
字符串表示|单引号(PSR)|双引号
拼接字符串|.|+
语言版本兼容性|不好|向下兼容
代码风格|无官方标准，社区标准起步晚|自始至今官方统一标准，且提供工具
脚本语言|是|不是
强类型语言|不是(PHP7支持严格模式)|是
是否支持垃圾回收|是|是
面向对象语言(OOP)|神似|部分支持，核心是合成复用
是否支持继承|是|否(有合成复用)
是否支持interface|是|是
是否支持try...catch...|是|否
是否支持包管理|是|是
是否支持跨平台|是|是
环境搭建成本|高|低
执行方式|cli命令行模式、php-fpm模式(①)|二进制
进程模型|多进程|单进程
原生是否支持创建TCP/UDP服务|是(支持不好，生产不可用)|是
原生是否支持创建HTTP服务|是(支持不好，生产不可用)|是
进程阻塞性|是|否
是否支持协程|否(②)|是
并发能力(③)|弱|极强
是否常驻内存运行|不是(④)|是
引入文件方式|`require`或者`include`对应文件|`import`导入包
是否支持单元测试|是|是
是否支持基准测试(benchmark)|否|是
是否支持性能分析|支持(xhprof/tideways)|支持(pprof/dlv)
性能分析工具使用成本|高(装扩展成本高)|极低

```
①其他模式还有swoole等
②PHP的swoole协程框架等支持协程
③此处不考虑I/O多路复用，PHP的swoole协程框架等也支持协程并发
④PHP的swoole协程框架是常驻内存，cli命令行模式也可以常驻内存等
```

刚开始由PHP语言转Go语言的过程，重点是编程意识的转变，尤其是以下几点：

- 强类型
- 常驻内存运行
- 理解和使用指针
- 并发安全
- 资源及时释放或返还
## 基础语法差异

```
备注：下文基于PHP5.4+版本
```
### 常用基本类型对比

PHP类型比较少和简单，PHP常用数据类型有boolean布尔值、string字符串、int整型、float浮点型、array数组、object对象。

PHP常用数据类型和Go语言对应或者类似的类型做个对比，如下：

语言\类型|boolean|string|int|float|array|object
------|------|------|------|------|------|------
PHP|bool|string|int|float|array(1,2,3)索引数组、array('1' => 1, '2' => 2, '3' => 3)关联数组|实例化类class
Go|bool|string|int、int8、int16、int32、int64、uint、uint8、uint16、uint32、uint64|float32、float64|[length]type|比较像struct

除此之外Go还支持更丰富的类型：

|类型|
|---|
|slice切片(相当于PHP的索引数组)|
|map(相当于PHP的关联数组)|
|channel(管道，通过通信共享，不要通过共享来通信)|
|指针(Go语言的值类型都有对应的指针类型)|
|byte(字节，对应uint8别名，可以表示Ascaii码)|
|rune(对应int32，可以表示unicode)|
|等等|
|自定义类型，例如`type userDefinedType int32`|
### 常用基本类型初始化方式对比

类型|PHP|Go(定义变量带`var`关键字，或者不带直接使用语法糖`:=`)
---------|---------|---------
boolean|`$varStr = true;`|`var varStr bool = true`<br>或者 `var varStr = true`<br>或者 `varStr := true`
string|`$varStr = 'demo';`|`var varStr string = ""`<br>或者 `varStr := ""`(:=写法下面省略)
int32|`$varNum = 0;`|`var varInt32 int32 = 0`
int64|同上|`var varInt64 int64 = 0`
float32|`$varNum = 0.01;`|`var varFloat32 float32 = 0`
float64|同上|`var varFloat64 float64 = 0`
array|`$varArray = array();`<br>或者语法糖`$varArray = [];`|`var varArray [6]int32 = [6]int32{}`
slice(切片)|同上，PHP叫索引数据|`var varSlice []int32 = []int32{}`切片相对于数据会自动扩容
map|`$varMap = array('key' => 'value');`|`var varMap map[string]int32 = map[string]int32{}`
closure(闭包)|`$varClosure = function() {};`|`var varClosure func() = func() {}`
channel|无|`var varChannel chan string = make(chan string)` 无缓存channel；<br>`var varChannelBuffer chan string = make(chan string, 6)`有缓存channel

### 常用函数对比

常用函数描述 | PHP | Go
--------|--------|--------
数组长度 | count() | len()
分割字符串为数组 | explode() | strings.Split(s string, sep string) []string
转大写 | strtoupper() | strings.ToUpper(s string) string
转小写 | strtolower() | strings.ToLower(s string) string
去除空格 | trim() | strings.Trim(s, cutset string) string
json序列化 | json_encode() | json.Marshal(v interface{}) ([]byte, error)
json反序列化 | json_decode() | json.Unmarshal(data []byte, v interface{}) error
序列化(不再建议使用) | serialize()、unserialize() | 包https://github.com/wulijun/go-php-serialize
md5 | md5() | 包crypto/md5
终端输出 | echo、var_dump等 | fmt.Println(a ...interface{})
各种类型互转 | intval()等 | 包strconv

### PHP类的实例化和Go结构体的初始化的对比

PHP类的实例化
```php
/* 
定义class
*/
class ClassDemo {
    // 私有属性
    private $privateVar = "";
    // 公有属性
    public $publicVar = "";
    // 构造函数
    public function __construct()
    {
        // 实例化类时执行
    }
    // 私有方法
    private function privateFun()
    {
    
    }
    // 公有方法
    public function publicFun()
    {
    
    }
}

// 实例化类ClassDemo 获取类ClassDemo的对象
$varObject = new ClassDemo(); // 对象(类)
```

Go结构体的初始化
```go
// 包初始化时执行
func init() {

}

type StructDemo struct{
    // 小写开头驼峰表示私有属性
    // 不可导出
    privateVar string
    // 大写开头驼峰表示公有属性
    // 可导出
    PublicVar string
}

// 小写开头驼峰表示私有方法
// 结构体StructDemo的私有方法
func (demo *StructDemo) privateFun() error {
    return nil
}

// 大写开头驼峰表示公有属性
// 结构体StructDemo的公有方法
func (demo *StructDemo) PublicFun() error {
    return nil
}

// 初始化结构体StructDemo
// structDemo := &StructDemo{}
```

### 常用函数对比

常用函数描述|PHP|Go
---------|---------|---------
数组长度|count()|len()
分割字符串为数组|explode()|strings.Split(s string, sep string) []string
转大写|strtoupper()|strings.ToUpper(s string) string
转小写|strtolower()|strings.ToLower(s string) string
去除空格|trim()|strings.Trim(s, cutset string) string
json序列化|json_encode()|json.Marshal(v interface{}) ([]byte, error)
json反序列化|json_decode()|json.Unmarshal(data []byte, v interface{}) error
md5|md5()|包`crypto/md5`
终端输出|echo、var_dump等|fmt.Println(a ...interface{})

## 避坑指南

1. 谨慎使用全局变量，全局变量不会像PHP一样，在完成一次请求之后被销毁
2. 形参是`slice`、`map`类型的参数，注意值可被全局修改
3. 资源使用完毕，记得释放资源或回收资源
4. 不要依赖map遍历的顺序
5. 不要并发写map
6. 注意判断指针类型不为空`nil`，再操作
7. Go语言不支持继承，但是有合成复用

### 1.谨慎使用全局变量，全局变量不会像PHP一样，在完成一次请求之后被销毁

```go
package main

import (
	"github.com/gin-gonic/gin"
)

// 全局变量不会像PHP一样，在完成一次请求之后被销毁
var GlobalVarDemo int32 = 0

// 模拟接口逻辑
func main() {
	r := gin.Default()
	r.GET("/ping", func(c *gin.Context) {
		GlobalVarDemo++
		c.JSON(200, gin.H{
			"message": GlobalVarDemo,
		})
	})
	r.Run()
}

// 我们多次请求接口，可以很明显发现：全局变量不会像PHP一样，在完成一次请求之后被销毁。
// 但是PHP不一样，全局变量在完成一次请求之后会被自动销毁。
// curl "127.0.0.1:8080/ping" 
// {"message":1}                                                                     
// curl "127.0.0.1:8080/ping"
// {"message":2} <------- 值在递增
// curl "127.0.0.1:8080/ping"
// {"message":3} <------- 值在递增
```

### 2.形参是`slice`、`map`类型的参数，注意值可被全局修改

> 类似PHP的引用传递，Go里面都是值传递，具体原因下面说。

```go
// 切片
package main

import "fmt"

func main() {
	paramDemo := []int32{1}
	fmt.Printf("main.paramDemo 1 %v, pointer: %p \n", paramDemo, &paramDemo)
	// 浅拷贝
	demo(paramDemo)
	fmt.Printf("main.paramDemo 2 %v, pointer: %p \n", paramDemo, &paramDemo)
}

func demo(paramDemo []int32) ([]int32, error) {
	fmt.Printf("main.demo.paramDemo pointer: %p \n", &paramDemo)
	paramDemo[0] = 2
	return paramDemo, nil
}

// main.paramDemo 1 [1], pointer: 0xc00000c048
// main.demo.paramDemo pointer: 0xc00000c078 <------- 内存地址不一样，发生了值拷贝
// main.paramDemo 2 [2] <------- 原值被修改

// main.paramDemo 1 [1], pointer: 0xc0000a6030
// main.demo.paramDemo pointer: 0xc0000a6060 <------- 内存地址不一样，发生了值拷贝
// main.paramDemo 2 [2], pointer: 0xc0000a6030 <------- 原值还是被修改了



//===========数组就没有这个问题===========
package main

import "fmt"

func main() {
	paramDemo := [1]int32{1}
	fmt.Println("main.paramDemo 1", paramDemo)
	demo(paramDemo)
	fmt.Println("main.paramDemo 2", paramDemo)
}

func demo(paramDemo [1]int32) ([1]int32, error) {
	paramDemo[0] = 2
	return paramDemo, nil
}

// [Running] go run ".../demo/main.go"
// main.paramDemo 1 [1]
// main.paramDemo 2 [1] <------- 值未被修改

//===========Map同样有这个问题===========

package main

import "fmt"

func main() {
	paramDemo := map[string]string{
		"a": "a",
	}
	fmt.Println("main.paramDemo 1", paramDemo)
	demo(paramDemo)
	fmt.Println("main.paramDemo 2", paramDemo)
}

func demo(paramDemo map[string]string) (map[string]string, error) {
	paramDemo["a"] = "b"
	return paramDemo, nil
}

// [Running] go run ".../demo/main.go"
// main.paramDemo 1 map[a:a]
// main.paramDemo 2 map[a:b] <------- 值被修改

```

> 为什么？

```
答：Go语言都是值传递，浅复制过程，slice和map底层的类型是个结构体，实际存储值的类型是个指针。
```

```go
// versions/1.13.8/src/runtime/slice.go
// slice源码结构体
type slice struct {
    array unsafe.Pointer // 实际存储值的类型是个指针
    len   int
    cap   int
}

// versions/1.13.8/src/runtime/map.go
// map源码结构体
type hmap struct {
    count     int
    flags     uint8
    B         uint8
    noverflow uint16
    hash0     uint32

    buckets    unsafe.Pointer // 实际存储值的类型是个指针
    oldbuckets unsafe.Pointer
    nevacuate  uintptr  

    extra *mapextra
}
```

> 怎么办？

```
答：深拷贝，开辟一块新内存，指针指向新内存地址，并把原有的值复制过去。如下：
```

```go
package main

import "fmt"

func main() {
	paramDemo := []int32{1}
	fmt.Println("main.paramDemo 1", paramDemo)
	// 初始化新空间
	paramDemoCopy := make([]int32, len(paramDemo))
	// 深拷贝
	copy(paramDemoCopy, paramDemo)
	demo(paramDemoCopy)
	fmt.Println("main.paramDemo 2", paramDemo)
}

func demo(paramDemo []int32) ([]int32, error) {
	paramDemo[0] = 2
	return paramDemo, nil
}

// [Running] go run ".../demo/main.go"
// main.paramDemo 1 [1]
// main.paramDemo 2 [1]

```

### 3.资源使用完毕，记得释放资源或回收资源

```go
package main

import (
	"github.com/gomodule/redigo/redis"
)

var RedisPool *redis.Pool

func init() {
	RedisPool = NewRedisPool()
}

func main() {
	redisConn := RedisPool.Get()
	// 记得defer释放资源
	defer redisConn.Close()
}

func NewRedisPool() *redis.Pool {
	// 略...
	return &redis.Pool{}
}

```

> 为什么？

```
答：避免资源被无效的持有，浪费资源和增加了资源的连接数。其次如果是归还连接池也减少新建资源的开销。
```

- 资源连接数线性增长
- 如果一直持有，资源服务端也有超时时间

### 4.不要依赖map遍历的顺序

以往PHP的”Map“(关联数组)不管遍历多少次，元素的顺序都是稳定不变的，如下：
```php
<?php

$demoMap = array(
    'a' => 'a',
	'b' => 'b',
    'c' => 'c',
    'd' => 'd',
    'e' => 'e',
);
foreach ($demoMap as $v) {
    var_dump("v {$v}");
}

// 第一次执行
[Running] php ".../php/demo.php"
string(3) "v a"
string(3) "v b"
string(3) "v c"
string(3) "v d"
string(3) "v e"

// 第N次执行
// 遍历结果的顺序都是稳定不变的
[Running] php ".../php/demo.php"
string(3) "v a"
string(3) "v b"
string(3) "v c"
string(3) "v d"
string(3) "v e"
```

但是Go语言里就不一样了，如下：
```go
package main

import "fmt"

func main() {
	var demoMap map[string]string = map[string]string{
		"a": "a",
		"b": "b",
		"c": "c",
		"d": "d",
		"e": "e",
	}
	for _, v := range demoMap {
		fmt.Println("v", v)
	}
}

// 第一次执行
// [Running] go run ".../demo/main.go"
// v a
// v b
// v c
// v d
// v e

// 第二次执行
// 遍历结果，元素顺序发生了改变
// [Running] go run ".../demo/main.go"
// v e
// v a
// v b
// v c
// v d
```

> 为什么？

```
答：底层实现都是数组+类似拉链法。
1. hash函数无序写入
2. 成倍扩容
3. 等量扩容
都决定了map本来就是无序的，所以Go语言为了避免开发者依赖元素顺序，每次遍历的时候都是随机了一个索引起始值。然后PHP通过额外的内存空间维护了map元素的顺序。
```

### 5.不要并发写map

```go
package main

import (
	"testing"
)

func BenchmarkDemo(b *testing.B) {
	var demoMap map[string]string = map[string]string{
		"a": "a",
		"b": "b",
	}
	// 模拟并发写map
	b.RunParallel(func(pb *testing.PB) {
		for pb.Next() {
			demoMap["a"] = "aa"
		}
	})
}

// BenchmarkDemo
// fatal error: concurrent map writes
// fatal error: concurrent map writes
```

> 为什么？

```
答：并发不安全，触发panic：“fatal error: concurrent map writes”。
```

```go
// go version 1.13.8源码
// hashWriting 值为 4
if h.flags&hashWriting != 0 {
	throw("concurrent map read and map write")
}
```

### 6.注意判断指针类型不为空`nil`，再操作

```go
package main

import (
	"fmt"
	"log"
	"net/http"
)

func main() {
	resp, err := http.Get("https://www.example.com")
	if resp.StatusCode != http.StatusOK || err != nil {
		// 当 resp为nil时 会触发panic
		// 当 resp.StatusCode != http.StatusOK 时err可能为nil 触发panic
		log.Printf("err: %s", err.Error())
	}
}


// [Running] go run ".../demo/main.go"
// panic: runtime error: invalid memory address or nil pointer dereference

```

```go
package main

import (
	"fmt"
	"log"
	"net/http"
)

func main() {
	// 模拟请求业务code
	resp, err := http.Get("https://www.example.com")
	fmt.Println(resp, err)
	if err != nil {
		// 报错并记录异常日志
		log.Printf("err: %s", err.Error())
		return
	}
	// 模拟业务code不为成功的code
	if resp != nil && resp.StatusCode != http.StatusOK {
		// 报错并记录异常日志
	}
}
```

### 7. Go语言不支持继承，但是有合成复用

```php
abstract class AbstractClassDemo {

    // 抽象方法
    abstract public function demoFun();
    
    // 公有方法
    public function publicFun()
    {
        $this->demoFun();
    }
}

class ClassDemo extends AbstractClassDemo {

    public function demoFun()
    {
        var_dump("Demo");
    }
}

(new ClassDemo())->demoFun();

// [Running] php ".../php/demo.php"
// string(4) "Demo"
```

```go
package main

import (
	"fmt"
)

//基础结构体
type Base struct {
}

// Base的DemoFun
func (b *Base) DemoFun() {
	fmt.Println("Base")
}

func (b *Base) PublicFun() {
	b.DemoFun()
}

type Demo struct {
	// 合成复用Base
	Base
}

// Demo的DemoFun
func (d *Demo) DemoFun() {
	fmt.Println("Demo")
}

func main() {
	// 执行
	(&Demo{}).PublicFun()
}

// [Running] go run ".../demo/main.go"
// Base <------ 注意此处执行的是被合成复用的结构体的方法
```

## 进阶使用

1. 热加载工具bee
2. Goroutine并发控制之`sync.WaitGroup`包的使用
3. 子Goroutine超时控制之`context.Context`包的使用
4. 并发安全的map之`sync.Map`包的使用
5. 减少GC压力之`sync.Pool`包的使用
6. 减少缓存穿透利器之`singleflight`包的使用
7. Channel的使用
8. 单元测试&基准测试
9. 性能分析

### 1.热加载工具bee

> 作用：以热加载方式运行Go代码，会监视代码的变动重新运行代码，提高开发效率。

使用：
```
安装
go get github.com/beego/bee/v2

热加载方式启动项目
SOAAGENT=10.40.24.126 bee run -main=main.go -runargs="start"
```

### 2.Goroutine并发控制之`sync.WaitGroup`包的使用

> 作用：Goroutine可以等待，直到当前Goroutine派生的子Goroutine执行完成。

使用：
```go
package main

import (
	"fmt"
	"sync"
	"time"
)

func main() {
	wg := &sync.WaitGroup{}

	wg.Add(1)
	go func(wg *sync.WaitGroup) {
		defer wg.Done()
		fmt.Println("子a 开始执行")
		time.Sleep(5 * time.Second)
		fmt.Println("子a 执行完毕")
	}(wg)

	wg.Add(1)
	go func(wg *sync.WaitGroup) {
		defer wg.Done()
		fmt.Println("子b 开始执行")
		time.Sleep(5 * time.Second)
		fmt.Println("子b 执行完毕")
	}(wg)

	wg.Add(1)
	go func(wg *sync.WaitGroup) {
		defer wg.Done()
		fmt.Println("子c 开始执行")
		time.Sleep(5 * time.Second)
		fmt.Println("子c 执行完毕")
	}(wg)

	fmt.Println("主 等待")
	wg.Wait()
	fmt.Println("主 退出")
}

// 第一次执行
// [Running] go run ".../demo/main.go"
// 子a 开始执行
// 子c 开始执行
// 子b 开始执行
// 主 等待 <------ 注意这里和下面打印的位置不一样，因为当前代码并发执行是没有保障执行顺序的
// 子b 执行完毕
// 子a 执行完毕
// 子c 执行完毕
// 主 退出

// 第一次执行
// [Running] go run ".../demo/main.go"
// 主 等待 <------ 注意这里和上面打印的位置不一样，因为当前代码并发执行是没有保障执行顺序的
// 子a 开始执行
// 子c 开始执行
// 子b 开始执行
// 子b 执行完毕
// 子c 执行完毕
// 子a 执行完毕
// 主 退出 <------ 主Goroutine一直等待直到子Goroutine都执行完毕
```

### 3.子Goroutine超时控制之`context.Context`包的使用

> 作用：Go语言第一形参通常都为context.Context类型，1. 传递上下文 2. 控制子Goroutine超时退出 3. 控制子Goroutine定时退出

使用：
```go
package main

import (
	"context"
	"fmt"
	"time"
)

func main() {
	ctx, cancel := context.WithTimeout(context.TODO(), 5*time.Second)
	defer cancel()
	go func(ctx context.Context) {
		execResult := make(chan bool)
		// 模拟业务逻辑
		go func(execResult chan<- bool) {
			// 模拟处理超时
			time.Sleep(6 * time.Second)
			execResult <- true
		}(execResult)
		// 等待结果
		select {
		case <-ctx.Done():
			fmt.Println("超时退出")
			return
		case <-execResult:
			fmt.Println("处理完成")
			return
		}
	}(ctx)

	time.Sleep(10 * time.Second)
}

// [Running] go run ".../demo/main.go"
// 超时退出
```

### 4.并发安全的map之`sync.Map`包的使用

> 作用：并发安全的map，支持并发写。读多写少场景的性能好。

使用：
```go
package main

import (
	"sync"
	"testing"
)

func BenchmarkDemo(b *testing.B) {
	demoMap := &sync.Map{}
	demoMap.Store("a", "a")
	demoMap.Store("b", "b")
	b.RunParallel(func(pb *testing.PB) {
		for pb.Next() {
			demoMap.Store("a", "aa")
		}
	})
}

// BenchmarkDemo
// BenchmarkDemo-4   	 6334993	       203.8 ns/op	      16 B/op	       1 allocs/op
// PASS
// 没有panic
```

### 5.减少GC压力之`sync.Pool`包的使用

> 作用：复用对象，减少垃圾回收GC压力。

使用：

#### 5.1 不使用sync.Pool代码示例
```go
package main

import (
	"sync"
	"testing"
)

type Country struct {
	ID   int    `json:"id"`
	Name string `json:"name"`
}
type Province struct {
	ID   int    `json:"id"`
	Name string `json:"name"`
}
type City struct {
	ID   int    `json:"id"`
	Name string `json:"name"`
}
type County struct {
	ID   int    `json:"id"`
	Name string `json:"name"`
}
type Street struct {
	ID   int    `json:"id"`
	Name string `json:"name"`
}

// 模拟数据
// 地址信息对象
type AddressModule struct {
	Consignee       string    `json:"consignee"`
	Email           string    `json:"email"`
	Mobile          int64     `json:"mobile"`
	Country         *Country  `json:"country"`
	Province        *Province `json:"province"`
	City            *City     `json:"city"`
	County          *County   `json:"county"`
	Street          *Street   `json:"street"`
	DetailedAddress string    `json:"detailed_address"`
	PostalCode      string    `json:"postal_code"`
	AddressID       int64     `json:"address_id"`
	IsDefault       bool      `json:"is_default"`
	Label           string    `json:"label"`
	Longitude       string    `json:"longitude"`
	Latitude        string    `json:"latitude"`
}

// 不使用sync.Pool
func BenchmarkDemo_NoPool(b *testing.B) {
	b.RunParallel(func(pb *testing.PB) {
		for pb.Next() {
			// 直接初始化
			addressModule := &AddressModule{}
			addressModule.Consignee = ""
			addressModule.Email = ""
			addressModule.Mobile = 0
			addressModule.Country = &Country{
				ID:   0,
				Name: "",
			}
			addressModule.Province = &Province{
				ID:   0,
				Name: "",
			}
			addressModule.City = &City{
				ID:   0,
				Name: "",
			}
			addressModule.County = &County{
				ID:   0,
				Name: "",
			}
			addressModule.Street = &Street{
				ID:   0,
				Name: "",
			}
			addressModule.DetailedAddress = ""
			addressModule.PostalCode = ""
			addressModule.IsDefault = false
			addressModule.Label = ""
			addressModule.Longitude = ""
			addressModule.Latitude = ""
			// 下面这段代码没意义 只是为了不报语法错误
			if addressModule == nil {
				return
			}
		}
	})
}

// 不使用sync.Pool执行结果
// goos: darwin
// goarch: amd64
// pkg: demo
// cpu: Intel(R) Core(TM) i5-7360U CPU @ 2.30GHz
// BenchmarkDemo_NoPool-4   	144146564	        84.62 ns/op	     120 B/op	       5 allocs/op
// PASS
// ok  	demo	21.782s

```

<p align="center">
	<h5>不使用sync.Pool执行分析：火焰图&Top函数</h5>
	<p>可以很明显看见GC过程消耗了大量的CPU。</p>
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210708235739.jpg" style="width:80%">
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210708152442.png" style="width:50%">
</p>

#### 5.2 使用sync.Pool代码示例
```go
// 使用sync.Pool
func BenchmarkDemo_Pool(b *testing.B) {
	// 使用缓存池sync.Pool
	demoPool := &sync.Pool{
		// 定义初始化结构体的匿名函数
		New: func() interface{} {
			return &AddressModule{
				Country: &Country{
					ID:   0,
					Name: "",
				},
				Province: &Province{
					ID:   0,
					Name: "",
				},
				City: &City{
					ID:   0,
					Name: "",
				},
				County: &County{
					ID:   0,
					Name: "",
				},
				Street: &Street{
					ID:   0,
					Name: "",
				},
			}
		},
	}
	b.RunParallel(func(pb *testing.PB) {
		for pb.Next() {
			// 从缓存池中获取对象
			addressModule, _ := (demoPool.Get()).(*AddressModule)
			// 下面这段代码没意义 只是为了不报语法错误
			if addressModule == nil {
				return
			}

			// 重置对象 准备归还对象到缓存池
			addressModule.Consignee = ""
			addressModule.Email = ""
			addressModule.Mobile = 0
			addressModule.Country.ID = 0
			addressModule.Country.Name = ""
			addressModule.Province.ID = 0
			addressModule.Province.Name = ""
			addressModule.County.ID = 0
			addressModule.County.Name = ""
			addressModule.Street.ID = 0
			addressModule.Street.Name = ""
			addressModule.DetailedAddress = ""
			addressModule.PostalCode = ""
			addressModule.IsDefault = false
			addressModule.Label = ""
			addressModule.Longitude = ""
			addressModule.Latitude = ""
			// 还对象到缓存池
			demoPool.Put(addressModule)
		}
	})
}

// 使用sync.Pool执行结果
// goos: darwin
// goarch: amd64
// pkg: demo
// cpu: Intel(R) Core(TM) i5-7360U CPU @ 2.30GHz
// BenchmarkDemo_Pool-4   	988550808	        12.41 ns/op	       0 B/op	       0 allocs/op
// PASS
// ok  	demo	14.215s

```

<p align="center">
	<h5>使用sync.Pool执行分析：火焰图&Top函数</h5>
	<p>runtime.mallocgc 已经在top里面看不见了</p>
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210708151902.png" style="width:80%">
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210708152639.png" style="width:50%">
</p>

```
关于火焰图和Top函数的使用下面会讲到。
```

### 6.减少缓存穿透利器之`singleflight`包的使用

> 作用：缓存等穿透时减少请求数。

使用：
```go
package main

import (
	"io/ioutil"
	"net/http"
	"sync"
	"testing"

	"golang.org/x/sync/singleflight"
)

// 没有使用singleflight的代码示例
func TestDemo_NoSingleflight(t *testing.T) {
	t.Parallel()
	wg := sync.WaitGroup{}
	// 模拟并发远程调用
	for i := 0; i < 3; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()
			resp, err := http.Get("http://example.com")
			if err != nil {
				t.Error(err)
				return
			}
			_, err = ioutil.ReadAll(resp.Body)
			if err != nil {
				t.Error(err)
				return
			}
			t.Log("log")
		}()
	}

	wg.Wait()
}

// 使用singleflight的代码示例
func TestDemo_Singleflight(t *testing.T) {
	t.Parallel()
	singleGroup := singleflight.Group{}
	wg := sync.WaitGroup{}
	// 模拟并发远程调用
	for i := 0; i < 3; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()
			// 使用singleflight
			res, err, shared := singleGroup.Do("cache_key", func() (interface{}, error) {
				resp, err := http.Get("http://example.com")
				if err != nil {
					return nil, err
				}
				body, err := ioutil.ReadAll(resp.Body)
				if err != nil {
					return nil, err
				}
				return body, nil
			})
			if err != nil {
				t.Error(err)
				return
			}
			_, _ = res.([]byte)
			t.Log("log", shared, err)
		}()
	}

	wg.Wait()
}

```

> 抓包域名example.com的请求：tcpdump host example.com

<p align="center">
	<h5>没有使用Singleflight一共发起了3次请求</h5>
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210708212953.png" style="width:80%">
	<h5>使用Singleflight只发起了1次请求</h5>
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210708213004.png" style="width:80%">
</p>

### 7. Channel的使用

> 作用：不要通过共享内存来通信，要通过通信来实现共享内存。相当于管道。

使用：
```go
package main

import (
	"fmt"
	"time"
)

// 响应公共结构体
type APIBase struct {
	Code    int32  `json:"code"`
	Message string `json:"message"`
}

// 模拟接口A的响应结构体
type APIDemoA struct {
	APIBase
	Data APIDemoAData `json:"data"`
}

type APIDemoAData struct {
	Title string `json:"title"`
}

// 模拟接口B的响应结构体
type APIDemoB struct {
	APIBase
	Data APIDemoBData `json:"data"`
}

type APIDemoBData struct {
	SkuList []int64 `json:"sku_list"`
}

// 模拟接口逻辑
func main() {
	// 创建接口A传输结果的通道
	execAResult := make(chan APIDemoA)
	// 创建接口B传输结果的通道
	execBResult := make(chan APIDemoB)

	// 并发调用接口A
	go func(execAResult chan<- APIDemoA) {
		// 模拟接口A远程调用过程
		time.Sleep(2 * time.Second)
		execAResult <- APIDemoA{}
	}(execAResult)

	// 并发调用接口B
	go func(execBResult chan<- APIDemoB) {
		// 模拟接口B远程调用过程
		time.Sleep(1 * time.Second)
		execBResult <- APIDemoB{}
	}(execBResult)

	var resultA APIDemoA
	var resultB APIDemoB
	i := 0
	for {
		if i >= 2 {
			fmt.Println("退出")
			break
		}
		select {
		case resultA = <-execAResult: // 等待接口A的响应结果
			i++
			fmt.Println("resultA", resultA)
		case resultB = <-execBResult: // 等待接口B的响应结果
			i++
			fmt.Println("resultB", resultB)
		}
	}
}
```

### 8. 单元测试&基准测试

> 作用：开发阶段调试代码块、接口；对代码块、接口做基准测试，分析性能问题，包含CPU使用、内存使用等。可做对比测试。ci阶段检测代码质量减少bug。

使用：

#### 8.1 单元测试

一个很简单的单元测试示例：
```go
package main

import (
	"io/ioutil"
	"net/http"
	"testing"
)

func TestDemo(t *testing.T) {
	t.Parallel()
	// 模拟调用接口
	resp, err := http.Get("http://example.com?user_id=121212")
	if err != nil {
		t.Error(err)
		return
	}
	body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		t.Error(err)
		return
	}
	t.Log("body", string(body))
}

// 执行
// go test -timeout 30s -run ^TestDemo$ demo -v -count=1
// === RUN   TestDemo
// === PAUSE TestDemo
// === CONT  TestDemo
// ......
// --- PASS: TestDemo (0.45s)
// PASS
// ok      demo    1.130s
```

多个测试用例的单元测试示例：
```go
package main

import (
	"fmt"
	"io/ioutil"
	"net/http"
	"testing"
)

type Req struct {
	UserID int64
}

func TestDemo(t *testing.T) {
	t.Parallel()
	tests := []struct {
		TestName string
		*Req
	}{
		{
			TestName: "测试用例1",
			Req: &Req{
				UserID: 12121212,
			},
		},
		{
			TestName: "测试用例2",
			Req: &Req{
				UserID: 829066,
			},
		},
	}
	for _, v := range tests {
		t.Run(v.TestName, func(t *testing.T) {
			// 模拟调用接口
			url := fmt.Sprintf("http://example.com?user_id=%d", v.UserID)
			resp, err := http.Get(url)
			if err != nil {
				t.Error(err)
				return
			}
			body, err := ioutil.ReadAll(resp.Body)
			if err != nil {
				t.Error(err)
				return
			}
			t.Log("body", string(body), url)
		})
	}
}

// 执行
// go test -timeout 30s -run ^TestDemo$ demo -v -count=1
// === RUN   TestDemo
// === PAUSE TestDemo
// === CONT  TestDemo
// === RUN   TestDemo/测试用例1
// ...
// === RUN   TestDemo/测试用例2
// ...
// --- PASS: TestDemo (7.34s)
//     --- PASS: TestDemo/测试用例1 (7.13s)
//     --- PASS: TestDemo/测试用例2 (0.21s)
// PASS
// ok  	demo	7.984s

```

#### 8.2 基准测试

简单的基准测试：
```go
package main

import (
	"sync"
	"testing"
)

// 压力测试sync.Map
func BenchmarkSyncMap(b *testing.B) {
	demoMap := &sync.Map{}
	b.RunParallel(func(pb *testing.PB) {
		for pb.Next() {
			demoMap.Store("a", "a")
			for i := 0; i < 1000; i++ {
				demoMap.Load("a")
			}
		}
	})
}

// go test -benchmem -run=^$ -bench ^(BenchmarkSyncMap)$ demo -v -count=1 -cpuprofile=cpu.profile -memprofile=mem.profile -benchtime=10s

// goos: darwin
// goarch: amd64
// pkg: demo
// BenchmarkSyncMap
// BenchmarkSyncMap-4
//   570206	     23047 ns/op	      16 B/op	       1 allocs/op
// PASS
// ok  	demo	13.623s
```

对比基准测试：
```go
package main

import (
	"sync"
	"testing"
)

// 压力测试sync.Map
func BenchmarkSyncMap(b *testing.B) {
	demoMap := &sync.Map{}
	b.RunParallel(func(pb *testing.PB) {
		for pb.Next() {
			demoMap.Store("a", "a")
			for i := 0; i < 1000; i++ {
				demoMap.Load("a")
			}
		}
	})
}

// 用读写锁实现一个并发map
type ConcurrentMap struct {
	value map[string]string
	mutex sync.RWMutex
}

// 写
func (c *ConcurrentMap) Store(key string, val string) {
	c.mutex.Lock()
	defer c.mutex.Unlock()
	if c.value == nil {
		c.value = map[string]string{}
	}
	c.value[key] = val
}

// 读
func (c *ConcurrentMap) Load(key string) string {
	c.mutex.Lock()
	defer c.mutex.Unlock()
	return c.value[key]
}

// 压力测试并发map
func BenchmarkConcurrentMap(b *testing.B) {
	demoMap := &ConcurrentMap{}
	b.RunParallel(func(pb *testing.PB) {
		for pb.Next() {
			demoMap.Store("a", "a")
			for i := 0; i < 1000; i++ {
				demoMap.Load("a")
			}
		}
	})
}

// go test -benchmem -run=^$ -bench . demo -v -count=1 -cpuprofile=cpu.profile -memprofile=mem.profile -benchtime=10s

// goos: darwin
// goarch: amd64
// pkg: demo
// BenchmarkSyncMap
// BenchmarkSyncMap-4   	  668082	     15818 ns/op	      16 B/op	       1 allocs/op
// BenchmarkConcurrentMap
// BenchmarkConcurrentMap-4       	  171730	     67888 ns/op	       0 B/op	       0 allocs/op
// PASS
// coverage: 0.0% of statements
// ok  	demo	23.823s

```
### 9.性能分析

> 作用：CPU分析、内存分析。通过可视化调用链路、可视化火焰图、TOP函数等快速定位代码问题、提升代码性能。

- pprof
- trace
- dlv

使用：

#### 9.1 pprof的使用

##### 9.1.1 基准测试场景

1. 首先编写基准测试用例，复用上面`sync.Map`的用例：
```go
package main

import (
	"sync"
	"testing"
)

// 压力测试sync.Map
func BenchmarkSyncMap(b *testing.B) {
	demoMap := &sync.Map{}
	b.RunParallel(func(pb *testing.PB) {
		for pb.Next() {
			demoMap.Store("a", "a")
			for i := 0; i < 1000; i++ {
				demoMap.Load("a")
			}
		}
	})
}
```

2. 执行基准测试，生成`cpu.profile`文件和`mem.profile `文件。命令如下

> go test -benchmem -run=^$ -bench ^BenchmarkSyncMap$ demo -v -count=1 -cpuprofile=cpu.profile -memprofile=mem.profile -benchtime=10s

常用参数解释：
```
-benchmem: 输出内存指标
-run: 正则，指定需要test的方法
-bench: 正则，指定需要benchmark的方法
-v: 即使成功也输出打印结果和日志
-count: 执行次数
-cpuprofile: 输出cpu的profile文件
-memprofile: 输出内存的profile文件
-benchtime: 执行时间

更多参数请查看：
go help testflag
```

3. 使用`go tool`自带的pprof工具分析测试结果。命令如下：

> go tool pprof -http=:8000 cpu.profile 

常用参数解释：
```
-http: 指定ip:port，启动web服务可视化查看分析，浏览器会自动打开页面 http://localhost:8000/ui/
```

<p align="center">
	<h5>可视化选项菜单</h5>
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210710185849.png" style="width:30%">
	<h5>火焰图</h5>
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210710185947.png" style="width:80%">
	<h5>调用链路图</h5>
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210710190017.png" style="width:80%">
	<h5>Top函数</h5>
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210710190040.png" style="width:36%">
</p>


##### 9.1.2 Web服务场景

1. 使用上面全局变量的代码示例，引入`net/http/pprof`包，并单独注册各端口获取pprof数据。

```go
package main

import (
	"net/http"
	// 引入pprof包
	// _代表只执行包内的init函数
	_ "net/http/pprof"

	"github.com/gin-gonic/gin"
)

// 全局变量不会像PHP一样，在完成一次请求之后被销毁
var GlobalVarDemo int32 = 0

// 模拟接口逻辑
func main() {
	r := gin.Default()
	r.GET("/ping", func(c *gin.Context) {
		GlobalVarDemo++
		c.JSON(200, gin.H{
			"message": GlobalVarDemo,
		})
	})
	// 再开启一个端口获取pprof数据
	go func() {
		http.ListenAndServe(":8888", nil)
	}()
	// 启动web服务
	r.Run()
}
```

2. 访问链接 `http://localhost:8888/debug/pprof/`，可以看见相关profiles。

<p align="center">
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210710195214.png" style="width:50%">
</p>

3. 命令使用pprof工具，获取远程服务profile，命令如下：

> go tool pprof -http=:8000 http://localhost:8888/debug/pprof/profile?seconds=5

```
备注：
执行上面命令的时候，可以使用压测工具模拟流量，比如命令：siege -c 50 -t 100 "http://localhost:8080/ping"
```

同样，我们得到了这个熟悉的页面：

<p align="center">
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210710200849.png" style="width:50%">
</p>

#### 9.2 trace工具的使用

作用：清晰查看每个逻辑处理器中Goroutine的执行过程，可以很直观看出Goroutine的阻塞消耗，包含网络阻塞、同步阻塞(锁)、系统调用阻塞、调度等待、GC执行耗时、GC STW(Stop The World)耗时。

##### 9.2.1 基准测试场景

使用：

```
生成trace.out文件命令：
go test -benchmem -run=^$ -bench ^BenchmarkDemo_NoPool$ demo -v -count=1 -trace=trace.out 
go test -benchmem -run=^$ -bench ^BenchmarkDemo_Pool$ demo -v -count=1 -trace=trace.out 

分析trace.out文件命令：
go tool trace -http=127.0.0.1:8000 trace.out
```

<p align="center">
	<h5>没使用sync.Pool</h5>
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210711180143.png" style="width:30%">
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210711175446.png" style="width:30%">
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210711175358.png" style="width:80%">
</p>

<p align="center">
	<h5>使用sync.Pool</h5>
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210711175727.png" style="width:80%">
</p>


##### 9.2.2 Web服务场景

使用：

同样引入包`net/http/pprof`
```go
package main

import (
	"net/http"
	// 引入pprof包
	// _代表只执行包内的init函数
	_ "net/http/pprof"

	"github.com/gin-gonic/gin"
)

// 全局变量不会像PHP一样，在完成一次请求之后被销毁
var GlobalVarDemo int32 = 0

// 模拟接口逻辑
func main() {
	r := gin.Default()
	r.GET("/ping", func(c *gin.Context) {
		GlobalVarDemo++
		c.JSON(200, gin.H{
			"message": GlobalVarDemo,
		})
	})
	// 再开启一个端口获取pprof数据
	go func() {
		http.ListenAndServe(":8888", nil)
	}()
	// 启动web服务
	r.Run()
}

```

启动服务后执行如下命令：
```
1. 
生成trace.out文件命令：
curl http://localhost:8888/debug/pprof/trace?seconds=20 > trace.out

和上面命令同时执行，模拟请求，也可以用ab：
siege -c 50 -t 100 "http://localhost:8080/ping"

2. 分析trace.out文件命令：
go tool trace -http=127.0.0.1:8000 trace.out

快捷健：
w 放大
e 右移
```

<p>
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210711212814.png" style="width:80%">
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210711213345.png" style="width:80%">
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210711213416.png" style="width:30%">
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210711213519.png" style="width:80%">
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210711214610.png" style="width:80%">
</p>

#### 9.3 dlv工具的使用
	
##### 9.3.1 基准测试场景

作用：断点调试等。

安装：

```
go install github.com/go-delve/delve/cmd/dlv@latest
```

使用：


```go
package main

import (
	_ "net/http/pprof"

	"github.com/gin-gonic/gin"
)

// 全局变量不会像PHP一样，在完成一次请求之后被销毁
var GlobalVarDemo int32 = 0

// 模拟接口逻辑
func main() {
	r := gin.Default()
	r.GET("/ping", func(c *gin.Context) {
		GlobalVarDemo++
		c.JSON(200, gin.H{
			"message": GlobalVarDemo,
		})
	})
	r.Run()
}

```

命令行执行命令:

> dlv debug main.go

进入调试，常用调试命令：

- （list或l：输出代码）：list main.go:16
- （break或b：断点命令）：执行 `break main.go:16` 给行 `GlobalVarDemo++`打断点
- （continue或c：继续执行）：continue
- （print或p：打印变量）：`print GlobalVarDemo`
- （step或s：可以进入函数）：step

更多命令请执行 `help`。

```
模拟请求：
curl http://localhost:8080/ping
```

<p align="center">
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210711164556.png" style="width:80%">
</p>

<p align="center">
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210711172959.png" style="width:80%">
</p>

##### 9.3.2 Web服务场景

还是这个demo
```go
package main

import (
	"github.com/gin-gonic/gin"
)

// 全局变量不会像PHP一样，在完成一次请求之后被销毁
var GlobalVarDemo int32 = 0

// 模拟接口逻辑
func main() {
	r := gin.Default()
	r.GET("/ping", func(c *gin.Context) {
		GlobalVarDemo++
		c.JSON(200, gin.H{
			"message": GlobalVarDemo,
		})
	})
	// 启动web服务
	r.Run()
}

```

- 找到服务进程ID `lsof -i :8080`
- dlv调试进程 `dlv attach 36968`
- 进入调试模式，调试代码（和上面一样）

#### 9.4(扩展) 逃逸分析

> 逃逸分析命令：go build -gcflags "-m -l" *.go

```go 
package main

type Demo struct {
}

func main() {
	DemoFun()
}

func DemoFun() *Demo {
	demo := &Demo{}
	return demo
}

// # command-line-arguments
// ./main.go:11:10: &Demo literal escapes to heap <------- 局部变量内存被分配到堆上
```

#### 9.5(扩展) 汇编代码

> 直接生成汇编代码命令：go run -gcflags -S main.go

```
# command-line-arguments
"".main STEXT nosplit size=1 args=0x0 locals=0x0
        0x0000 00000 (.../demo/main.go:6)  TEXT    "".main(SB), NOSPLIT|ABIInternal, $0-0
        0x0000 00000 (.../demo/main.go:6)  FUNCDATA        $0, gclocals·33cdeccccebe80329f1fdbee7f5874cb(SB)
        0x0000 00000 (.../demo/main.go:6)  FUNCDATA        $1, gclocals·33cdeccccebe80329f1fdbee7f5874cb(SB)
        0x0000 00000 (.../demo/main.go:6)  FUNCDATA        $2, gclocals·33cdeccccebe80329f1fdbee7f5874cb(SB)
        0x0000 00000 (<unknown line number>)    RET
        0x0000 c3       
		略......                              
```

> 获取生成汇编代码整个优化过程：GOSSAFUNC=main go build main.go


```
dumped SSA to ./ssa.html <------- 生成的文件，浏览器打开此文件
```

<p align="center">
	<img src="http://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/20210714144240.png" style="width:100%">
</p>

## 总结

最后我们再总结下，从PHPer到Gopher的过程，我们重点需要关注的几点如下：

- PHP和Go常用代码块的对应关系
- 常驻内存
	+ 全局变量使用
	+ 资源
		* 复用
		* 释放
		* 返还
- 指针
- 并发
	+ 并发安全
	+ 并发控制
	+ 超时控制
- 单元测试&基准测试
- 性能分析
