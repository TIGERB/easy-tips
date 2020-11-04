# 延时队列实现方案

> 参考： https://mp.weixin.qq.com/s/DcyXPGxXFYcXCQJII1INpg

1. Redis zset

```
1. 添加任务 `zadd {{key}} {{时间戳}} {{任务名称}}`
2. 每秒定时任务执行 `zrangebyscore {{key}} -inf +inf limit 0 1 WITHSCORES`
    2.1 如果score大于当前时间戳 则donothing 等待下秒定时任务
    2.2 如果score小于等于当前时间戳 则异步执行任务
```

2. RabbitMQ

```
1. 进程消费死信队列
2. 延时消息设置ttl
3. 消息过期被投递到死信队列
```

3. 时间轮

```

```

# 优先队列实现方案

> 参考：https://www.cxyxiaowu.com/5417.html

1. 二叉堆