<?php
/**
 * redis实战
 * Redis practice
 *
 * 简单字符串缓存
 * easy string cache
 *
 * @author TIGERB <https://github.com/TIGERB>
 * @example php cache.php
 */

$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);

/**
 * 缓存数据
 * cache data
 */
$redis->set('cache-key', json_encode([
  'data-list' => '这是个缓存数据～',
  'data-list-en' => 'This a data of cache~',

]), JSON_UNESCAPED_UNICODE);
echo "字符串缓存成功～ \n\n";
echo "String cache success \n\n";

/**
 * 获取缓存数据
 * get cache data
 */
$data = $redis->get('cache-key');
echo "读取缓存数据为： \n";
echo "The cache data is: \n";
print_r(json_decode($data,true));
