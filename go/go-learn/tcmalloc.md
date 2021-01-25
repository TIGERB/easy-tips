# 

> 源码版本(commit:9d274df) https://github.com/google/tcmalloc/tree/master/tcmalloc

## 导读

```
本系列基于64位平台、1Page=8KB
```

今天我们开始拉开《Go语言轻松系列》第二章「内存与垃圾回收」的序幕。

<p align="center">
  <img src="http://cdn.tigerb.cn/20210109200839.png" style="width:60%;box-shadow: 3px 3px 3px 3px #ddd;">
</p>

关于「内存与垃圾回收」章节，大体从如下三大部分展开：

- 知识预备：为后续的内容做一些知识储备，知识预备包括
  + 指针的大小
  + Tcmalloc内存分配原理
- Go内存设计与实现
- Go的垃圾回收原理

## 目录

- 指针自身的大小为什么是8字节(64位平台)？
- 内存的线性分配
- 什么是`FreeList`？
- 什么是`TCMalloc`？
  - `Page`的概念
  - `Span`的概念
  - `Object`的概念
- `TCMalloc`的基本结构
  - `PageHeap`的概念
  - `CentralFreeList`和`TransferCacheManager`的概念
  - `ThreadCache`的概念
- `TCMalloc`基本结构的依赖关系(简易)
- `TCMalloc`基本结构的依赖关系(详细)

## 64位平台下，指针自身的大小为什么是8字节？

## 本篇前言

第一部分`知识预备`的第一个知识点`指针的大小`。

> 为什么`指针的大小`会作为一个知识点呢？

因为后续内存管理的内容会涉及一些数据结构，这些数据结构使用到了指针，同时存储指针的值是需要内存空间的，所以我们需要了解指针的大小，便于我们理解一些设计的意图；其次，这也是困扰我的一个问题，因为有看见64位平台下指针底层定义的类型为`uint64`。

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

|系统总线的组成|
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

> 2^32个存储单元 == 2^32个1Byte == 2^32Byte == 4GByte == 4GB

## 做个总结哈

我们回头再来看，本次内容可以get到如下知识点：

- 存储器的基本单位是存储单元
- 存储单元为8bit
- 指针的值就是存储单元的编号
- CPU地址总线的宽度决定了指针的值的最大范围

## 内存的线性分配

线性分配大致就是需要使用多少分配多少，用到哪了标识到哪，如下图所示：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210124225714.png" style="width:100%">
</p>

线性分配有个问题，已经分配的内存被释放了，我们再如何再次分配。大家会想到用链表`LinkedList`，是的没错，但是内存管理中一般使用的是`FreeList`。

## 什么是`FreeList`？

`FreeList`本质上还是个`LinkedList`，和`LinkedList`的区别：

- `FreeList`没有`Next`属性存放下一个节点的指针的值。
- `FreeList`使用了`Value`的前8字节存放下一个节点的指针的值。
- 分配出去的节点，节点整块内存空间可以被复写(指针的值可以被覆盖掉)

用于内存的管理再合适不过了。

<p align="center">
  <img src="http://cdn.tigerb.cn/20210124224723.png" style="width:100%">
</p>

因为我们的主要目的是**掌握Go语言的内存分配原理**，但是呢，Go语言的内存分配主要是参考**Tcmalloc内存分配器**实现的，所以，我们想搞懂Go语言的内存分配原理前，必须先了解**Tcmalloc内存分配器**，便于我们对后续知识的深入理解。

## 什么是`Tcmalloc`？

讲`Tcmalloc`前我们先来了解三个概念：

- `Page`的概念
- `Span`的概念
- `Object`的概念

### `Page`的概念

操作系统是按`Page`管理内存的，本文中1Page为8KB。

```
备注：操作系统为什么按`Page`管理内存？不在本文范围。
```

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120131944.png" style="width:100%">
</p>

### `Span`的概念

一个`Span`是由N个`Page`构成的，且：

- N的范围为`1 ~ +∞`
- 构成这个`Span`的N个`Page`在内存空间上必须是连续的

如下图所示：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210124225012.png" style="width:100%">
</p>

从图中可以看出，有：

- 1个`Page`构成的8KB的`Span`
- 2个连续`Page`构成的16KB的`Span`
- 3个连续`Page`构成的24KB的`Span`

除此之外，`Span`和`Span`之间可以构成**双向链表**，内存管理中通常持有相同数量`Page`的`Span`会构成一个双向链表，如下图所示：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210125202222.png" style="width:100%">
</p>

### `Object`的概念

一个`Span`会被按照某个大小拆分成N个`Object`，同时这N个`Object`构成一个`FreeList`(如果忘了FreeList的概念可以再返回上文重新看看)。

我们以持有`1Page`的`Span`为例，`Span`、`Page`、`Object`关系图示如下：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210125201952.png" style="width:100%">
</p>

看完上面的图示，问题来了：

> 上图怎么知道拆分`Span`为一个个24字节大小的`Object`，这个规则是怎么知道的呢？

```c++
答：依赖代码维护的映射列表。

我们以Google开源的TCMalloc源码(commit:9d274df)为例来看一下这个映射列表 https://github.com/google/tcmalloc/tree/master/tcmalloc

代码位置：tcmalloc/tcmalloc/size_classes.cc
代码示例(摘取一部分)：

const SizeClassInfo SizeMap::kSizeClasses[SizeMap::kSizeClassesCount] = {
    // <bytes>, <pages>, <batch size>    <fixed>
    // Object大小列，一次申请的page数，一次移动的object数(申请或回收)
    {        0,       0,           0},  // +Inf%
    {        8,       1,          32},  // 0.59%
    {       16,       1,          32},  // 0.59%
    {       24,       1,          32},  // 0.68%
    {       32,       1,          32},  // 0.59%
    {       40,       1,          32},  // 0.98%
    {       48,       1,          32},  // 0.98%
    // ...略...
    {    98304,      12,           2},  // 0.05%
    {   114688,      14,           2},  // 0.04%
    {   131072,      16,           2},  // 0.04%
    {   147456,      18,           2},  // 0.03%
    {   163840,      20,           2},  // 0.03%
    {   180224,      22,           2},  // 0.03%
    {   204800,      25,           2},  // 0.02%
    {   229376,      28,           2},  // 0.02%
    {   262144,      32,           2},  // 0.02%
};

我们来分下下这个过程：
1. 先找到对应行(也就是说所有行都会被维护)
2. 找到第一列，这个数字就是object的大小
```

所以，N个Span会按照`SizeMap::kSizeClasses`这个列表的所有列会维护一个`SpanList`，我们用熟悉的Go语言写个伪代码如下：

```go
// Go版伪代码
type SpanList struct {
  // kSizeClassesCount 映射表的总长度，也就是映射表的总行数
  [kSizeClassesCount]*Span
}

type Span struct {
  // 这个字段对应映射表的具体那一列，把Span和SpanList的列、以及映射表进行关联
  // 通过这个数可以找到映射表对应的列规则
  // 比如 该字段值为6 则对应上面映射表的第6列：
  //  {       24,       1,          32},  // 0.68%
  // 则该Span为1Page大小
  // 则该Span会被拆成24字节的对象，一共是341个24字节Objects
  // 尾部浪费8字节
  kSizeClasses int8
}
```

再来回顾下这张图，是不是理解了。

<p align="center">
  <img src="http://cdn.tigerb.cn/20210125201952.png" style="width:100%">
</p>


`Tcmalloc`的基本结构

## 基本结构

主要由三部分构成

- `PageHeap`的概念
- `CentralFreeList`和TransferCacheManager的概念
- `ThreadCache`的概念

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