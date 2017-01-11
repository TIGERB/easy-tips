<?php
/**
 * 行为型模式
 *
 * php策略模式
 * 策略依照使用而定
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

use strategy\Substance;
use strategy\StrategyExampleOne;
use strategy\StrategyExampleTwo;

// 使用策略1
$substanceOne = new Substance(new StrategyExampleOne);
$substanceOne->someOperation();

// 使用策略2
$substanceTwo = new Substance(new StrategyExampleTwo);
$substanceTwo->someOperation();
