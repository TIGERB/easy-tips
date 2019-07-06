package main

// 导入net/http包
import (
	"fmt"
	"time"

	"github.com/gomodule/redigo/redis"
)

var RedisPool *redis.Pool

func init() {
	RedisPool = NewRedisPool()
	fmt.Println("RedisPool.Stats: ", RedisPool.Stats())
}

func main() {
	// ------------------ defer 使用 ------------------
	for {
		redisConn := RedisPool.Get()
		defer redisConn.Close()
		defer DeferDemo()

		// 一堆业务逻辑
		_, err := redisConn.Do("set", "demo_key", "666")
		if err != nil {
			fmt.Println("redis set err: ", err.Error())
			continue
		}
		res, _ := redis.String(redisConn.Do("get", "demo_key"))
		fmt.Println("get demo_key: ", res, " redis conn active", RedisPool.ActiveCount())
		time.Sleep(1 * time.Second)
	}
}

func NewRedisPool() *redis.Pool {
	return &redis.Pool{
		MaxIdle:     6,
		IdleTimeout: 240 * time.Second,
		Dial: func() (redis.Conn, error) {
			c, err := redis.Dial("tcp", "127.0.0.1:6379")
			if err != nil {
				return nil, err
			}
			return c, nil
		},
		TestOnBorrow: func(c redis.Conn, t time.Time) error {
			if time.Since(t) < time.Minute {
				return nil
			}
			_, err := c.Do("PING")
			return err
		},
	}
}

func DeferDemo() {
	fmt.Println("DeferDemo...")
}
