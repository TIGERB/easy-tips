package main

import "fmt"

// Tree 二叉树
type Tree struct {
	Val    int
	Left   *Tree
	Right  *Tree
	IsRoot bool
}

// demo 二叉树
//       1
//    /     \
//   2       3
//  / \     / \
// 4   5  6    7
//       / \
//      8   9
//
// 初始化demo二叉树
//
// 理论输出
// 前序输出: 1 2 4 5 3 6 8 9 7
// 中序输出: 4 2 5 1 8 6 9 3 7
// 后序输出: 4 5 2 8 9 6 7 3 1
// 层序输出: 1 2 3 4 5 6 7 8 9
var node9 = &Tree{
	Val: 9,
}
var node8 = &Tree{
	Val: 8,
}
var node7 = &Tree{
	Val: 7,
}
var node6 = &Tree{
	Val:   6,
	Left:  node8,
	Right: node9,
}
var node5 = &Tree{
	Val: 5,
}
var node4 = &Tree{
	Val: 4,
}
var node3 = &Tree{
	Val:   3,
	Left:  node6,
	Right: node7,
}
var node2 = &Tree{
	Val:   2,
	Left:  node4,
	Right: node5,
}
var root = &Tree{
	Val:    1,
	Left:   node2,
	Right:  node3,
	IsRoot: true,
}

// 前序
func preorder(t *Tree) {
	if t == nil {
		return
	}
	fmt.Println(t.Val)
	preorder(t.Left)
	preorder(t.Right)
}

// 中序
func inorder(t *Tree) {
	if t == nil {
		return
	}
	inorder(t.Left)
	fmt.Println(t.Val)
	inorder(t.Right)
}

// 后序
func postorder(t *Tree) {
	if t == nil {
		return
	}
	postorder(t.Left)
	postorder(t.Right)
	fmt.Println(t.Val)
}

// Queue 队列
type Queue struct {
	Val    []*Tree
	Length int
}

// Push 入队列
func (q *Queue) Push(t *Tree) {
	q.Val = append(q.Val, t)
}

// Pop 出队列
func (q *Queue) Pop() (node *Tree) {
	len := q.Len()
	if len == 0 {
		panic("Queue is empty")
	}
	node = q.Val[0]
	if len == 1 {
		q.Val = []*Tree{}
	} else {
		q.Val = q.Val[1:]
	}
	return
}

// Len 队列长度
func (q *Queue) Len() int {
	q.Length = len(q.Val)
	return q.Length
}

// 层序
func levelorder(r *Tree) {

	queue := Queue{}
	queue.Push(root)
	for queue.Len() > 0 {
		node := queue.Pop()
		if node == nil {
			panic("node is nil")
		}
		// 打印根结点
		if node.IsRoot {
			fmt.Println(node.Val)
		}
		if node.Left != nil {
			fmt.Println(node.Left.Val)
			queue.Push(node.Left)
		}
		if node.Right != nil {
			fmt.Println(node.Right.Val)
			queue.Push(node.Right)
		}
	}
}

func main() {
	fmt.Println("前序遍历开始...")
	preorder(root)
	fmt.Println("")

	fmt.Println("中序遍历开始...")
	inorder(root)
	fmt.Println("")

	fmt.Println("后序遍历开始...")
	postorder(root)
	fmt.Println("")

	fmt.Println("层序遍历开始...")
	levelorder(root)
	fmt.Println("")
}
