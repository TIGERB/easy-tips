<?php
/**
 * redis实战
 *
 * 利用列表list实现简单队列
 *
 * @author TIGERB <https://github.com/TIGERB>
 * @example php cache.php
 */

$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);

// 进队列
$userId = mt_rand(000000, 999999);
$redis->rpush('QUEUE_NAME',json_encode(['user_id' => $userId]));
$userId = mt_rand(000000, 999999);
$redis->rpush('QUEUE_NAME',json_encode(['user_id' => $userId]));
$userId = mt_rand(000000, 999999);
$redis->rpush('QUEUE_NAME',json_encode(['user_id' => $userId]));
echo "数据进队列成功 \n";

// 查看队列
$res = $redis->lrange('QUEUE_NAME', 0, 1000);
echo "当前队列数据为： \n";
print_r($res);

echo "----------------------------- \n";

// 出队列
$redis->lpop('QUEUE_NAME');
echo "数据出队列成功 \n";

// 查看队列
$res = $redis->lrange('QUEUE_NAME', 0, 1000);
echo "当前队列数据为： \n";
print_r($res);
