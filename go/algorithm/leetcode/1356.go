package leetcode

func sortByBits(arr []int) []int {
	// if len(arr) == 0 {
	// 	return arr
	// }

	// for _, item := range arr {

	// }
	return arr
}

// 二进制有几个1
func countOne(num int) int {
	count := 0
	for {
		if num == 0 {
			break
		}
		if num&1 == 1 {
			count++
		}
		num = num >> 1
	}
	return count
}
