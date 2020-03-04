<?php
/**
 * redis实战
 *
 * 实现悲观锁机制
 *
 * @author TIGERB <https://github.com/TIGERB>
 * @example php pessimistic-lock.php
 */

$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);

$timeout = 3;
$lockKey = 'lock.count';
do {
    // 上锁
    $isLock = $redis->set($lockKey, '1', ['nx', 'ex' => $timeout]); // 避免死锁，设置 $timeout
    if (!$isLock) {
        // 睡眠 降低抢锁频率　缓解redis压力
        echo "资源繁忙，正在重试...". microtime(true) . PHP_EOL;
        usleep(5000);
    } else {
        break;
    }
} while (!$isLock);

$key = 'count';
// 执行业务逻辑
echo "执行count加1操作～" . PHP_EOL;
$redis->incr($key);

// 删除锁
$redis->del($lockKey);

// 打印 count 值
$count = $redis->get($key);
echo "count值为：{$count} " . PHP_EOL;
