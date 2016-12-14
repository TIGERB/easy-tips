<?php
/**
 * 结构型模式
 *
 * php桥接模式
 * 基础的结构型设计模式：将抽象和实现解耦,对抽象的实现是实体行为对接口的实现
 * 例如：人 => 抽象为属性：性别 动作：吃 => 人吃的动作抽象为interface => 实现不同的吃法
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

use bridge\PersonMale;
use bridge\EatByChopsticks;
use bridge\EatByFork;

try {
  // 初始化一个用筷子吃饭的男人的实例
  $male = new PersonMale('male', new EatByChopsticks());
  // 吃饭
  $male->eat('大盘鸡');

} catch (\Exception $e) {
  echo $e->getMessage();
}
