<?php
/**
 * 创建型模式
 *
 * php建造者模式
 * 简单对象构建复杂对象
 * 基本组件不变，但是组件之间的组合方式善变
 *
 * 下面我们来构建手机和mp3
 *
 * // 手机简单由以下构成
 * 手机 => 名称，硬件， 软件
 * // 硬件又由以下硬件构成
 * 硬件 => 屏幕，cpu, 内存， 储存， 摄像头
 * // 软件又由以下构成
 * 软件 => android, ubuntu
 *
 * * // mp3简单由以下构成
 * 手机 => 名称，硬件， 软件
 * // 硬件又由以下硬件构成
 * 硬件 => cpu, 内存， 储存
 * // 软件又由以下构成
 * 软件 => mp3 os
 * 
 * builder 导演类
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

use builder\ProductBuilder;

$builder = new ProductBuilder();

// 生产一款mp3
$builder->getMp3([
  'name' => '某族MP3',
  'hardware' => [
    'cpu'     => 1,
    'ram'     => 1,
    'storage' => 128,
  ],
  'software' => ['os' => 'mp3 os']
]);

echo "\n";
echo "----------------\n";
echo "\n";

// 生产一款手机
$builder->getPhone([
  'name' => '某米8s',
  'hardware' => [
    'screen'  => '5.8',
    'camera'  => '2600w',
    'cpu'     => 4,
    'ram'     => 8,
    'storage' => 128,
  ],
  'software' => ['os' => 'android 6.0']
]);
