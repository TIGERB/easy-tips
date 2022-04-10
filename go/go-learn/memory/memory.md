# 一大波更新预告

号外！号外！！本月将迎来一大波更新，目前看起来大概是5篇左右的文章。知识点不少，目的帮助大家夯实基础、拓宽复杂系统设计思路。

本波更新主要为今年Q1季度学习过程的沉淀和输出，更新的核心内容是关于「Go语言内存管理」的设计与实现。

由于「Go语言内存管理」是基于`TCMalloc`理念设计的，所以去年按计划阅读了`TCMalloc`的源码和大量文章。我的学习思路是先了解`TCMalloc`的设计与实现，再反过来看Go语言的实现，可以更好帮助我们理解「Go语言内存管理」的设计与实现。

预告！！！后续5篇文章主题：

- Go内存管理架构
- 为什么线程缓存`mcache`是被逻辑处理器`p`持有，而不是系统线程`m`?
- Go内存管理单元`mspan`
- Go堆内存的分配
- Go栈内存的分配

每篇文章会以周为单位推送。

不过开启这个新篇章的时候，我还是希望大家可以提前温故知新，看看之前知识储备的内容，目的是帮助大家更好理解接下来的内容。这也是我发预告的重要原因😁😁😁。

知识储备内容如下(点击下方查看)：

- [指针的大小](http://tigerb.cn/go/#/kernal/memory-pointer)
- [内存的线性分配](http://tigerb.cn/go/#/kernal/tCMalloc?id=%e5%86%85%e5%ad%98%e7%9a%84%e7%ba%bf%e6%80%a7%e5%88%86%e9%85%8d)
- [什么是FreeList？](http://tigerb.cn/go/#/kernal/tCMalloc?id=%e4%bb%80%e4%b9%88%e6%98%affreelist%ef%bc%9f)
- [虚拟内存](http://tigerb.cn/go/#/kernal/tCMalloc?id=%e8%99%9a%e6%8b%9f%e5%86%85%e5%ad%98)
- [TCMalloc内存分配原理](http://tigerb.cn/go/#/kernal/tCMalloc?id=%e4%bb%80%e4%b9%88%e6%98%aftCMalloc%ef%bc%9f)

另外本篇章的内容会归档到《Go语言轻松系列》第二章「内存与垃圾回收」第二部分「Go内存设计与实现」中，方便大家后续查阅。

<p align="center">
  <img src="http://cdn.tigerb.cn/20210109200839.png" style="width:60%;box-shadow: 3px 3px 3px 3px #ddd;">
</p>

# 目录

- Go内存管理架构
    + `mcache`
    + `mcentral`
    + `mheap`
- 为什么线程缓存`mcache`是被逻辑处理器`p`持有，而不是系统线程`m`?
- Go内存管理单元`mspan`
    + `page`的概念
    + `mspan`的概念
    + `object`的概念
    + `heaparena`的概念
    + `chunk`的概念
- Go堆内存的分配
    + 微对象分配
    + 小对象分配
    + 大对象分配
- Go栈内存的分配
    + 栈内存分配时机
    + 小于32KB的栈分配
    + 大于等于32KB的栈分配


# 导读

```
本文基于Go源码版本1.16、64位Linux平台、1Page=8KB、本文的内存特指虚拟内存
```

今天我们开始进入《Go语言轻松系列》第二章「内存与垃圾回收」第二部分「Go内存设计与实现」。

<p align="center">
  <img src="http://cdn.tigerb.cn/20210109200839.png" style="width:60%;box-shadow: 3px 3px 3px 3px #ddd;">
</p>

关于「内存与垃圾回收」章节，会从如下三大部分展开：

- 读前知识储备(已完结)
	+ [指针的大小](http://tigerb.cn/go/#/kernal/memory-pointer)
    + [内存的线性分配](http://tigerb.cn/go/#/kernal/tCMalloc?id=%e5%86%85%e5%ad%98%e7%9a%84%e7%ba%bf%e6%80%a7%e5%88%86%e9%85%8d)
    + [什么是FreeList？](http://tigerb.cn/go/#/kernal/tCMalloc?id=%e4%bb%80%e4%b9%88%e6%98%affreelist%ef%bc%9f)
    + [虚拟内存](http://tigerb.cn/go/#/kernal/tCMalloc?id=%e8%99%9a%e6%8b%9f%e5%86%85%e5%ad%98)
	+ [TCMalloc内存分配原理](http://tigerb.cn/go/#/kernal/tCMalloc?id=%e4%bb%80%e4%b9%88%e6%98%aftCMalloc%ef%bc%9f)
- Go语言内存管理(当前部分)
- Go语言垃圾回收原理(未开始)

第一部分「读前知识储备」已经完结，为了更好理解本文大家可以点击历史链接进行查看或复习。

# 目录

关于讲解「Go语言内存管理」部分我的思路如下：

1. 介绍整体架构
2. 介绍架构设计中一个很有意思的地方
3. 通过介绍Go内存管理中的关键结构`mspan`，带出`page`、`mspan`、`object`、`sizeclass`、`spanclass`、`heaparena`、`chunk`的概念
4. 接着介绍堆内存、栈内存的分配
5. 回顾和总结

通过这个思路拆解的目录：

- Go内存管理架构(本篇内容)
    + `mcache`
    + `mcentral`
    + `mheap`
- 为什么线程缓存`mcache`是被逻辑处理器`p`持有，而不是系统线程`m`?
- Go内存管理单元`mspan`
    + `page`的概念
    + `mspan`的概念
    + `object`的概念
    + `sizeclass`的概念
    + `spanclass`的概念
    + `heaparena`的概念
    + `chunk`的概念
- Go堆内存的分配
    + 微对象分配
    + 小对象分配
    + 大对象分配
- Go栈内存的分配
    + 栈内存分配时机
    + 小于32KB的栈分配
    + 大于等于32KB的栈分配

# Go内存管理架构

Go的内存统一由内存管理器管理的，Go的内存管理器是基于Google自身开源的`TCMalloc`内存分配器为理念设计和实现的，关于`TCMalloc`内存分配器的详细介绍可以查看之前的文章。

先来简单回顾下`TCMalloc`内存分配器的核心设计。

## 回顾`TCMalloc`内存分配器

> `TCMalloc`诞生的背景？

在多核以及超线程时代的今天，多线程技术已经被广泛运用到了各个编程语言中。当使用多线程技术时，由于**多线程共享内存**，线程申在请内存(虚拟内存)时，由于并行问题会产生竞争不安全。

为了保证分配内存的过程足够安全，所以需要在内存分配的过程中加锁，加锁过程会带来阻塞影响性能。之后就诞生了`TCMalloc`内存分配器并被开源。

> `TCMalloc`如何解决这个问题?

`TCMalloc`全称`Thread Cache Memory alloc`线程缓存内存分配器。顾名思义就是给线程添加内存缓存，减少竞争从而提高性能，当线程内存不足时才会加锁去共享的内存中获取内存。

接着我们来看看`TCMalloc`的架构。

> `TCMalloc`的架构？

`TCMalloc`三层逻辑架构

- `ThreadCache`：线程缓存
- `CentralFreeList`(CentralCache)：中央缓存
- `PageHeap`：堆内存

> `TCMalloc`架构上不同的层是如何协作的？

`TCMalloc`把申请的内存对象按大小分为了两类：

- 小对象 <= 256 KB
- 大对象 > 256 KB

我们这里以分配小对象为例，当给小对象分配内存时：
- 先去线程缓存`ThreadCache`中分配
- 当线程缓存`ThreadCache`的内存不足时，从对应`SizeClass`的中央缓存`CentralFreeList`获取
- 最后，再从对应`SizeClass`的`PageHeap`中分配

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132244.png" style="width:66%">
</p>

## Go内存分配器的逻辑架构

采用了和`TCMalloc`内存分配器一样的三层逻辑架构：

- `mcache`：线程缓存
- `mcentral`：中央缓存
- `mheap`：堆内存

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405133623.png" style="width:60%">
</p>

实际中央缓存`central`是一个由136个`mcentral`类型元素的数组构成。

除此之外需要特别注意的地方：`mcache`被逻辑处理器`p`持有，而并不是被真正的系统线程`m`持有。(这个设计很有意思，后续会有一篇文章来解释这个问题)

我们更新下架构图如下：

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405224809.png" style="width:60%">
</p>

「Go内存分配器」把申请的内存对象按大小分为了三类：

- 微对象 0 < Micro Object < 16B
- 小对象 16B =< Small Object <= 32KB
- 大对象 32KB < Large Object

为了清晰看出这三层的关系，这里以堆上分配小对象为例：

- 先去线程缓存`mcache`中分配内存
- 找不到时，再去中央缓存`central`中分配内存
- 最后直接去堆上`mheap`分配一块内存


<p align="center">
  <img src="http://cdn.tigerb.cn/20220405224348.png" style="width:80%">
</p>

## 总结

通过以上的分析可以看出Go内存分配器的设计和开源`TCMalloc`内存分配器的理念、思路基本一致。对比图如下：

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405225026.png" style="width:100%">
</p>

最后我们总结下：

- Go内存分配器采用了和`TCMalloc`一样的三层架构。逻辑上为：
  + `mcache`：线程缓存
  + `mcentral`：中央缓存
  + `mheap`：堆内存
- 线程缓存`mcache`是被逻辑处理器`p`持有，而不是系统线程`m`

# 《Go内存管理实现中一个很有意思的地方》


通过之前的文章我们了解了`TCMalloc`内存分配器的核心设计思路 增加了线程缓存

多线程去堆上分配内存 由于并行和并发需要加锁竞争 

为了减少加锁过程 在每个线程上添加内存缓存


但是实际上Go的内存管理器的线程缓存是`mcache`被逻辑处理器`p`持有，而并不是被真正的系统线程`m`持有。

> 为什么？

这就是这个很有意思的地方：

> 为什么Go的内存管理器的线程缓存是`mcache`被逻辑处理器`p`持有，而并不是被真正的系统线程`m`持有?

想理解这个问题，需要了解Go的调度原理，当然接触Go之后大家肯定都了解Go的调度模型`GMP`

我先来简单解释下

- Go是核心通过逻辑物理器`p`在用户态调度协程`g`实现的并发能力，而不是完全依赖多线程技术。

这也不能说明 `mcache`应该被逻辑处理器`p`持有

是的 我们先记住这个简单的结论：`p`调度`g`实现并发

有人说`GMP`模型，不是还用到了`m`了吗？

是的，完全没问题。虽然是`p`调度`g`实现并发，但是`p`只是Go的一个逻辑结构，通常我们通过设置保证`p`等于或者小于计算机真实逻辑处理器的数量，然而`g`的执行需要真正系统线程`m`，所以需要给`p`绑定一个真正系统线程`m`去执行`g`上的代码

为了便于理解，这里可以看做`p`和`m`的数量是一一对应的

- 1.`p`绑定`m`
- 2.`p`调度`g`
- 3.`m`执行`g`

这样看起来，按照`TCMalloc`的理念，`mcache`应该还是系统线程`m`持有才对啊，是吧？

这么看是的，关键点来了，问题就出现在**3.`m`执行`g`**过程中如果产生了系统调用怎么办？

系统调用产生阻塞，等于：

- `m`、`g`被阻塞
- 进一步导致`p`被阻塞

如果这时候不做任何事情，相当于当前的逻辑处理器`p`完全被阻塞了，假设极端情况下当前`p`绑定的`m`都因为系统调用被阻塞，如果真是这样我们的程序是不是就暂时不工作了。Go当前不允许这样的低级事情发生，怎么做呢？

- `m`发生系统调用会和当前的`p`解绑，这里的`m`我们称之为`m1`，这里的`m1`执行的`g`我们称之为`g1`
- 当前的`p`会绑定一个新的`m`
- `m1`系统调用阻塞结束
    + `m1`放到调度器的m闲置列表
    + 找到闲置的`p`执行`g1`，或者放进全局队列


通过了解上述的内容之后 我们假设`mcache`被`m`持有，假设有`p2`、`m2`、`g2`、`m3`、`g3`

- `p2`绑定`m2`
- `m2`生命周期过程中，把堆上的内存缓存到了线程缓存`m2.mcache`
- `p2`调度`g2`
- `m2`执行`g2`
- `g2`的一段代码触发系统调用
- `m2`和`p2`解绑
- `p2`绑定闲置的或者新创建的线程`m3`

后续`p2`调度执行的`g`就不能及时高效的使用线程缓存`m2.mcache`

假设`mcache`被`p`持有

不管因为系统调用创建多少`m`，都可以复用当前`p.mcache`，极大的提高了内存使用的效率


同时 `p`作为统一控制端，资源都从`p`上获取，`m`只负责具体的计算逻辑，职责分明

这样看起来`mcache`被逻辑处理器`p`持有 是不是更合适？

```go
// 退出系统调用的代码逻辑
// 代码位置
// src/runtime/proc.go::3813
func exitsyscall0(gp *g) {
	_g_ := getg()

	casgstatus(gp, _Gsyscall, _Grunnable)
	dropg()
	lock(&sched.lock)
	var _p_ *p
	if schedEnabled(_g_) {
		_p_ = pidleget()
	}
	if _p_ == nil {
		globrunqput(gp) // 找不到空闲的p 则放进全局队列
	} else if atomic.Load(&sched.sysmonwait) != 0 {
		atomic.Store(&sched.sysmonwait, 0)
		notewakeup(&sched.sysmonnote)
	}
	unlock(&sched.lock)
	if _p_ != nil {
		acquirep(_p_)
		execute(gp, false) // 执行系统调用阻塞的g
	}
	if _g_.m.lockedg != 0 {
		stoplockedm()
		execute(gp, false) // 执行系统调用阻塞的g
	}
	stopm() // 停止m，并放到调度器的m闲置列表
	schedule()
}
```

# Go内存管理单元`mspan`

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405234945.png" style="width:30%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405235014.png" style="width:100%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405235024.png" style="width:100%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405235059.png" style="width:100%">
</p>


- 栈内存
- 堆内存

# 堆内存的分配

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405235337.png" style="width:30%">
</p>

- 微对象 0 < Micro Object < 16B
- 小对象 16B =< Small Object <= 32KB
- 大对象 32KB < Large Object

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405235126.png" style="width:80%">
</p>

mcache:

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405235250.png" style="width:80%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405235037.png" style="width:80%">
</p>

central:

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405234451.png" style="width:80%">
</p>

## 微对象的分配

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405234253.png" style="width:80%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405234330.png" style="width:80%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405234341.png" style="width:80%">
</p>


## 小对象的分配

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405234409.png" style="width:80%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405234425.png" style="width:80%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405234513.png" style="width:80%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405234521.png" style="width:80%">
</p>

## 大对象的分配

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405234609.png" style="width:80%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405234616.png" style="width:80%">
</p>

# 栈内存和堆内存的关系

内存管理器 管理堆内存

问题 栈内存从哪来的呢？

为了了解这个问题 通过栈内存分配的时机以及函数去分析

## 栈内存分配函数

栈内存分配的时机

- 1.创建`Goroutinue`
    + 创建`g0`
    + 创建`g`

创建一个全新`g`函数

```go
// src/runtime/proc.go::3943
// 创建一个指定栈内存的g
func malg(stacksize int32) *g {
	newg := new(g)
	if stacksize >= 0 {
		// ...略
		systemstack(func() {
            // 分配栈内存
			newg.stack = stackalloc(uint32(stacksize))
		})
		// ...略
	}
	return newg
}
```

创建`g0`
```go
// src/runtime/proc.go::1720
// 创建 m
func allocm(_p_ *p, fn func(), id int64) *m {
    // ...略
    if iscgo || mStackIsSystemAllocated() {
		mp.g0 = malg(-1)
	} else {
        // 创建g0 并申请8KB栈内存
		mp.g0 = malg(8192 * sys.StackGuardMultiplier)
	}
    // ...略
}
```

创建`g`
```go
// src/runtime/proc.go::3999
// 创建一个带有任务fn的goroutine
func newproc1(fn *funcval, argp unsafe.Pointer, narg int32, callergp *g, callerpc uintptr) *g {
    // ...略
    newg := gfget(_p_)
	if newg == nil {
        // 全局队列、本地队列找不到g 则 创建一个全新的goroutine
        // _StackMin = 2048
        // 申请2KB栈内存
		newg = malg(_StackMin)
		casgstatus(newg, _Gidle, _Gdead)
		allgadd(newg)
	}
    // ...略
}
```

```
g0申请8KB栈内存
g申请2KB栈内存
不在本章节范围，后续Go的调度系列会介绍
```

- 2.栈扩容

```go
// src/runtime/stack.go::838
func copystack(gp *g, newsize uintptr) {
	// ...略

	// 分配新的栈空间
	new := stackalloc(uint32(newsize))

    // ...略
}
```


都指向了 函数 `stackalloc`

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405133309.png" style="width:50%">
</p>

分析 `stackalloc`

分析 `stackalloc`来源于

全局变量

- 1.`var stackpool`
- 2.`var stackLarge`

进一步分析全局变量 `var stackpool`和`var stackLarge`内存的来源

来自`mheap`

- 栈内存和堆内存都是统一由内存管理器管理`allco`
- 栈内存来自于`mheap`堆内存

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405132922.png" style="width:60%">
</p>

- 小于32KB的栈内存
- 大于32KB的栈内存

### 小于32KB栈分配过程

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405234800.png" style="width:80%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405234810.png" style="width:80%">
</p>


### 大于等于32KB栈分配过程

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405234822.png" style="width:80%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20220405234828.png" style="width:80%">
</p>




CPU
cat /proc/cpuinfo | grep 'physical id' | sort | uniq | wc -l

物理核
cat /proc/cpuinfo | grep 'core id' | sort | uniq | wc -l

逻辑核
cat /proc/cpuinfo | grep 'processor' | sort | uniq | wc -l