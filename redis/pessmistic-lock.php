<?php
/**
 * redis实战
 *
 * 实现悲观锁机制
 *
 * @author TIGERB <https://github.com/TIGERB>
 * @example php pessmistic-lock.php
 */

$timeout = 500;

$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);

do {
 $microtime = explode(' ', microtime());
 $microtime = (int)round(($microtime[1]+$microtime[0])*1000, 0);
 $microtimeout = $microtime+$timeout+1;
 // 上锁
 $isLock = $redis->setnx('lock.count', $microtimeout);
 if (!$isLock) {
   // 超时判断
   $lockTime = $redis->get('lock.count');
   if (!$lockTime || $lockTime > $microtime) {
     // 未超时
     continue;
   }
   // 抢锁
   $previousTime = $redis->getset('lock.count', $microtime);
   if ($previousTime < $microtime) {
     break;
   }
 }
} while (!$isLock);

$count = $redis->get('count')? : 0;

// file_put_contents('/var/log/count.log.1', ($count+1));

// 业务逻辑
$redis->set('count', $count+1);
// 删除锁
$redis->del('lock.count');
