package algorithm

// 树：节点node、根节点root、子节点child、叶节点leaf
// 二叉树：每个节点最多有两个节点
// 完全二叉树
// 满二叉树

// bigTopHeap 大顶堆
func bigTopHeap(input []int) []int {
	aroundFind := false
	lenInput := len(input)
	i := 0
	for {
		if 2*(i+1) > lenInput {
			if !aroundFind {
				return input
			}
			i = 0
			aroundFind = false
			continue
		}
		if input[i] < input[2*i+1] {
			tmp := input[i]
			input[i] = input[2*i+1]
			input[2*i+1] = tmp
			aroundFind = true
		}
		if 2*(i+1) < lenInput && input[i] < input[2*i+2] {
			tmp := input[i]
			input[i] = input[2*i+2]
			input[2*i+2] = tmp
			aroundFind = true
		}
		i++
	}
}

// lessTopHeap 小顶堆
func lessTopHeap(input []int) []int {
	aroundFind := false
	lenInput := len(input)
	i := 0
	for {
		if 2*(i+1) > lenInput {
			if !aroundFind {
				return input
			}
			i = 0
			aroundFind = false
			continue
		}
		if input[i] > input[2*i+1] {
			tmp := input[i]
			input[i] = input[2*i+1]
			input[2*i+1] = tmp
			aroundFind = true
		}
		if 2*(i+1) < lenInput && input[i] > input[2*i+2] {
			tmp := input[i]
			input[i] = input[2*i+2]
			input[2*i+2] = tmp
			aroundFind = true
		}
		i++
	}
}

// func main() {
// 	input := []int{2, 7, 26, 25, 19, 17, 1, 90, 3, 36}
// 	fmt.Println(bigTopHeap(input))
// 	fmt.Println(lessTopHeap(input))
// }
