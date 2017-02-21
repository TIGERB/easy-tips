<?php
/**
 * 创建型模式
 *
 * php抽象工厂模式
 *
 * 说说我理解的工厂模式和抽象工厂模式的区别：
 * 工厂就是一个独立公司，负责生产对象；
 * 抽象工厂就是集团，负责生产子公司（工厂）；

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

use factoryAbstract\FarmProducer;

// 初始化一个工厂生产器
$farmProducer = new FarmProducer();

// 成产一个动物农场
$farmAnimail  = $farmProducer->produceFarm('animal');
// 生产一只猪
$farmAnimail->produce('pig');

// 生产一个植物农场
$farmPlant    = $farmProducer->produceFarm('plant');
// 生产水稻
$farmPlant->produce('rice');
