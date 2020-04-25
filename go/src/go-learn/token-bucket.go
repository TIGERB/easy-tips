package main

import (
	"errors"
	"fmt"
	"sync/atomic"
	"time"
)

// 定时往桶里放n枚令牌

// TokenBucket TokenBucket
type TokenBucket struct {
	interval time.Duration
	buf      int64
	incre    int64
	max      int64
	min      int64
	qps      int64
	prev     time.Time
	now      time.Time
}

// GetToken GetToken
func (b *TokenBucket) GetToken() error {
	atomic.AddInt64(&b.qps, 1)
	if b.buf <= b.min {
		return errors.New("no token")
	}
	atomic.AddInt64(&b.buf, -1)
	fmt.Printf("remove token: %d \n", b.buf)
	return nil
}

func token() {
	bucket := &TokenBucket{
		interval: time.Duration(2000) * time.Millisecond,
		buf:      0,
		incre:    10,
		max:      100,
		min:      0,
	}
	fmt.Println(bucket.prev.Format("2006-01-02 15:04:05"))
	go (func() {
		ticker := time.NewTicker(bucket.interval)
		for v := range ticker.C {
			fmt.Println(fmt.Sprintf("push token into the bucket %s", v.Format("2006-01-02 15:04:05")))
			if bucket.buf >= bucket.max {
				fmt.Println("overflow and throw token")
				continue
			}
			atomic.AddInt64(&bucket.buf, bucket.incre)
			fmt.Printf("add tokens: %d, %d \n", bucket.incre, bucket.buf)
		}
	})()

	var qps int64 = 1
	prev := time.Now()
	for {
		if bucket.GetToken() == nil {
			now := time.Now()
			if prev.Unix() == now.Unix() {
				atomic.AddInt64(&qps, 1)
				fmt.Printf("qps %d \n", qps)
			} else {
				qps = 1
				fmt.Printf("qps %d \n", qps)
			}
			surplus := now.Sub(prev)
			prev = now
			fmt.Printf("get tokens %s \n", surplus)
			continue
		}
	}
}
