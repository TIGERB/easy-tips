<?php
/**
 * php下redis实战
 *
 * @author  TIGERB <https://github.com/TIGERB>
 * @example php test.php
 */

// 参数
$method = '';
if (isset($argv[1])) {
  $method = $argv[1];
}
$path   = dirname($_SERVER['SCRIPT_FILENAME']);

/*---------------------------- cache ---------------------------------*/

if ($method === 'cache') {
  require($path . '/cache.php');
  die;
}

/*---------------------------- queue ---------------------------------*/

if ($method === 'queue') {
  require($path . '/queue.php');
  die;
}

/*---------------------------- p-lock ---------------------------------*/

if ($method === 'p-lock') {
  require($path . '/pessmistic-lock.php');
  die;
}

/*---------------------------- o-lock ---------------------------------*/

if ($method === 'o-lock') {
  require($path . '/optimistic-lock.php');
  die;
}

/*---------------------------- sub ---------------------------------*/

if ($method === 'sub') {
  require($path . '/subscribe-publish/subscribe.php');
  die;
}

/*---------------------------- pub ---------------------------------*/

if ($method === 'pub') {
  require($path . '/subscribe-publish/publish.php');
  die;
}

/*---------------------------- warning ---------------------------------*/

echo "\n";
echo "参数有误，正确示例：php {$path}/test.php p-lock \n";
echo "====================================== \n";
echo "参数列表： \n";
print_r([
  '缓存'   => 'cache',
  '队列'   => 'queue',
  '悲观锁' => 'p-lock',
  '乐观锁' => 'o-lock',
  '消息订阅/推送' => [
      '订阅' => 'sub',
      '推送' => 'pub'
  ],
]);
