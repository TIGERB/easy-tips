<?php
/**
 * 结构型模式
 *
 * php过滤器模式
 * 使用不同的标准来过滤一组对象，说实话没明白该模式的意义，忘你留言补充讲解
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

use filter\SportsPerson;
use filter\FilterSportType;
use filter\FilterGender;

try {
  // 定义一组运动员
  $persons = [];
  $persons[] = new SportsPerson('male', 'basketball');
  $persons[] = new SportsPerson('female', 'basketball');
  $persons[] = new SportsPerson('male', 'football');
  $persons[] = new SportsPerson('female', 'football');
  $persons[] = new SportsPerson('male', 'swim');
  $persons[] = new SportsPerson('female', 'swim');

  // 按过滤男性
  $filterGender = new FilterGender('male');
  var_dump($filterGender->filter($persons));
  // 过滤运动项目篮球
  $filterSportType = new FilterSportType('basketball');
  var_dump($filterSportType->filter($persons));

} catch (\Exception $e) {
  echo $e->getMessage();
}
