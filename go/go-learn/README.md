# 延时队列实现方案

> 参考： https://mp.weixin.qq.com/s/DcyXPGxXFYcXCQJII1INpg

1. Redis zset

```
1. 添加任务 `zadd {{key}} {{时间戳}} {{任务名称}}`
2. 每秒定时任务执行 `zrangebyscore {{key}} -inf +inf limit 0 1 WITHSCORES`
    2.1 如果score大于当前时间戳 则donothing 等待下秒定时任务
    2.2 如果score小于等于当前时间戳 则异步执行任务
```

2. RabbitMQ

```
1. 进程消费死信队列
2. 延时消息设置ttl
3. 消息过期被投递到死信队列
```

3. 时间轮

```

```

# 优先队列实现方案

> 参考：https://www.cxyxiaowu.com/5417.html

1. 二叉堆

-------------------
# Go语言设计与实现

## Go语言编译大致过程

```
词法分析
|
|
V
语法分析
|
|
V
抽象语法树(AST)
|
|
V
类型检查
|
|
V
中间代码
|
|
V
机器码

```

## 内存

```
静态存储区：通常存放 全局变量、常量、静态变量

堆：手动申请、分配、释放

栈：局部变量，自动申请、分配、释放
```

```
x86 栈内存由高地址向低地址分配 堆内存由低地址向高地址分配
```

## 数组和切片

### 数组

```
连续内存
```

### 切片 

len当前切片的长度
cap当前切片的容量
指针：指向数组(连续内存)的开始位置

切片cap的扩容`growslice`:

1. 「切片期望的容量的值」大于「当前切片容量的值」的两倍 则扩容「切片期望的容量的值」
2. 「切片期望的容量的值」小于「当前切片容量的值」的两倍 
    - 「当前切片长度」小于1024 则扩容「当前容量的值」的两倍
    - 「当前切片长度」大于1024 则持续扩容「当前切片容量的值」的1/4 直到 「当前切片容量的值」大于0 且「当前切片容量的值」大于「切片期望的容量的值」

## uintptr unsafe.Pointer

uintptr 指针 可计算偏移量

unsafe.Pointer 指针 可存放任意类型的地址 unsafe.Pointer可转成uintptr类型 unsafe.Offsetof()获取偏移量

```
var a float64 = 1.0
var b *int64
b = (*int64)(unsafe.Pointer(&a))
```

```
d := &demo{
    PropertyOne: "one",
    PropertyTwo: "two",
}

dTwoPointer := (*string)(unsafe.Pointer(uintptr(unsafe.Pointer(d)) + unsafe.Offsetof(d.PropertyTwo)))
*dTwoPointer = "three"
fmt.Println("d.PropertyTwo", d.PropertyTwo)
```

> 参考 https://www.flysnow.org/2017/07/06/go-in-action-unsafe-pointer.html

## Map

数组->数组

```
buckets
```

## 内存管理

内存对齐？

---

> 一个指针8字节？ 

- 为何64位下一个指针大小为8个字节？https://www.jianshu.com/p/14b4bc2a76cc
- 为什么一个指针在32位系统中占4个字节，在64位系统中占8个字节？https://www.cnblogs.com/gaoxiaoniu/p/10677754.html

> 地址总线?

- 总结总线的一些基本知识 https://www.junmajinlong.com/os/bus/

> CPU的寻址能力为什么以字节为单位？

- 32位cpu 内存空间4GB 是怎么算的？为什么单位从b变成了B? https://www.zhihu.com/question/61974351
- 8位、16位、32位操作系统的区别 https://blog.csdn.net/luckyzhoustar/article/details/80384827
- 存储单元是CPU访问存储器的基本单位 https://baike.baidu.com/item/%E5%AD%98%E5%82%A8%E5%8D%95%E5%85%83

> 寻址?

```
存储器(这里指计算机的内存)由存储单位构成
存储单元 = 8bit = 1byte

---

储存器地址就是储存单元的编号
```

> CPU的寻址范围?

```
32位CPU：
一般指的是地址总线的宽度 -> 32位 -> 0 ~ 2^32-1(bit）
64位CPU：
一般指的是地址总线的宽度 -> 64位 -> 0 ~ 2^64-1(bit）

地址总线的1bit ---(对应)---> 1个存储单元 == 8位(bit) == 1byte

---

32位 -> 0 ~ 2^32-1(bit）---(对应)---> 0 ~ 2^32-1(byte）容量 == 4G
64位 -> 0 ~ 2^64-1(bit） == 0X0000 0000 0000 0000 ~ 0XFFFF FFFF FFFF FFFF ---(对应)---> 0 ~ 2^64-1(byte）容量 （16 EB）
```

- 寄存器，存储器，RAM，ROM有什么区别？https://www.zhihu.com/question/288534298
- 64位linux操作系统每个进程分配的虚拟内存有多大，4G还是说2的64次方？https://www.zhihu.com/question/265014061

- Golang 是否有必要内存对齐？https://ms2008.github.io/2019/08/01/golang-memory-alignment/
- 在 Go 中恰到好处的内存对齐 https://zhuanlan.zhihu.com/p/53413177

- 你真的了解虚拟内存和物理内存吗 https://juejin.cn/post/6844903970981281800
- 为什么 Linux 需要虚拟内存 https://draveness.me/whys-the-design-os-virtual-memory/

golangci-lint

```
1. 为什么64位平台Go语言里的指针`uintptr`的实际类型是uint64?

2. 为什么32位平台的最大寻址空间是4GB？那64位平台的寻址空间又是多大？---> 为什么CPU的寻址能力以字节(Byte)为单位？

3. 为什么需要虚拟内存？

4. 我们知道32位平台下，每个进程对应4GB虚拟内存，1GB高地址作为内核空间，3GB低地址作为用户空间。
那么：64位平台下是如何分配内核空间和用户空间的？

5. 当我们看到Go的内存对齐，这到底是在干些什么？
```

---

TCMalloc

- 图解 TCMalloc https://zhuanlan.zhihu.com/p/29216091
- TCMalloc解密 https://wallenwang.com/2018/11/tcmalloc/
- TCMalloc : Thread-Caching Malloc https://github.com/google/tcmalloc/blob/master/docs/design.md
- 可利用空间表（Free List）https://songlee24.github.io/2015/04/08/free-list/

page 8kb