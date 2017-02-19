<?php
/**
 * redis实战
 *
 * 简单字符串缓存
 *
 * @author TIGERB <https://github.com/TIGERB>
 * @example php cache.php
 */

$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);

// 缓存数据
$redis->set('cache-key', json_encode([
  'data-list' => '这是个缓存数据～'
]), JSON_UNESCAPED_UNICODE);
echo "字符串缓存成功～ \n\n";

// 获取缓存数据
$data = $redis->get('cache-key');
echo "读取缓存数据为： \n";
print_r(json_decode($data,true));
