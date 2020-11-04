package composite

import (
	"easy-tips/go/patterns/composite/concurrency"
	"easy-tips/go/patterns/composite/normal"
	"runtime"
	"testing"
)

// func TestComposite(t *testing.T) {
// 	normal.Demo()
// }

// func TestCompositeConcurrency(t *testing.T) {
// 	concurrency.Demo()
// }

// go test -benchmem -run=^$ easy-tips/go/patterns/composite -bench . -v -count=1 --benchtime 20s

func Benchmark_Normal(b *testing.B) {
	b.SetParallelism(runtime.NumCPU())
	b.RunParallel(func(pb *testing.PB) {
		for pb.Next() {
			normal.Demo()
		}
	})
}

func Benchmark_Concurrency(b *testing.B) {
	b.SetParallelism(runtime.NumCPU())
	b.RunParallel(func(pb *testing.PB) {
		for pb.Next() {
			concurrency.Demo()
		}
	})
}
