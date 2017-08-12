<?php
/**
 * redis实战
 *
 * 订阅
 *
 * @author TIGERB <https://github.com/TIGERB>
 * @example php subscribe.php
 */

  // ini_set(‘default_socket_timeout’, -1);

  //redis连接到数据库
  require_once '../redis_connect.php';
  //实例化redis对象
  $redis = RedisConnect::getRedisInstance();

  //订阅
  echo "订阅msg这个频道，等待消息推送... \n";
  $redis->subscribe(['msg'], 'callfun');
  function callfun($redis, $channel, $msg)
  {
   print_r([
     'redis'   => $redis,
     'channel' => $channel,
     'msg'     => $msg
   ]);
  }
