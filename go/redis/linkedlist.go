package main

// 双端链表
type LinkedNode struct {
	Prev *LinkedNode
	Next *LinkedNode
	Data interface{}
}
