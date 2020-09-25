package leetcode

// ================
// leetcode题解
// 347. 前 K 个高频元素
// ================

// 给定一个非空的整数数组，返回其中出现频率前 k 高的元素。

// 示例 1:

// 输入: nums = [1,1,1,2,2,3], k = 2
// 输出: [1,2]
// 示例 2:

// 输入: nums = [1], k = 1
// 输出: [1]

// 提示：

// 你可以假设给定的 k 总是合理的，且 1 ≤ k ≤ 数组中不相同的元素的个数。
// 你的算法的时间复杂度必须优于 O(n log n) , n 是数组的大小。
// 题目数据保证答案唯一，换句话说，数组中前 k 个高频元素的集合是唯一的。
// 你可以按任意顺序返回答案。

// 来源：力扣（LeetCode）
// 链接：https://leetcode-cn.com/problems/top-k-frequent-elements
// 著作权归领扣网络所有。商业转载请联系官方授权，非商业转载请注明出处。

//  解法一：快排
func topKFrequent(nums []int, k int) []int {
	res := []int{}
	if len(nums) == 0 || k == 0 {
		return res
	}
	numsMap := map[int]int{}
	for _, v := range nums {
		if _, ok := numsMap[v]; ok {
			numsMap[v]++
			continue
		}
		numsMap[v] = 1
	}

	resSlice := []int{}
	for _, v := range numsMap {
		resSlice = append(resSlice, v)
	}

	// resSlice 快排
	resSliceLen := len(resSlice)
	resSlice = quick(resSlice, 0, resSliceLen)

	// 输出后k个(最大值)
	resSlice = append(resSlice[:0], resSlice[resSliceLen-k:]...)

	resSliceMap := map[int]byte{}
	for _, v := range resSlice {
		resSliceMap[v] = '0'
	}
	for k, v := range numsMap {
		if _, ok := resSliceMap[v]; ok {
			res = append(res, k)
		}
	}

	return res
}

func quick(resSlice []int, left int, right int) (res []int) {
	if left >= right {
		return resSlice
	}
	base := left
	i := right
	j := left
	for {
		if i <= j {
			break
		}
		for i = right; i > base; i-- {
			if resSlice[i] < resSlice[base] {
				tmp := resSlice[i]
				resSlice[i] = resSlice[base]
				resSlice[base] = tmp
				base = i
				break
			}
		}

		for j = left; j < base; j++ {
			if resSlice[j] > resSlice[base] {
				tmp := resSlice[j]
				resSlice[j] = resSlice[base]
				resSlice[base] = tmp
				base = j
				break
			}
		}
	}

	resSlice = quick(resSlice, left, base-1)
	resSlice = quick(resSlice, base+1, right)

	return resSlice
}
