# 

> 源码版本(commit:9d274df) https://github.com/google/tcmalloc/tree/master/tcmalloc

## 导读

```
本文基于64位平台、1Page=8KB
```

今天我们开始拉开《Go语言轻松系列》第二章「内存与垃圾回收」的序幕。

<p align="center">
  <img src="http://cdn.tigerb.cn/20210109200839.png" style="width:60%;box-shadow: 3px 3px 3px 3px #ddd;">
</p>

关于「内存与垃圾回收」章节，大体分为如下三大部分展开：

- 知识预备：为后续的内容做一些知识储备
- Go内存设计与实现
- Go的垃圾回收原理

## 目录

- 指针自身的大小为什么是8字节(64位平台)？
- 内存的线性分配
- 什么是`Freelist`？
- 什么是`Tcmalloc`？
  - `Page`的概念
  - `Span`的概念
  - `Object`的概念
- `Tcmalloc`的基本结构
  - `PageHeap`的概念
  - `CentralFreeList`和`TransferCacheManager`的概念
  - ThreadCache的概念
- `Tcmalloc`基本结构的依赖关系(简易)
- `Tcmalloc`基本结构的依赖关系(详细)

## 64位平台下，指针自身的大小为什么是8字节？

第一部分`知识预备`的第一个知识点`指针的大小`。

为什么这个会作为一个知识点呢？因为后续内存管理的内容会涉及一些数据结构，这些数据结构使用到了指针，同时存储指针的值是需要内存空间的，所以我们需要了解指针的大小，便于我们理解一些设计的意图；其次，这也是困扰我的一个问题，因为有看见64位平台下指针底层定义的类型为`uint64`。

为了搞清楚这个问题，我们需要了解两个知识点：

1. 存储单元
2. CPU总线

### 什么是存储单元？

存储单元是存储器(本文指内存)的基本单位，每个存储单元是8bit，也就是1Byte，如下图所示：
<p align="center">
  <img src="http://cdn.tigerb.cn/20210121193201.png" style="width:100%">
</p>

同时从上图中我们可以看出，每个存储单元会被编号，这个编号又是什么呢？

- 就是我们通常所谓的“内存的地址”
- 也就是指针的值

> 结论：指针的值就是存储单元的编号。

接着，我们只需要知道这个「编号」的最大值是多少，就可以知道存储「指针」的值所需的大小。要找到这个最大值就需要了解**CPU总线**的知识了。

### CPU总线的概念

CPU总线由系统总线、等等其他总线组成。

|总线的组成|
|---|
|系统总线|
|等等其他总线...|

系统总线由一系列总线组成。

|系统总线的祖册|
|---|
|地址总线|
|数据总线|
|信号总线|

内存的地址(存储单元的编号)是通过**地址总线**传递的，地址总线里的“每一根线”传递二进制`0`或`1`，如下图所示(实际不是这么简单，图示为了便于大家理解)。

<p align="center">
  <img src="http://cdn.tigerb.cn/20210121194127.png" style="width:100%">
</p>

地址总线的**宽度**决定了一次能传递多少个`0`或`1`，由于64位CPU每次可处理64位数据，所以理论上地址总线的宽度可以支持到最大64，也就是2^64种组合，可代表的数字范围为`0 ~ 2^64-1`。

> 结论：理论上64位CPU地址总线可传输的10进制数范围为`0 ~ 2^64-1`。

上面知道64位CPU的地址总线可寻址范围 为 `0 ~ 2^64-1`，需要一个类型可以存储这个指针的值，毫无疑问就是`uint64`，`uint64`又是多大呢？是不是8byte。所以：**64位平台下，一个指针的大小是8字节**。

顺便扩充个问题：

> 为什么32位平台下，可寻址空间是4GB？

```
备注：64位太大，我们这里用32位来看这个问题
```

我们来分析一下：

- 由于，32位平台可支持地址总线的最大宽度为32，及代表的存储单元编号的范围：0 ~ 2^32-1
- 则，最多可以找到2^32个存储单元
- 又有，存储单元的大小为8bit(1Byte)

所以我们可以得到，32位平台最多可以寻找到**2^32个存储单元**，再翻译下**2^32个存储单元**这句话：

> 2^32个存储单元 == 2^32个1Byte == 2^32Byte == 4GByte

总结下本次内容可以get到的知识点如下：

- 存储器的基本单位是存储单元
- 存储单元为8bit
- 指针的值就是存储单元的编号
- CPU地址总线的宽度决定了指针的值的最大范围

## 内存的线性分配

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120131804.png" style="width:100%">
</p>

## 什么是`Freelist`？

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120131820.png" style="width:100%">
</p>


## 什么是`Tcmalloc`？

讲`Tcmalloc`前我们先来了解三个概念：

- `Page`的概念
- `Span`的概念
- `Object`的概念

### `Page`的概念

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120131944.png" style="width:100%">
</p>

### `Span`的概念

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120131951.png" style="width:100%">
</p>

### `Object`的概念

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132002.png" style="width:100%">
</p>

再来看看`Tcmalloc`的基本结构

## 基本结构

主要由三部分构成

- `PageHeap`的概念
- `CentralFreeList`和TransferCacheManager的概念
- ThreadCache的概念

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132020.png" style="width:60%">
</p>

``CentralFreeList``被`TransferCacheManager`管理

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132031.png" style="width:80%">
</p>

`ThreadCache`被线程持有

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132037.png" style="width:80%">
</p>


分别看看三部分的详细概念

- `PageHeap`的概念
- `CentralFreeList`和TransferCacheManager的概念
- ThreadCache的概念

## `PageHeap`的概念

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132117.png" style="width:100%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132136.png" style="width:100%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132145.png" style="width:100%">
</p>

## `CentralFreeList`和TransferCacheManager的概念
### `CentralFreeList`

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132206.png" style="width:100%">
</p>

## TransferCacheManager

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132218.png" style="width:100%">
</p>

## ThreadCache的概念

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132229.png" style="width:100%">
</p>

## `Tcmalloc`基本结构的依赖关系(简易)

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132244.png" style="width:66%">
</p>

## `Tcmalloc`基本结构的依赖关系(详细)

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132256.png" style="width:100%">
</p>


## 结语


```
参考：
1. tcmalloc源码(commit:9d274df) https://github.com/google/tcmalloc/tree/master/tcmalloc
2. 可利用空间表（Free List）https://songlee24.github.io/2015/04/08/free-list/
3. 图解 TCMalloc https://zhuanlan.zhihu.com/p/29216091
4. TCMalloc解密 https://wallenwang.com/2018/11/tcmalloc/
5. TCMalloc : Thread-Caching Malloc https://github.com/google/tcmalloc/blob/master/docs/design.md
6. TCMalloc : Thread-Caching Malloc https://gperftools.github.io/gperftools/tcmalloc.html
7. tcmalloc原理剖析(基于gperftools-2.1) http://gao-xiao-long.github.io/2017/11/25/tcmalloc/
```