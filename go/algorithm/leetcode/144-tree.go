package leetcode

import "fmt"

// type TreeNode struct {
// 	Val   int
// 	Left  *TreeNode
// 	Right *TreeNode
// }

func main() {
	two := &TreeNode{Val: 3}
	one := &TreeNode{Val: 2, Left: two}
	root := &TreeNode{Val: 1, Left: one}
	fmt.Println(preorderTraversal(root))
}

// type Stack struct {
// 	data []
// }

/**
 * Definition for a binary tree node.
 * type TreeNode struct {
 *     Val int
 *     Left *TreeNode
 *     Right *TreeNode
 * }
 */
func preorderTraversal(root *TreeNode) []int {
	res := []int{}
	if root == nil {
		return res
	}

	// 初始化栈
	stack := []*TreeNode{}
	// 入栈
	pushStack := func(stack []*TreeNode, val *TreeNode) []*TreeNode {
		return append(stack, val)
	}
	// 出栈
	popStack := func(stack []*TreeNode) ([]*TreeNode, *TreeNode) {
		lenStack := len(stack)
		if lenStack == 0 {
			return []*TreeNode{}, nil
		}
		val := stack[lenStack-1]
		return append(stack[:0], stack[:lenStack-1]...), val
	}

	// 根节点入栈
	stack = pushStack(stack, root)
	node := &TreeNode{}
	for {
		if len(stack) == 0 {
			return res
		}
		stack, node = popStack(stack)
		res = append(res, node.Val)
		if node.Right != nil {
			stack = pushStack(stack, node.Right)
		}
		if node.Left != nil {
			stack = pushStack(stack, node.Left)
		}
	}
}

/**
 * Definition for a binary tree node.
 * type TreeNode struct {
 *     Val int
 *     Left *TreeNode
 *     Right *TreeNode
 * }
 */
func preorderTraversalRecursion(root *TreeNode) []int {
	if root == nil {
		return []int{}
	}
	res := []int{root.Val}
	res = append(res, preorderTraversal(root.Left)...)
	res = append(res, preorderTraversal(root.Right)...)
	return res
}
