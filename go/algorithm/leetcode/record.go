package leetcode

// 假设有打乱顺序的一群人站成一个队列。 每个人由一个整数对(h, k)表示，其中h是这个人的身高，k是排在这个人前面且身高大于或等于h的人数。 编写一个算法来重建这个队列。

// 注意：
// 总人数少于1100人。

// 示例

// 输入:
// [[7,0], [4,4], [7,1], [5,0], [6,1], [5,2]]

// 输出:
// [[5,0], [7,0], [5,2], [6,1], [4,4], [7,1]]

// 来源：力扣（LeetCode）
// 链接：https://leetcode-cn.com/problems/queue-reconstruction-by-height
// 著作权归领扣网络所有。商业转载请联系官方授权，非商业转载请注明出处。

func reconstructQueue(people [][]int) [][]int {
	return people
}

// 给定一个单链表，把所有的奇数节点和偶数节点分别排在一起。请注意，这里的奇数节点和偶数节点指的是节点编号的奇偶性，而不是节点的值的奇偶性。

// 请尝试使用原地算法完成。你的算法的空间复杂度应为 O(1)，时间复杂度应为 O(nodes)，nodes 为节点总数。

// 示例 1:

// 输入: 1->2->3->4->5->NULL
// 输出: 1->3->5->2->4->NULL
// 示例 2:

// 输入: 2->1->3->5->6->4->7->NULL
// 输出: 2->3->6->7->1->5->4->NULL
// 说明:

// 应当保持奇数节点和偶数节点的相对顺序。
// 链表的第一个节点视为奇数节点，第二个节点视为偶数节点，以此类推。

// 来源：力扣（LeetCode）
// 链接：https://leetcode-cn.com/problems/odd-even-linked-list
// 著作权归领扣网络所有。商业转载请联系官方授权，非商业转载请注明出处。

// 给定一个非负整数数组 A， A 中一半整数是奇数，一半整数是偶数。

// 对数组进行排序，以便当 A[i] 为奇数时，i 也是奇数；当 A[i] 为偶数时， i 也是偶数。

// 你可以返回任何满足上述条件的数组作为答案。

type ListNode struct {
	Val  int
	Next *ListNode
}

/**
 * Definition for singly-linked list.
 * type ListNode struct {
 *     Val int
 *     Next *ListNode
 * }
 */
func oddEvenList(head *ListNode) *ListNode {
	i := 0
	for head != nil {
		head = head.Next
		i++
	}
	return head
}

// 示例：

// 输入：[4,2,5,7]
// 输出：[4,5,2,7]
// 解释：[4,7,2,5]，[2,5,4,7]，[2,7,4,5] 也会被接受。
//

// 提示：

// 2 <= A.length <= 20000
// A.length % 2 == 0
// 0 <= A[i] <= 1000

// 来源：力扣（LeetCode）
// 链接：https://leetcode-cn.com/problems/sort-array-by-parity-ii
// 著作权归领扣网络所有。商业转载请联系官方授权，非商业转载请注明出处。

func sortArrayByParityII(A []int) []int {
	// 分类所有奇数偶数
	odds := []int{}
	evens := []int{}
	for _, item := range A {
		if item&1 == 1 {
			evens = append(evens, item)
			continue
		}
		odds = append(odds, item)
	}

	for k := range A {
		if k&1 == 1 {
			// 奇数位
			A[k] = evens[0]
			evens = append(evens[:0], evens[1:]...)
			continue
		}
		// 偶数位
		A[k] = odds[0]
		odds = append(odds[:0], odds[1:]...)
	}

	return A
}

func sortArrayByParityIIDoublePointerMine(A []int) []int {
	iMax := len(A) - 1
	for k, val := range A {
		// 当前索引是奇数还是偶数
		kIsEVen := (k&1 == 1)
		valIsEVen := (val&1 == 1)
		if kIsEVen == valIsEVen {
			// 索引和值一致
			continue
		}
		findValRes := false
		i := k
		for {
			if findValRes || i >= iMax {
				break
			}
			i++
			// k为奇数找奇数
			if kIsEVen {
				// k为偶数找偶数
				if A[i]&1 != 1 {
					continue
				}
				// 交换k和i
				A[i] = A[i] ^ A[k]
				A[k] = A[i] ^ A[k]
				A[i] = A[i] ^ A[k]
				findValRes = true
				continue
			}
			if A[i]&1 == 1 {
				continue
			}
			// 交换k和i
			A[i] = A[i] ^ A[k]
			A[k] = A[i] ^ A[k]
			A[i] = A[i] ^ A[k]
			findValRes = true
			continue
		}
	}
	return A
}

func sortArrayByParityIIDoublePointer(A []int) []int {
	for i, j := 0, 1; i < len(A); i += 2 {
		// 如果奇数索引值是个偶数的话
		if A[i]&1 == 1 {
			// 遍历索引奇数位 找到第一个偶数为止
			for A[j]&1 == 1 {
				j += 2
			}
			// 交换
			A[i], A[j] = A[j], A[i]
		}
	}
	return A
}
