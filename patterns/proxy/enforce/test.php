<?php

// 注册自加载
spl_autoload_register('autoload');

function autoload($class)
{
    require dirname($_SERVER['SCRIPT_FILENAME']) . '//..//..//' . str_replace('\\', '/', $class) . '.php';
}

/************************************* test *************************************/

use proxy\enforce\RealSubject;


try {
    echo "未加代理之前：\n";
    // 生产运动鞋
    $subject = new RealSubject();
    $subject->doSomething();

    echo "\n--------------------\n";

    echo "使用强制代理：\n";
    $proxy = $subject->getProxy();
    // 代工厂生产运动鞋
    $proxy->doSomething();
} catch (\Exception $e) {
    echo $e->getMessage();
}
