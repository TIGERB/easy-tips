package main

import "fmt"

// 快速排序
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

func main() {
	a := []int{2, 21, 3, 1, 5, 91, 7, 0, 21, 11, 12, 33}
	fmt.Println(quick(a, 0, len(a)-1))
}
