<?php
/**
 * 创建型模式
 * 工厂方法模式和抽象工厂模式的核心区别
 * 工厂方法模式利用继承，抽象工厂模式利用组合
 * 工厂方法模式产生一个对象，抽象工厂模式产生一族对象
 * 工厂方法模式利用子类创造对象，抽象工厂模式利用接口的实现创造对象
 * 工厂方法模式可以退化为简单工厂模式(非23中GOF)
 *
 * php工厂模式
 * @author  TIGERB <https://github.com/TIGERB>
 * @author  jealone <https://github.com/jealone>
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
use factory\Zoo;
use factory\SampleFactory;

// 初始化一个工厂
$farm = new Farm();

// 生产一只鸡
$farm->produce('chicken');
// 生产一只猪
$farm->produce('pig');

// 初始化一个动物园工厂
$zoo = new Zoo();
$zoo->produce("chicken");
$zoo->produce("pig");

// 工厂方法模式退化为简单工厂模式
SampleFactory::produce("chicken");
SampleFactory::produce("pig");
