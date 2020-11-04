package main

import (
	"context"
	"fmt"
	"time"
)

type key string

func main() {
	var key1 key = "key1"
	ctx, cfun := context.WithCancel(context.Background())
	ctxVal := context.WithValue(ctx, key1, "value1")

	for index := 0; index < 10; index++ {
		go fork(ctxVal, key1)
	}

	time.Sleep(2 * time.Second)
	cfun()
	time.Sleep(10 * time.Second)
}

func fork(ctx context.Context, key1 key) {
	for {
		select {
		case <-ctx.Done():
			fmt.Println("exit...")
			return
		default:
			fmt.Println(ctx.Value(key1), "doing...")
		}
	}
}
