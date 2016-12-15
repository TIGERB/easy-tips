<?php
namespace prototype;

/**
 * 原型接口
 */
abstract class PrototypeAbstract
{
  /**
   * 名称
   * @var string
   */
  private $_name;
  
  /**
   * 打印对象名称
   *
   * @return sting
   */
  abstract public function getName();

  /**
   * 获取原型对象
   *
   * @return object
   */
  abstract public function getPrototype();
}
