<?php
/**
 * 行为类模式
 * 
 * php观察者模式
 * 
 * @author  TIGERB <tigerbcode@gmail.com>
 * @example 运行 php test.php
 */


// 注册自加载
spl_autoload_register('autoload');

function autoload($class)
{
  require dirname($_SERVER['SCRIPT_FILENAME']) . '//..//' . str_replace('\\', '/', $class) . '.php';
}

/************************************* test *************************************/

use observer\Observable;
use observer\ObserverExampleOne;
use observer\ObserverExampleTwo;

// 注册一个被观察者对象
$observable = new Observable();
// 注册观察者们
$observerExampleOne = new ObserverExampleOne();
$observerExampleTwo = new ObserverExampleTwo();

// 附加观察者
$observable->attach($observerExampleOne);
$observable->attach($observerExampleTwo);

// 被观察者通知观察者们
$observable->notify();
