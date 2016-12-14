<?php
/**
 * 结构型模式
 *
 * php享元（轻量级）模式
 * 就是缓存了创建型模式创建的对象，不知道为什么会归在结构型模式中，个人觉得创建型模式更合适，哈哈～
 * 其次，享元强调的缓存对象，外观模式强调的对外保持简单易用，是不是就大体构成了目前牛逼哄哄且满大
 * 的街【依赖注入容器】
 *
 * 下面我们借助最简单的’工厂模式‘来实现享元模式，就是给工厂加了个缓存池
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

use flyweight\Farm;

// 初始化一个工厂
$farm = new Farm();

// 成产一只鸡
$farm->produce('chicken')->getType();
// 再生产一只鸡
$farm->produce('chicken')->getType();
