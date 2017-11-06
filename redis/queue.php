<?php
/**
 * redis实战
 * Redis practice
 *
 * 利用列表list实现简单队列
 * Use list to implement a simple queue
 *
 * @author TIGERB <https://github.com/TIGERB>
 * @example php cache.php
 */

//redis连接到数据库
require_once 'redis_connect.php';
//实例化redis对象
$redis = RedisConnect::getRedisInstance();
// 进队列
// push data to queue
$userId = mt_rand(000000, 999999);
$redis->rpush('QUEUE_NAME', json_encode(['user_id' => $userId]));
$userId = mt_rand(000000, 999999);
$redis->rpush('QUEUE_NAME', json_encode(['user_id' => $userId]));
$userId = mt_rand(000000, 999999);
$redis->rpush('QUEUE_NAME', json_encode(['user_id' => $userId]));
echo "数据进队列成功 \n";
echo "push data to queue success \n";

// 查看队列
// show queue
$res = $redis->lrange('QUEUE_NAME', 0, 1000);
echo "当前队列数据为： \n";
echo "The queue's data are： \n";
print_r($res);

echo "----------------------------- \n";

// 出队列
// pop up the earlier data from queue
$redis->lpop('QUEUE_NAME');
echo "数据出队列成功 \n";
echo "pop up success \n";

// 查看队列
$res = $redis->lrange('QUEUE_NAME', 0, 1000);
echo "当前队列数据为： \n";
echo "The queue's data are： \n";
print_r($res);
