<?php
/**
 * 结构型模式
 *
 * php装饰器模式
 * 对现有的对象增加功能
 * 和适配器的区别：适配器是连接两个接口，装饰器是对现有的对象包装
 *
 * @author  zhudong
 * @example 运行 php testpulish.php
 */


// 注册自加载
spl_autoload_register('autoload');

function autoload($class)
{
    require dirname($_SERVER['SCRIPT_FILENAME']) . '//..//' . str_replace('\\', '/', $class) . '.php';
}

use decorator\BasicPulisher;
use decorator\MoviePulisher;
use decorator\MusicPublisher;

try{

    $basicPulisher = new BasicPulisher();
    $moviePulisher = new MoviePulisher();
    $musicPulisher = new MusicPublisher();

    $moviePulisher->derect($basicPulisher);
    $musicPulisher->derect($moviePulisher);
    $musicPulisher->pulishText();


}catch (\Exception $e) {
    echo $e->getMessage();
}