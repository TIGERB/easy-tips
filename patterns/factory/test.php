<?php
/**
 * 创建型模式
 *
 * php工厂模式

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

use factory\Farm;

// 初始化一个工厂
$farm = new Farm();

// 生产一只鸡
$farm->produce('chicken');

// 生产一只猪
$farm->produce('pig');
