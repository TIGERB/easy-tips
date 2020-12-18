# 看了就懂，入门Go语言Map底层实现


## 前言

> 我为什么还在写博客？

把自己学习知识进行一个总结。同时把一些可能困难、复杂难以理解的东西消化吸收后，简单化输出，降低他人的学习成本，提高学习效率，主要为如下两点：

- 自我学习的总结过程
- 简化他人学习成本

> 为什么博客更新的这么慢？

- 学习的难度在不断的增加，产出越来越慢
- 比以前懒了？

今天要分享的是主要内容是`Go语言Map底层实现`，目的让大家快速了解`Go语言Map`底层大致的实现原理。读完本篇文章你可以获得收益、以及我所期望你能获取的收益：

收益序号|收益描述|掌握程度
--------------|--------------|--------------
收益1|**大致**对Go语言Map底层实现有一个了解|必须掌握
收益2|**大致知道**Go语言Map是如何读取数据的|必须掌握
收益3|**熟悉**Go语言Map底层核心结构体`hmap`|可选
收益4|**熟悉**Go语言Map底层核心结构体`bmap`|可选
收益5|**熟悉**Go语言Map底层里的溢出桶|可选
收益6|**熟悉**Go语言Map是如何读取数据的|可选

收益1和收益2是看了本篇文章希望大家**必须掌握**的知识点，其他的为可选项，如果你对此感兴趣或者已经掌握了收益1、2可以继续阅读此处的内容。

对于本篇文章的结构主要按如下顺序开展：

- 先看看一般Map的实现思路
- Go语言里Map的实现思路(入门程度：包含收益1、2)
- Go语言里Map的实现思路(熟悉程度：包含收益3、4、5、6)

其次，本篇文章主要以Map的读来展开分析，因为读弄明白了，其他的写、更新、删除等基本操作基本都可以猜出来了，不是么😏。

## 先看看一般Map的实现思路

直入主题，一般的Map会包含两个主要结构：

- 数组：数组里的值指向一个链表
- 链表：目的解决hash冲突的问题，并存放键值

大致结构如下：

<p align="center">
  <img src="http://cdn.tigerb.cn/20201216161128.png" style="width:100%">
</p>

读取一个key值的过程大致如下：

```
+------------------------------------+
|      key通过hash函数得到key的hash     |
+------------------+-----------------+
                   |
                   v
+------------------------------------+
|       key的hash通过取模或者位操作     |
|          得到key在数组上的索引        |
+------------------------------------+
                   |
                   v
+------------------------------------+
|         通过索引找到对应的链表         |
+------------------+-----------------+
                   |
                   v
+------------------------------------+
|       遍历链表对比key和目标key        |
+------------------+-----------------+
                   |
                   v
+------------------------------------+
|              相等则返回value         |
+------------------+-----------------+
                   |
                   v                
                 value 

```

接着我们来简单看看Go语言里Map的实现思路。

## Go语言里Map的实现思路(入门程度：包含收益1、2)

Go语言解决hash冲突不是链表，实际**主要**用的数组(内存上的连续空间)，如下图所示：

```
备注：后面我们会解释上面为啥用的“主要”两个字。
```

<p align="center">
  <img src="http://cdn.tigerb.cn/20201217210507.png" style="width:100%">
</p>

对应的是两个核心的结构体`hmap`和`bmap`.

转换一下，其实就是这样的一个大致关系，如下图所示：

<p align="center">
  <img src="http://cdn.tigerb.cn/20201217210752.png" style="width:100%">
</p>

我们通过一次`读操作`为例，看看读取某个key的值的一个**大致过程**：

步骤编号|描述
------|------
①|通过hash函数获取目标key的**哈希**，哈希和数组的长度通过位操作获取数组的**索引值**(备注：获取索引值的方式一般有取模或位操作，位操作的性能好些)
②|遍历bmap里的键，和目标key对比获取**key的索引值**
③|通过**key的索引值**获取偏移量，获取到对应的key的value

图示如下：

<p align="center">
  <img src="http://cdn.tigerb.cn/20201217210816.png" style="width:100%">
</p>

所以到这里，你已经入门了`Go语言Map底层实现`：

- **大致**对Go语言Map底层实现有一个了解
- **大致知道**Go语言Map是如何读取数据的

然而实际情况不止如此，我们再稍微深入的探索下，有兴趣的可以继续往下看，没兴趣可以不用继续往下看了(开玩笑=^_^=)，反正已经达到目的了，哈哈😏。

## Go语言里Map的实现思路(熟悉程度：包含收益3、4、5、6)

想要深入学习，首先得了解下底层Map中的两个核心结构体`hmap`和`bmap`。

### 核心结构体`hmap`

字段`buckets`

<p align="center">
  <img src="http://cdn.tigerb.cn/20201216202022.png" style="width:60%">
</p>

bmap

<p align="center">
  <img src="http://cdn.tigerb.cn/20201216202114.png" style="width:60%">
</p>

### 核心结构体`bmap`

<p align="center">
  <img src="http://cdn.tigerb.cn/20201216202224.png" style="width:60%">
</p>

### 串起来

<p align="center">
  <img src="http://cdn.tigerb.cn/20201216202349.png" style="width:90%">
</p>


> bmap最多只存储8对键值对，存满了怎么办？

<p align="center">
  <img src="http://cdn.tigerb.cn/20201216202608.png" style="width:80%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20201217165310.png" style="width:90%">
</p>

## 以一次读为例

<p align="center">
  <img src="http://cdn.tigerb.cn/20201217165551.png" style="width:90%">
</p>

```go
func mapaccess1(t *maptype, h *hmap, key unsafe.Pointer) unsafe.Pointer {
    // ...略
    
    // 通过hash函数获取当前key的哈希
	hash := alg.hash(key, uintptr(h.hash0))
    m := bucketMask(h.B)
    // 通过当前key的哈希获取到对应的bmap结构的b
    // 这里的b 我们称之为“正常桶的bmap”
    // “正常桶的bmap”可能会对应到溢出桶的bmap结构，我们称之为“溢出桶的bmap”
    b := (*bmap)(add(h.buckets, (hash&m)*uintptr(t.bucketsize)))
    
    // ...略
    
    // 获取当前key的哈希的高8位
	top := tophash(hash)
bucketloop:
    // 下面的for循环是个简写，完整如下。
    // for b = b; b != nil; b = b.overflow(t) {
    // 可以知道b的初始值为上面的“正常桶的bmap”，则：
    // 第一次遍历：遍历的是“正常桶的bmap”
    // 如果正常桶没找到，则
    // 继续遍历：如果当前“正常桶的bmap”中的overflow值不为nil(说明“正常桶的bmap”关联了“溢出桶的bmap”)，则遍历当前指向的“溢出桶的bmap”
	for ; b != nil; b = b.overflow(t) {
        // 由于b的初始值为“正常桶的bmap”，第一次先遍历“正常桶的bmap”
		for i := uintptr(0); i < bucketCnt; i++ {
            // 对比key哈希的高8位
            // 对比哈希的高8位目的是为了加速
			if b.tophash[i] != top {
                // emptyRest 标志位：表示当前位置已经是末尾了；删除操作会设置此标志位
				if b.tophash[i] == emptyRest {
					break bucketloop
				}
				continue
            }
            // 找到了相同的hash高8位，则：找到对应索引位置i的key
			k := add(unsafe.Pointer(b), dataOffset+i*uintptr(t.keysize))
			if t.indirectkey() {
				k = *((*unsafe.Pointer)(k))
            }
            // 对比key是不是一致
			if alg.equal(key, k) {
                // key是一致，则：获取对应索引位置的值
				e := add(unsafe.Pointer(b), dataOffset+bucketCnt*uintptr(t.keysize)+i*uintptr(t.elemsize))
				if t.indirectelem() {
					e = *((*unsafe.Pointer)(e))
                }
                // 返回找到的结果
				return e
			}
		}
    }
    // 正常桶、溢出桶都没找到则返回 “空值”
	return unsafe.Pointer(&zeroVal[0])
}
```


> hmap是如何扩容的？

<p align="center">
  <img src="http://cdn.tigerb.cn/20201216202703.png" style="width:60%">
</p>



```
参考：
1.《Go语言设计与实现》https://draveness.me/golang/docs/part2-foundation/ch03-datastructure/golang-hashmap/
2. Go源码版本1.13.8 https://github.com/golang/go/tree/go1.13.8/src
```