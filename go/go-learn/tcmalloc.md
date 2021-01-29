# 

> 源码版本(commit:9d274df) https://github.com/google/tcmalloc/tree/master/tcmalloc

# 导读

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
- 读前知识储备
	+ 内存的线性分配
	+ 什么是`FreeList`？
	+ 虚拟内存
- `TCMalloc`中的五个基本概念
	+ `Page`的概念
	+ `Span`的概念
    	* `SpanList`的概念
	+ `Object`的概念
    	* `SizeClass`的概念
- 解密`Tcmalloc`的基本结构
- `PageHeap`、`CentralFreeList`、`ThreadCache`的详细构成
	+ 解密`PageHeap`
	+ 解密`CentralFreeList`和`TransferCacheManager`的构成
    	* 解密`CentralFreeList`
		* 解密`TransferCacheManager`
	+ 解密`ThreadCache`
- 解密`TCMalloc`基本结构的依赖关系
	+ 简易版
	+ 详细版

# 64位平台下，指针自身的大小为什么是8字节？

## 本篇导读

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

# 18张图解密TCMalloc内存分配原理

## 本文导读

<!-- 先，了解三个读前的基本知识储备，目的辅助我们更好的理解内存分配原理：

- 内存的线性分配
- 什么是`FreeList`？
- 虚拟内存

接着，了解`TCMalloc`中的五个基本概念，目的`TCMalloc`各个主要部分是基于这些基本概念组成的：

- `Page`的概念
- `Span`的概念
	+ `SpanList`的概念
- `Object`的概念
	+ `SizeClass`的概念

再接着，在掌握了上面基础概念的基础上，简单看看`TCMalloc`的基本结构：

- `PageHeap`的概念
- `CentralFreeList`和`TransferCacheManager`的概念
- `ThreadCache`的概念

最后，在掌握如上基础知识的概念上看看，`TCMalloc`主要三部分的结构关系，以及内存分配的大致过程：
  
- `TCMalloc`基本结构的依赖关系(简易)
- `TCMalloc`基本结构的依赖关系(详细)

## 目录 -->

- 读前知识储备
	+ 内存的线性分配
	+ 什么是`FreeList`？
	+ 虚拟内存
	+ 什么是`TCMalloc`?
- `TCMalloc`中的五个基本概念
	+ `Page`的概念
	+ `Span`的概念
    	* `SpanList`的概念
	+ `Object`的概念
    	* `SizeClass`的概念
- 解密`Tcmalloc`的基本结构
- `PageHeap`、`CentralFreeList`、`ThreadCache`的详细构成
	+ 解密`PageHeap`
	+ 解密`CentralFreeList`和`TransferCacheManager`的构成
    	* 解密`CentralFreeList`
		* 解密`TransferCacheManager`
	+ 解密`ThreadCache`
- 解密`TCMalloc`基本结构的依赖关系
	+ 简易版
	+ 详细版

## 读前知识储备

本小节的结构如下：

- 内存的线性分配
- 什么是`FreeList`？
- 虚拟内存
- 什么是`TCMalloc`?

> 目的：辅助我们更好的理解内存分配原理。

### 内存的线性分配

线性分配大致就是需要使用多少分配多少，“用到哪了标识到哪”，如下图所示：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210124225714.png" style="width:100%">
</p>

线性分配有个问题：“已经分配的内存被释放了，我们如何再次分配？”。大家会想到用链表`LinkedList`，是的没错，但是内存管理中一般使用的是`FreeList`。

### 什么是`FreeList`？

`FreeList`本质上还是个`LinkedList`，和`LinkedList`的区别：

- `FreeList`没有`Next`属性，所以不是用`Next`属性存放下一个节点的指针的值。
- `FreeList`“相当于使用了`Value`的前8字节”(其实就是整块内存的前8字节)存放下一个节点的指针。
- 分配出去的节点，节点整块内存空间可以被复写(指针的值可以被覆盖掉)

如下图所示：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210124224723.png" style="width:100%">
</p>

因为我们的主要目的是**掌握Go语言的内存分配原理**，但是呢，Go语言的内存分配主要是参考**Tcmalloc内存分配器**实现的，所以，我们想搞懂Go语言的内存分配原理前，必须先了解**Tcmalloc内存分配器**，便于我们对后续知识的深入理解。

### 虚拟内存

这里直说结论哈，我们的进程是运行在虚拟内存上的，图示如下：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210129194928.png" style="width:90%">
</p>

- 对于我们的进程而言，可使用的内存是连续的
- 安全，防止了进程直接对物理内存的操作(如果进程可以直接操作物理内存，那么存在某个进程篡改其他进程数据的可能)
- 虚拟内存和物理内存是通过MMU(Memory Manage Unit)映射的(感兴趣的可以研究下)
- 等等(感兴趣的可以研究下)

所以，以下文章我们所说的内存都是指**虚拟内存**。

### 什么是`TCMalloc`？

`TCMalloc`全称`Thread Cache Alloc`，是Google开源的一个内存分配器，基于数据结构`FreeList`实现，并引入了线程级别的缓存，性能更加优异。

## `TCMalloc`中的五个基本概念

本小节的结构如下：

- `Page`的概念
- `Span`的概念
	+ `SpanList`的概念
- `Object`的概念
	+ `SizeClass`的概念

> 目的：`TCMalloc`各个主要部分是基于这些基本概念组成的.

### `Page`的概念

操作系统是按`Page`管理内存的，本文中1Page为8KB，如下图所示：

```
备注：操作系统为什么按`Page`管理内存？不在本文范围。
```

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120131944.png" style="width:100%">
</p>

### `Span`和`SpanList`的概念

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

除此之外，`Span`和`Span`之间可以构成**双向链表**我们称之为`SpanList`，内存管理中通常将持有相同数量`Page`的`Span`构成一个双向链表，如下图所示(**N个持有1Page的`Span`构成的`SpanList`**)：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210128131031.png" style="width:100%">
</p>

```c++
class Span : public SpanList::Elem {
 public:

  ...

  // 把span拆解成object的方法
  void BuildFreelist(size_t size, size_t count);

  
  ...

  union {
    // 
    ObjIdx cache_[kCacheSize];
    
    ...
  };

  PageId first_page_;  // 当前span是从哪个page开始的
  Length num_pages_;   // 当前page持有的page数量

  ...
};

```

### `Object`和`SizeClass`的概念

一个`Span`会被按照某个大小拆分为N个`Objects`，同时这N个`Objects`构成一个`FreeList`(如果忘了FreeList的概念可以再返回上文重新看看)。

我们以持有`1Page`的`Span`为例，`Span`、`Page`、`Object`关系图示如下：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210125201952.png" style="width:100%">
</p>

看完上面的图示，问题来了：

> 上图怎么知道拆分`Span`为一个个24字节大小的`Object`，这个规则是怎么知道的呢？

```c++
答案：依赖代码维护的映射列表。

我们以Google开源的TCMalloc源码(commit:9d274df)为例来看一下这个映射列表 https://github.com/google/tcmalloc/tree/master/tcmalloc

代码位置：tcmalloc/tcmalloc/size_classes.cc
代码示例(摘取一部分)：

const SizeClassInfo SizeMap::kSizeClasses[SizeMap::kSizeClassesCount] = {
    // 这里的每一行 称之为SizeClass
    // <bytes>, <pages>, <batch size>    <fixed>
    // Object大小列，一次申请的page数，一次移动的objects数(内存申请或回收)
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

获取拆分规则的过程(先找到行、再找到这行第一列的值)：
1. 先找到对应行(如何找到这个行？是不是有人有疑惑了，
想知道这个答案就需要了解`CentralFreeList`这个结构了，
下文我们会讲到。)
2. 找到第一列，这个数字就是object的大小
```

同时通过上面我们知道了：`SizeMap::kSizeClasses`的每一行元素我们称之为**SizeClass**(下文中我们直接就称之为`SizeClass`).

<!-- 规则(SizeMap)有`SizeMap::kSizeClassesCount`个规则，所以所有的Span会按照`SizeMap::kSizeClasses`这个列表的每一列规则维护一个`SpanList`，用熟悉的Go语言写个伪代码如下：

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
</p> -->

> 这个5个基本概念具体干什么用的呢？

```
答案：支撑了`Tcmalloc`的基本结构的实现。
```

## 解密`Tcmalloc`的基本结构

`Tcmalloc`主要由三部分构成：

- `PageHeap`
- `CentralFreeList`
- `ThreadCache`

图示如下：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132020.png" style="width:60%">
</p>

但是呢，实际上`CentralFreeList`是被`TransferCacheManager`管理的，所以`Tcmalloc`的基本结构实际应该为下图所示：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132031.png" style="width:80%">
</p>

> 接着，`ThreadCache`其实被线程持有，为什么呢？

```
答案：减少线程之间的竞争，分配内存时减少锁的过程。
这也是为什么叫`Thread Cache Alloc`的原因。
```

进一步得到简易的结构图：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132037.png" style="width:80%">
</p>

## 解密`PageHeap`、`CentralFreeList`、`ThreadCache`的详细构成

本小节的结构如下：

+ 解密`PageHeap`
+ 解密`CentralFreeList`和`TransferCacheManager`的构成
	* 解密`CentralFreeList`
	* 解密`TransferCacheManager`
+ 解密`ThreadCache`

> 目的：详细了解`TCMalloc`各个组成部分的实现。

### 解密`PageHeap`

`PageHeap`主要负责管理不同规格的`Span`，相同规格的`Span`构成`SpanList`(可回顾上文`SpanList`的概念)。

> 什么是相同规格的`Span`？

```
答：持有相同`Page`数目的`Span`。
```

`PageHeap`对象里维护了一个属性`free_`类型是个数组，**粗略看**数组元素的类型是`SpanList`，同时`free_`这个数据的元素具有以下特性：

- 索引值为1对应的`SpanList`，该`SpanList`的`Span`都持有1Pages；
- 索引值为2对应的`SpanList`，该`SpanList`的`Span`都持有2Pages；
- 以此类推，`free_`索引值为MaxNumber对应的`SpanList`，该`SpanList`的`Span`都持有MaxNumber Pages；
- MaxNumber的值由`kMaxPages`决定


数组索引|SpanList里单个Span持有Page数
---|---
1|1Pages
2|2Pages
3|3Pages
4|4Pages
5|5Pages
...|...
kMaxPages|kMaxPages Pages

图示如下：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210129133136.png" style="width:100%">
</p>

但是呢，实际上从代码可知：数组元素的实际类型为`SpanListPair`，代码如下

```c++
class PageHeap final : public PageAllocatorInterface {
 public:

  // ...略

 private:
  // 持有两个Span构成的双向链表
  struct SpanListPair {
    // Span构成的双向链表 正常的
    SpanList normal; 
    // Span构成的双向链表 大概是 物理内存已经回收 但是虚拟内存还被持有(感兴趣可以研究)
    SpanList returned;
  };

  // ...略

  // kMaxPages.raw_num()这么多个，由上面SpanListPair类型构成的数组
  SpanListPair free_[kMaxPages.raw_num()] ABSL_GUARDED_BY(pageheap_lock);

  // ...略
};

```

结论：

- `free_`数组元素的类型是`SpanListPair`
- `SpanListPair`里维护了两个`SpanList`

根据这个结论我们修正下`PageHeap`结构图，如下：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210129132903.png" style="width:100%">
</p>

又因为大于kMaxPages个Pages(大对象)的内存分配是从`large_`中分配的，代码如下：

```c++
class PageHeap final : public PageAllocatorInterface {
 public:

  // ...略

  //  大对象的内存从这里分配(length >= kMaxPages)
  SpanListPair large_ ABSL_GUARDED_BY(pageheap_lock);

  // ...略
};

```

所以我们再加上大对象的分配时的`large_`属性，得到`PageHeap`的结构图如下：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210129132923.png" style="width:100%">
</p>

同时`PageHeap`核心的代码片段如下：

```c++
class PageHeap final : public PageAllocatorInterface {
 public:

  // ...略

 private:
  // 持有两个Span构成的双向链表
  struct SpanListPair {
    // Span构成的双向链表
    SpanList normal; 
    // Span构成的双向链表
    SpanList returned;
  };

  //  大对象的内存从这里分配(length >= kMaxPages)
  SpanListPair large_ ABSL_GUARDED_BY(pageheap_lock);

  // kMaxPages.raw_num()这么多个，由上面SpanListPair类型构成的数组
  SpanListPair free_[kMaxPages.raw_num()] ABSL_GUARDED_BY(pageheap_lock);

  // ...略
};

```

### 解密`CentralFreeList`和`TransferCacheManager`的构成
#### 解密`CentralFreeList`

我们可以称之为中央缓存，中央缓存被线程共享，从中央缓存`CentralFreeList`获取缓存需要加锁。

把`Span`按照`SizeClass`里面的规则拆解成`Object`，同时`Object`构成`FreeList`

`CentralFreeList`里面有个属性`size_class_`，就是`SizeClass`的值，来自于映射表`SizeMap`这个数组的索引值

每个`SizeClass`对应一个`CentralFreeList`

有N个`CentralFreeList`，N的值为`kNumClasses`

```c++
class CentralFreeList {

  // ...略

 private:

  // 锁
  // 线程从此处获取内存 需要加锁 保证并发安全
  absl::base_internal::SpinLock lock_;

  // 对应上文提到的映射表SizeClassInfo中的某个索引值
  // 目的找到Span拆解为object时，object的大小等规则
  size_t size_class_;  
  // object的总数量
  size_t object_size_;
  // 一个Span持有的object的数量
  size_t objects_per_span_;
  // 一个Span持有的page的数量
  Length pages_per_span_;

  // ...略
```

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132206.png" style="width:100%">
</p>

#### 解密`TransferCacheManager`

有`kNumClasses`个`CentralFreeList`，这些`CentralFreeList`在哪维护的呢？
就是`TransferCacheManager`这个结构里的`freelist_`属性，代码如下：

```c++
class TransferCacheManager {
  
  // ...略

 private:
  // freelist_是个数组
  // 元素的类型是上面的CentralFreeList
  // 元素的数量与 映射表 SizeClassInfo对应
  CentralFreeList freelist_[kNumClasses];
} ABSL_CACHELINE_ALIGNED;
```

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132218.png" style="width:100%">
</p>


### 解密`ThreadCache`的构成

我们可以称之为线程缓存，`TCMalloc`内存分配器的核心所在。`ThreadCache`被每个线程持有，分配内存时不用加锁，性能好。

`ThreadCache`对象里维护了一个属性`list_`类型是个数组，数组元素的类型是`FreeList`，代码如下：

```c++
class ThreadCache {
  // ...略

  // list_是个数组
  // 元素的类型是FreeList
  // 元素的数量与 映射表 SizeClassInfo对应
  FreeList list_[kNumClasses]; 

  // ...略
};
```

同时`FreeList`里的元素还具有以下特性：

- 索引值为1对应的`FreeList`，该`FreeList`的`Object`大小为8 Bytes；
- 索引值为2对应的`FreeList`，该`FreeList`的`Object`大小为16 Bytes；
- 以此类推，`free_`索引值为MaxNumber对应的`FreeList`，该`FreeList`的`Object`大小为MaxNumber Bytes；
- MaxNumber的值由`kNumClasses`决定

这个规则怎么来的？还是取决于映射列表，同样以Google开源的TCMalloc源码(commit:9d274df)为例，来看一下这个映射列表：

```c++
https://github.com/google/tcmalloc/tree/master/tcmalloc

代码位置：tcmalloc/tcmalloc/size_classes.cc
代码示例(摘取一部分)：

const SizeClassInfo SizeMap::kSizeClasses[SizeMap::kSizeClassesCount] = {
    // 这里的每一行 称之为SizeClass
    // <bytes>, <pages>, <batch size>    <fixed>
    // Object大小列，一次申请的page数，一次移动的objects数(内存申请或回收)
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
```
我们可以得到：

数组索引|FreeList里单个Object的大小
---|---
1|8 Bytes
2|16 Bytes
3|24 Bytes
4|32 Bytes
5|40 Bytes
...|...
kNumClasses|kNumClasses Bytes

得到`ThreadCache`结构图如下所示：

```
注意：图示中索引为3的FreeList的Span尾部会浪费掉8字节。
```

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132229.png" style="width:100%">
</p>

## 解密`Tcmalloc`基本结构的依赖关系

本小节的结构如下：

- 简易版
- 详细版

> 目的：了解`Tcmalloc`内存分配的大致过程。

### 简易版

当给小对象分配内存时，先`ThreadCache`的内存不足时，从对应`SizeClass`的`CentralFreeList`获取，再`CentralFreeList`，最后，从对应`SizeClass`的`PageHeap`。

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132244.png" style="width:66%">
</p>

## 解密`Tcmalloc`基本结构的依赖关系(详细)

以`SizeClass`的值为1为例，看一下详细内存分配过程。

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