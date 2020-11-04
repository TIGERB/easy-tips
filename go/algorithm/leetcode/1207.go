package main

func main() {

}

func uniqueOccurrences(arr []int) bool {
	resMap := map[int]int{}
	for _, item := range arr {
		if _, ok := resMap[item]; ok {
			resMap[item]++
			continue
		}
		resMap[item] = 1
	}
	resNewMap := map[int]int{}
	for _, item := range resMap {
		if _, ok := resNewMap[item]; ok {
			return false
		}
		resNewMap[item] = 1
	}
	return true
}
