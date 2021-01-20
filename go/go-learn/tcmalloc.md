# 

> 源码版本(commit:9d274df) https://github.com/google/tcmalloc/tree/master/tcmalloc

## 导读

## 64位平台下，一个指针的大小为什么是8字节？

## Freelist

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120131804.png" style="width:100%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120131820.png" style="width:100%">
</p>

## Page

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120131944.png" style="width:100%">
</p>

## Span

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120131951.png" style="width:100%">
</p>

## Object

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132002.png" style="width:100%">
</p>

## 整体结构

三层构成

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132020.png" style="width:60%">
</p>

`CentralFreeList`被`TransferCacheManager`管理

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132031.png" style="width:80%">
</p>

`ThreadCache`被线程持有

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132037.png" style="width:80%">
</p>

## PageHeap

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132117.png" style="width:100%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132136.png" style="width:100%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132145.png" style="width:100%">
</p>

## CentralFreeList

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132206.png" style="width:100%">
</p>

## TransferCacheManager

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132218.png" style="width:100%">
</p>

## ThreadCache

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132229.png" style="width:100%">
</p>

## 三层的简易关系

<p align="center">
  <img src="http://cdn.tigerb.cn/20210120132244.png" style="width:66%">
</p>

## 三层的复杂关系

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