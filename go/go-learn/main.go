package main

import (
	"fmt"
	"unsafe"
)

type flightGroup interface {
	// Done is called when Do is done.
	Do(key string, fn func() (interface{}, error)) (interface{}, error)
}

// golangci-lint run --disable-all -E maligned

type Demo struct {
	A int8
	B int64
	C int8
}

type DemoTwo struct {
	A int8
	C int8
	B int64
}

func main() {
	demo := Demo{}
	demoTwo := DemoTwo{}
	fmt.Println(unsafe.Sizeof(demo), unsafe.Sizeof(demoTwo))
}
