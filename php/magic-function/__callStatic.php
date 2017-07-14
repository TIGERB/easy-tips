<?php
/**
 * 魔术方法性能探索
 *
 * 静态重载函数
 *
 * @author TIGERB <https://github.com/TIGERB>
 */

require('./function.php');
if (!isset($argv[1])) {
    die('error: variable is_use_magic is empty');
}
$is_use_magic = $argv[1];

/**
 * 存在test静态方法
 */
class ClassOne
{
    public function __construct()
    {
        # code...
    }

    public static function test()
    {
        # code...
    }
}

/**
 * 使用重载实现test
 */
class ClassTwo
{
    public function __construct()
    {
        # code...
    }

    public static function __callStatic($method, $argus)
    {
        # code...
    }
}

$a = getmicrotime();
if ($is_use_magic === 'no_magic') {
    ClassOne::test();
}else {
    ClassTwo::test();
}
$b = getmicrotime();

echo  ($b-$a) . "\n";
