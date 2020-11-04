package main

import (
	"fmt"
	"strings"
)

// 理解unsafe.Pointer笔记
// 理解uintptr笔记

type demo struct {
	PropertyOne string
	PropertyTwo string
}

func main() {
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

	// ------------

	a := "aaa"

	fmt.Println(strings.Count(a, ""), len(a))

}
