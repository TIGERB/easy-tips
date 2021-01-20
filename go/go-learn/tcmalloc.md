# 

> 源码版本(commit:9d274df) https://github.com/google/tcmalloc/tree/master/tcmalloc

## 导读

```
本文基于64位平台
```

- 一个指针的大小为什么是8字节(64位平台)？
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

开始本篇文章前，我们先来看个问题：
> 64位平台下，一个指针的大小为什么是8字节？


## 64位平台下，一个指针的大小为什么是8字节？

存储指针是占有内存空间的，我们需要了解指针的大小，为后面了解内存分配打好基础。

寻址空间

总线决定的，总线又分为：
- 内部总线
- 外部总线
- 系统总线
  + 地址总线
  + 数据总线
  + 信号总线

`地址总线`的宽度决定了可寻址空间的范围，地址总线里的“每一根线”传输`0`或`1`，64位CPU的地址总线宽度是64，有2^64中组合，可代表的数字范围为`0 ~ 2^64-1`。

结论：64位CPU地址总线可传输的10进制数范围为`0 ~ 2^64-1`。

储存器 存储单元 8bit 1Byte 1字节

给每个存储单元编号，这个编号就是内存的地址，这个编号就是指针的值。

上面64位CPU的地址总线可寻址范围 为 `0 ~ 2^64-1`，需要一个类型可以存储这个指针的值，毫无疑问就是`uint64`，`uint64`又是多大呢？是不是8byte。所以：**64位平台下，一个指针的大小为什么是8字节**。

顺便扩充个问题：

> 为什么32位平台下，可寻址空间最大是4GB？

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