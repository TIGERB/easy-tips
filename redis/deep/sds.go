package main

import "fmt"

// SDS 简单动态字符串
type SDS struct {
	// 当前字符串的长度
	len int64
	// 剩余的free
	free int64
	// 字符串的值
	buf []byte
}

// Len Len
func (s *SDS) Len() int64 {
	return s.len
}

func main() {
	demo := []byte("ab")
	fmt.Println("demo", demo)
}
