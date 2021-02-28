# 为什么Go的Map读不到key时没有Panic?

```go
func mapaccess1(t *maptype, h *hmap, key unsafe.Pointer) unsafe.Pointer {
	
    // ...略...

    // 当前map不存在 或者 元素数量为0时
	if h == nil || h.count == 0 {
		if t.hashMightPanic() {
			t.key.alg.hash(key, 0) // see issue 23734
		}

        // 返回了一个全局数组的第一个元素
        // const maxZero = 1024 
        // var zeroVal [maxZero]byte
		return unsafe.Pointer(&zeroVal[0])
	}

    //  当前map是写入的状态时 panic
	if h.flags&hashWriting != 0 {
		throw("concurrent map read and map write")
	}

    // ...略...
    // 找key的代码 略

    // 没找到该key
    // 返回了一个全局数组的第一个元素
    // const maxZero = 1024 
    // var zeroVal [maxZero]byte
	unsafe.Pointer(&zeroVal[0])
}
```