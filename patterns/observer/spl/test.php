<?php
/**
 * 行为型模式
 *
 * php观察者模式
 * 观察者观察被观察者，被观察者通知观察者
 *
 * @author jealone <https://github.com/jealone>
 * @example 运行 php test.php
 */


// 注册自加载
spl_autoload_register('autoload');

function autoload($class)
{
    require dirname($_SERVER['SCRIPT_FILENAME']) . '//..//' . str_replace('\\', '/', $class) . '.php';
}

/************************************* test *************************************/

use spl\Observable;
use spl\ObserverExampleOne;
use spl\ObserverExampleTwo;

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
