<?php
/**
 * 行为型模式
 *
 * php命令模式
 * 命令模式:就是在依赖的类中间加一个命令类，本来可以直接调用的类方法现在通过命令来调用，已达到
 * 解耦的的目的，其次可以实现undo，redo等操作，因为你知道调了哪些命令
 *
 * 下面我们来用命令模式实现一个记事本，涉及的命令：
 * - 新建
 * - 写入
 * - 保存
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

use command\Text;
use command\OrderCreate;
use command\OrderWrite;
use command\OrderSave;
use command\Console;

try {
  // 创建一个记事本实例
  $text   = new Text();

  // 创建命令
  $create = new OrderCreate($text, [
    'filename' => 'test.txt'
  ]);
  // 写入命令
  $write  = new OrderWrite($text, [
    'filename' => 'test.txt',
    'content'  => 'life is a struggle'
  ]);
  // 保存命令
  $save   = new OrderSave($text, [
    'filename' => 'text.txt'
  ]);

  // 创建一个控制台
  $console = new Console();
  // 添加命令
  $console->add($create);
  $console->add($write);
  $console->add($save);
  // 运行命令
  $console->run();

} catch (\Exception $e) {
  echo $e->getMessage();
}
