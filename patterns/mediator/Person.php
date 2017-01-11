<?php
namespace mediator;

/**
 * 抽象类人
 */
abstract class Person
{
  /**
   * 名字
   * @var string
   */
  private $_name = '';

  /**
   * 构造函数
   */
  function __construct($name)
  {
    $this->_name = $name;
  }

  /**
   * 魔术方法
   * 读取私有属性
   *
   * @param  string $name 属性名称
   * @return mixed
   */
  function __get($name='')
  {
    $name = '_' . $name;
    return $this->$name;
  }

  /**
   * 抽象方法
   *
   * @return mixed
   */
  abstract function doSomthing(Person $person);
}
