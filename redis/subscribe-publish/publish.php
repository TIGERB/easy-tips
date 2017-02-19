<?php
/**
 * redis实战
 *
 * 发布
 *
 * @author TIGERB <https://github.com/TIGERB>
 * @example php publish.php
 */

  //发布
  $redis = new \Redis();
  $redis->connect('127.0.0.1', 6379);
  $redis->publish('msg', '来自msg频道的推送');
  echo "msg频道消息推送成功～ \n";
  $redis->close();
