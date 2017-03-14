<?php
/**
 * redis实战
 *
 * 实现悲观锁机制
 *
 * @author TIGERB <https://github.com/TIGERB>
 * @example php pessmistic-lock.php
 */

$timeout = 5000;

$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);

do {
 $microtime = microtime(true) * 1000;
 $microtimeout = $microtime+$timeout+1;
 // 上锁
 $isLock = $redis->setnx('lock.count', $microtimeout);
 if (!$isLock) {
     $getTime = $redis->get('lock.count');
     if ($getTime > $microtime) {
        // 未超时继续等待
        continue;
     }
    // 超时,抢锁,可能有几毫秒级时间差可忽略
    $previousTime = $redis->getset('lock.count', $microtimeout);
    if ((int)$previousTime < $microtime) {
        break;
    }
 }
} while (!$isLock);

$count = $redis->get('count')? : 0;

// file_put_contents('/var/log/count.log.1', ($count+1));

// 业务逻辑
echo "执行count加1操作～ \n\n";
$redis->set('count', $count+1);
// 删除锁
$redis->del('lock.count');
// 打印count值
$count = $redis->get('count');
echo "count值为：$count \n";
