<?php
/**
 * 结构型模式
 *
 * php代理器模式
 * 对对象加以【控制】
 * 和适配器的区别：适配器是连接两个接口（【改变】了接口）
 * 和装饰器的区别：装饰器是对现有的对象包装（【功能扩展】）
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

use proxy\Proxy;
use proxy\ShoesSport;
use proxy\ShoesSkateboard;

try {
  echo "未加代理之前：\n";
  // 生产运动鞋
  $shoesSport = new ShoesSport();
  $shoesSport->product();

  echo "\n--------------------\n";
  //-----------------------------------

  echo "加代理：\n";
  // 把运动鞋产品线外包给代工厂
  $proxy = new Proxy('sport');
  // 代工厂生产运动鞋
  $proxy->product();
} catch (\Exception $e) {
  echo $e->getMessage();
}
