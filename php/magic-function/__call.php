<?php
/**
 * 魔术方法性能探索
 *
 * 重载函数
 *
 * @author TIGERB <https://github.com/TIGERB>
 */

require('./function.php');
if (!isset($argv[1])) {
    die('error: variable is_use_magic is empty');
}
$is_use_magic = $argv[1];

/**
 * 
 */
class ClassOne
{
    public function __construct()
    {
        # code...
    }

    public function test()
    {
        # code...
    }
}

/**
 * 构造函数使用魔术函数__construct
 */
class ClassTwo
{
    public function __construct()
    {
        # code...
    }

    public function __call($method, $argus)
    {
        # code...
    }
}

$a = getmicrotime();
if ($is_use_magic === 'no_magic') {
    $instance = new ClassOne();
    $instance->test();
}else {
    $instance = new ClassTwo();
    $instance->test();
}
$b = getmicrotime();

echo  ($b-$a) . "\n";
