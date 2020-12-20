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

