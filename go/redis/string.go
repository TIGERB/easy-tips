package main

// 学习&参考资料，感谢：
// [Redis 设计与实现](http://redisbook.com/preview/sds/implementation.html)
// [Go 语言设计与实现 (3.4 字符串)](https://draveness.me/golang/docs/part2-foundation/ch03-datastructure/golang-string/)
// [Go语言实战笔记（二十七）| Go unsafe Pointer](https://www.flysnow.org/2017/07/06/go-in-action-unsafe-pointer.html)
// [golang 标准库及第三方库文档中文翻译](https://www.bookstack.cn/read/gctt-godoc/Translate-builtin-builtin.md)
// [字符串横向对比：C、Golang、Redis](https://blog.cyeam.com/golang/2015/09/15/string)
// [内存分配中的堆、栈、静态区、只读区](https://www.cnblogs.com/fuleying/p/4454869.html)

// // SDS 简单动态字符串 原C实现结构
// type SDS struct {
// 	len  uint
// 	free int
// 	buf  []byte
// }

// SDS 简单动态字符串 Go实现结构
type SDS string
