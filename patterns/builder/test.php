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

use builder\PhoneBuilder;
use builder\Mp3Builder;

// 创建一个PhoneBuilder生产一款旗舰android手机
$builder = new PhoneBuilder('某米8s', [
    'screen'  => '5.0',
    'cpu'     => 16,
    'ram'     => 8,
    'storage' => 64,
    'camera'  => '2000w'
  ],['os' => 'android 6.0']);

echo "\n";
echo "----------------\n";
echo "\n";

// 创建一个Mp3Builder生产一款mp3
$builder = new Mp3Builder('某族MP3', [
    'cpu'     => 1,
    'ram'     => 1,
    'storage' => 128,
  ],['os' => 'mp3 os']);
