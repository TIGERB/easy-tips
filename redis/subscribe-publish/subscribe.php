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

  $redis = new \Redis();
  $redis->pconnect('127.0.0.1', 6379);

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
