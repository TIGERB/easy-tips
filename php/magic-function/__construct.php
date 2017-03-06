<?php
/**
 * 魔术方法性能探索
 *
 * 构造函数
 *
 * @author TIGERB <https://github.com/TIGERB>
 */

require('./function.php');
if (!isset($argv[1])) {
    die('error: variable is_use_magic is empty');
}
$is_use_magic = $argv[1];

/**
 * 构造函数使用类名
 */
class ClassOne
{
    public function classOne()
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
}

$a = getmicrotime();
if ($is_use_magic === 'no_magic') {
    new ClassOne();
}else {
    new ClassTwo();
}
$b = getmicrotime();

echo  ($b-$a) . "\n";
