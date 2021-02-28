# 为什么说Go的Map是无序的？

> Go源码版本1.13.8

# 前言

是的，我也是一个PHPer，对于我们PHPer转Gopher的银😁，一定有个困扰：**Go语言里每次遍历Map输出元素的顺序并不一致，但是在PHP里却是稳定的**。今天我们就来看看这个现象的原因。本篇文章主要从如下节点展开：

- Go的Map遍历结果“无序”
    + 遍历Map的索引的起点是随机的
- Go的Map本质上是“无序的”
    + 无序写入
        * 正常写入(非哈希冲突写入)
        * 哈希冲突写入
    + 扩容
        * 成倍扩容迫使元素顺序变化
        * 等量扩容

# Go的Map遍历结果“无序”

> 现象：Go语言里每次遍历Map输出元素的顺序并不一致，但是在PHP里却是稳定的。

关于这个现象我就不过多赘述了，同时我相信大家应该都网上搜过相关的文章，这些文章大多都说明了原因：**For ... Range ... 遍历Map的索引的起点是随机的**，没错，就是下面这段代码。

```go
// versions/1.13.8/src/cmd/compile/internal/gc/range.go
func walkrange(n *Node) *Node {
	
    // 略...

    // 遍历Map时
	case TMAP:
		
        // 略...

        // 调用mapiterinit mapiterinit函数见下方
		fn := syslook("mapiterinit")

		// 略...

		fn = syslook("mapiternext")
		
        // 略...
}

// versions/1.13.8/src/runtime/map.go
func mapiterinit(t *maptype, h *hmap, it *hiter) {

    // 略...

    // 对，就是这行代码
    // 随机一个索引值，作为遍历开始的地方
	// decide where to start
	r := uintptr(fastrand())
	if h.B > 31-bucketCntBits {
		r += uintptr(fastrand()) << 31
	}
	
    // 略...

	mapiternext(it)
}
```

但是呢，有没有再推测过Go的作者们这么做背后的真正原因是什么？个人觉着因为：

# Go的Map本质上是“无序的”

> Go的Map本质上是“无序的”，为什么这么说？

## “无序”写入

### 1. 正常写入(非哈希冲突写入)：是hash到某一个bucket上，而不是按buckets顺序写入。

虽然buckets是一块连续的内存，但是新写入的键值可能写到这个bucket：
<p align="center">
  <img src="http://cdn.tigerb.cn/20210220201909.png" style="width:55%">
</p>

也可能写到这个bucket：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210220201917.png" style="width:55%">
</p>

### 2. 哈希冲突写入：如果存在hash冲突，会写到同一个bucket上。

可能写到这个位置：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210221180849.png" style="width:50%">
</p>

极限情况，也可能写到这个位置：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210221181012.png" style="width:50%">
</p>

更有可能写到溢出桶去：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210220203705.png" style="width:36%">
</p>

所以，写数据时，**并没有单独维护键值对的顺序**。而PHP(version 5)语言通过一个全局链表维护了Map里元素的顺序。

## 扩容

Go的Map的扩容有两种：

- 成倍扩容
- 等量扩容

### 1. 成倍扩容迫使元素顺序变化

为了简化理解我们对「成倍扩容」的理解，我们假设如下条件：

- 有如下`map`
- 且该`map`当前有2个`bucket`(也就是2个`bmap结构`)
- 键hash的过程这里简单用取模(便于理解)

```go
// 以此map为例
map[int]int{
    1:  1,
    2:  2,
    3:  3,
    4:  4,
    5:  5,
    6:  6,
    7:  7,
    8:  8,
    9:  9,
    10: 10,
    11: 11,
    12: 12,
    13: 13,
}
```

同时根据如上的假设，我们得到此map对应的结构图示如下：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210223162655.png" style="width:90%">
</p>

> 什么时候触发**成倍**扩容？

- map写操作时
- (元素数量/bucket数量) > 6.5时

通过下面的代码分析可知：

```go
// versions/1.13.8/src/runtime/map.go
// map写操作
func mapassign(t *maptype, h *hmap, key unsafe.Pointer) unsafe.Pointer {
	
    // 略...

    // 是否触发扩容校验
	if !h.growing() && (overLoadFactor(h.count+1, h.B) || tooManyOverflowBuckets(h.noverflow, h.B)) {
        // 扩容
		hashGrow(t, h)
		goto again
	}

    // 略...

}

// 触发扩容的装载因子临界值 = loadFactorNum/loadFactDen = 13/2 = 6.5
loadFactorNum = 13
loadFactorDen = 2

// 超过装载因子校验
func overLoadFactor(count int, B uint8) bool {
    // 装换公式 uintptr(count) > loadFactorNum*(bucketShift(B)/loadFactorDen)
    // 得到 uintptr(count)/bucketShift(B) > loadFactorNum/loadFactorDen
    // 又有 loadFactorNum/loadFactDen = 13/2 = 6.5
    // 可得 uintptr(count)/bucketShift(B) > 6.5 时触发成倍扩容
	return count > bucketCnt && uintptr(count) > loadFactorNum*(bucketShift(B)/loadFactorDen)
}
```

上述Map，当写入键值`14:14`时，我们分析是否会触发成倍扩容：

```
可知当前元素数量count：13
bucket(正常桶bmap)的数量bucketShift(B)：2

(13+1)/2 = 7 > 6.5 

所以，会触发成倍扩容。
```

成倍扩容的过程如下：

- 原`buckets`被指向`oldbuckets`
- 从初始化成倍新的`buckets`指向`buckets`
- 写操作触发扩容 
- 每次只扩容当前的键对应的`bucket`(`bmap`)
- 原`bucket`(`bmap`)被分流到两个新的`bucket`(`bmap`)中

过程如下图所示(标红部分为本次扩容的bucket)：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210223173949.png" style="width:100%">
</p>

之后随着键值`15:15`被写入，完成扩容过程，扩容后的图示如下：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210223162925.png" style="width:100%">
</p>

同时，通过上面的分析我们可以得到：**成倍扩容迫使元素顺序变化**。

### 2. 等量扩容

> 什么时候触发**等量**扩容？

答案见下面的代码：

```go
// 等量扩容判断
func tooManyOverflowBuckets(noverflow uint16, B uint8) bool {
	// 复习下B的含义：count(buckets) = 2^B
	if B > 15 {
		B = 15
	}
	
    // 溢出桶的数量大于等于 2*B时 触发等量扩容
	return noverflow >= uint16(1)<<(B&15)
}
```

> 等量扩容的目的？

```
答：整理溢出桶，回收冗余的溢出桶。
```

同样，为了简化理解我们对「等量扩容」的理解，我们假设如下条件：

- 有如下`map`
- 且该`map`当前有2个`bucket`(也就是2个`bmap结构`)
- 键hash的过程这里简单用取模(便于理解)
- 忽略索引为1的的`bucket`(也就是`buckets`的第2个`bmap`)
- 以索引为0的`bucket`(也就是`buckets`的第1个`bmap`)里的键值为例
- 假设第一个`bmap`已经被写满(hash冲突所致)，且与之关联的溢出桶里的`bmap`也被写满，且与此溢出桶里的`bmap`关联的另一个溢出桶里的`bmap`写入了一个键值

```go
// 以此map为例
map[int]int{
    1:  1,
    2:  2,
    3:  3,
    4:  4,
    5:  5,
    6:  6,
    // ...略 连续值
    34: 34,
}
```

同时根据如上的假设，我们得到此map对应的结构图示如下：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210223190741.png" style="width:90%">
</p>

为了说明「等量扩容」的作用，我们继续假设：

- 删掉键值`8:8`
- 删掉键值`20:20`
- 删掉键值`30:30`

此时，得到此map对应的结构图示如下：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210223191015.png" style="width:90%">
</p>

> 基于上面的假设，我们写入键值`36:36`时是否会触发「等量扩容」？

```
答：
条件1. 否会触发「等量扩容」的公式：noverflow >= uint16(1)<<(B&15)
条件2. 上文我们已经假设：忽略索引为1的的`bucket`(也就是`buckets`的第2个`bmap`)，仅以索引为0的`bucket`(也就是`buckets`的第1个`bmap`)里的键值为例

可得：
noverflow = 2
B = 1

我们套入这个公式：

2 >= 1 << (0001 & 1111)
2 >= 1 << 0001
2 >= 0010
2 >= 2

得到结果：true
```

结论：写入键值`36:36`时会触发「等量扩容」，等量扩容扩容后的结果如下图所示：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210223191105.png" style="width:90%">
</p>

从上图可以看出：

- 整理了正常桶`bmap`的内存
- 整理了正常桶`bmap`对应所有溢出桶`bmap`的内存
- 上述整理内存过程之后，上图示中绿色的溢出桶会被GC垃圾回收

同时，通过上面的分析我们可以得到：**等量扩容并没有改变元素顺序**。

# 结语

通过上文的分析，我们可知Go的Map的特性：

- 无序写入
- 成倍扩容迫使元素顺序变化

所以可以说「Go的Map是无序的」。

其次，通过本文我们：

- 再次回顾了Go的Map遍历结果“无序”的原因
- 了解了Map的写入过程
- 了解了Map的「成倍扩容」和「等量扩容」的设计与目的
