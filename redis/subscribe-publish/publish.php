<?php
/**
 * redis实战
 *
 * 发布
 *
 * @author TIGERB <https://github.com/TIGERB>
 * @example php publish.php
 */

//redis连接到数据库
require_once '../redis_connect.php';
//实例化redis对象
$redis = RedisConnect::getRedisInstance();
//发布
$redis->publish('msg', '来自msg频道的推送');
echo "msg频道消息推送成功～ \n";
$redis->close();
