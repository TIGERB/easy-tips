<?php
/**
 * 行为型模式
 *
 * php迭代器模式
 * 理解：遍历对象内部的属性，无需对外暴露内部的构成
 * 下面我们来实现一个迭代器访问学校所有的老师
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

use iterator\SchoolExperimental;

try {
  // 初始化一个实验小学
  $experimental = new SchoolExperimental();
  // 添加老师
  $experimental->addTeacher('Griffin');
  $experimental->addTeacher('Curry');
  $experimental->addTeacher('Mc');
  $experimental->addTeacher('Kobe');
  $experimental->addTeacher('Rose');
  $experimental->addTeacher('Kd');
  // 获取教师迭代器
  $iterator = $experimental->getIterator();
  // 打印所有老师
  do {
    $iterator->current();
  } while ($iterator->hasNext());

} catch (\Exception $e) {
  echo 'error:' . $e->getMessage();
}
