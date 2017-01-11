<?php
/**
 * 结构型模式
 *
 * php装饰器模式
 * 对现有的对象增加功能
 * 和适配器的区别：适配器是连接两个接口，装饰器是对现有的对象包装
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

use decorator\DecoratorBrand;
use decorator\ShoesSport;
use decorator\ShoesSkateboard;

try {
  echo "未加装饰器之前：\n";
  // 生产运动鞋
  $shoesSport = new ShoesSport();
  $shoesSport->product();

  echo "\n--------------------\n";
  //-----------------------------------

  echo "加贴标装饰器：\n";
  // 初始化一个贴商标适配器
  $DecoratorBrand = new DecoratorBrand(new ShoesSport());
  $DecoratorBrand->_brand = 'nike';
  // 生产nike牌运动鞋
  $DecoratorBrand->product();
} catch (\Exception $e) {
  echo $e->getMessage();
}
