package main

import "fmt"

// 理解unsafe.Pointer笔记
// 理解uintptr笔记

type demo struct {
	PropertyOne string
	PropertyTwo string
}

func main() {
	demo := true
	fmt.Println(demo)
	// a := map[int]int{
	// 	1:  1,
	// 	2:  2,
	// 	3:  3,
	// 	4:  4,
	// 	5:  5,
	// 	6:  6,
	// 	7:  7,
	// 	8:  8,
	// 	9:  9,
	// 	10: 10,
	// 	11: 11,
	// 	12: 12,
	// 	13: 13,
	// 	14: 14,
	// 	15: 15,
	// 	16: 16,
	// 	17: 17,
	// 	18: 18,
	// 	19: 19,
	// }
	// fmt.Println(a)
	// fmt.Println("sys.Ctz64", sys.Ctz64(9))
	// a := "aaa"
	// b := &a
	// var c unsafe.Pointer
	// c = &a
	// d := &demo{
	// 	PropertyOne: "one",
	// 	PropertyTwo: "two",
	// }
	// b = d
	// c = d

	// ./main.go:14:4: cannot use &a (type *string) as type unsafe.Pointer in assignment
	// ./main.go:19:4: cannot use d (type *demo) as type *string in assignment
	// ./main.go:20:4: cannot use d (type *demo) as type unsafe.Pointer in assignment

	// ------------

	// a := "2"
	// b := &a
	// fmt.Println("b", b, *b)
	// d := 1
	// c := &d
	// fmt.Println("c", c)
	// c = (*int)(unsafe.Pointer(b))
	// fmt.Println("c", c, *c)

	// b 0xc000010200 2
	// c 0xc0000180a8
	// c 0xc000010200 17629802

	// ------------ 理解 uintptr

	// d := &demo{
	// 	PropertyOne: "one",
	// 	PropertyTwo: "two",
	// }
	// fmt.Println("d", d)
	// fmt.Printf("d %p", d)

	// dStr := (*string)(unsafe.Pointer(d))
	// fmt.Println("")
	// fmt.Printf("dStr %p %v", dStr, *dStr)
	// fmt.Println("")
	// dTwoPointer := (*string)(unsafe.Pointer(uintptr(unsafe.Pointer(d)) + unsafe.Offsetof(d.PropertyTwo)))
	// fmt.Println("uintptr", *(dTwoPointer))
	// *dTwoPointer = "three"
	// fmt.Println("uintptr", *(dTwoPointer))
	// fmt.Println("d.PropertyTwo", d.PropertyTwo)

	// var a float64 = 1.0
	// var b *int64
	// b = (*int64)(unsafe.Pointer(&a))
	// fmt.Println("a, b", &a, a, b, *b)

	// ------------

	// a := "aaa"

	// fmt.Println(strings.Count(a, ""), len(a))

}
