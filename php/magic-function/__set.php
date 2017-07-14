<?php
/**
 * 魔术方法性能探索
 *
 * 设置私有属性__set
 *
 * @author TIGERB <https://github.com/TIGERB>
 */

require('./function.php');
if (!isset($argv[1])) {
    die('error: variable is_use_magic is empty');
}
$is_use_magic = $argv[1];

/**
 * 实现公共方法设置私有属性
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

    public function setSomeVariable($value = '')
    {
        $this->someVariable = $value;
    }
}

/**
 * 使用_set设置私有属性
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

    public function __set($name = '', $value = '')
    {
        $this->$name = $value;
    }
}

$a = getmicrotime();
if ($is_use_magic === 'no_magic') {
    $instance = new ClassOne();
    $instance->setSomeVariable('public');
}else {
    $instance = new ClassTwo();
    $instance->someVariable = 'public';
}
$b = getmicrotime();

echo  ($b-$a) . "\n";
