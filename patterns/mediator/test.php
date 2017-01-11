<?php
/**
 * 行为型模式
 *
 * php中介者模式
 * 理解：就是不同的对象之间通信，互相之间不直接调用，而是通过一个中间对象（中介者）
 * 使用场景：对象之间大量的互相依赖
 * 下面实现一个房屋中介
 *
 *
 * @author  TIGERB <https://github.com/TIGERB>
 * @example 运行 php test.php
 */


// 注册自加载
spl_autoload_register('autoload');

function autoload($class)
{
  require dirname($_SERVER['SCRIPT_FILENAME']) . '//..//' . str_replace('\\', '/', $class) . '.php';
}

/************************************* test *************************************/

use mediator\Tenant;
use mediator\Landlord;
use mediator\HouseMediator;

try {
  // 初始化一个租客
  $tenant = new Tenant('小明');

  // 小明直接找小梅租房
  $landlord = new Landlord('小梅');
  echo $landlord->doSomthing($tenant);

  // 小明通过房屋中介租房
  // 初始化一个房屋中介
  $mediator = new HouseMediator();
  // 租房
  $mediator->rentHouse($tenant);

} catch (\Exception $e) {
  echo 'error:' . $e->getMessage();
}
