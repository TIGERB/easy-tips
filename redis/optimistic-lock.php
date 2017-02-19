<?php
/**
 * redis实战
 *
 * 实现乐观锁机制
 *
 * @author TIGERB <https://github.com/TIGERB>
 * @example php optimistic-lock.php
 */

$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);

// 监视 count 值
$redis->watch('count');

// 开启事务
$redis->multi();

// 操作count
$time = time();
$redis->set('count', $time);

//-------------------------------
/**
 * 模拟并发下其他进程进行set count操作 请执行下面操作
 *
 * redis-cli 执行 $redis->set('count', 'is simulate'); 模拟其他终端
 */
sleep(10);
//-------------------------------

// 提交事务
$res = $redis->exec();

if ($res) {
  // 成功...
  echo 'success:' . $time;
  return;
}

// 失败...
echo 'fail:' . $time;
