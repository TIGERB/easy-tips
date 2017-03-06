<?php
/**
 * 魔术方法性能探索
 *
 * 读取私有属性__get
 *
 * @author TIGERB <https://github.com/TIGERB>
 */

require('./function.php');
if (!isset($argv[1])) {
    die('error: variable is_use_magic is empty');
}
$is_use_magic = $argv[1];

/**
 * 实现公共方法获取私有属性
 */
class ClassOne
{
    /**
     * 私有属性
     *
     * @var string
     */
    private $someVariable = 'private';

    public function __construct()
    {
        # code...
    }

    public function getSomeVariable()
    {
        return $this->someVariable;
    }
}

/**
 * 使用_get获取私有属性
 */
class ClassTwo
{
    /**
     * 私有属性
     *
     * @var string
     */
    private $someVariable = 'private';

    public function __construct()
    {
        # code...
    }

    public function __get($name = '')
    {
        return $this->$name;
    }
}

$a = getmicrotime();
if ($is_use_magic === 'no_magic') {
    $instance = new ClassOne();
    $instance->getSomeVariable();
}else {
    $instance = new ClassTwo();
    $instance->someVariable;
}
$b = getmicrotime();

echo  ($b-$a) . "\n";
