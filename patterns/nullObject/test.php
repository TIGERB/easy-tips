<?php
/**
 * 行为型模式
 *
 * php空对象模式
 * 理解：当程序运行过程中出现操作空对象时，程序依然能够通过操作提供的空对象继续执行
 * 使用场景：谨慎使用吧
 *
 * 下面实现老师课堂叫学生回答问题
 *
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

use nullObject\Teacher;
use nullObject\Student;

try {
  //创建一个老师：路飞
  $teacher = new Teacher('路飞');

  // 创建学生
  $mc      = new Student('麦迪');
  $kobe    = new Student('科比');
  $paul    = new Student('保罗');

  // 老师提问
  $teacher->doSomthing($mc);
  $teacher->doSomthing($kobe);
  $teacher->doSomthing($paul);
  $teacher->doSomthing('小李');// 提问了一个班级里不存在人名


} catch (\Exception $e) {
  echo 'error:' . $e->getMessage();
}
