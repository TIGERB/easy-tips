<?php
namespace prototype;

/**
 * 原型实体
 */
class Prototype extends PrototypeAbstract
{ 
  /**
   * 构造函数
   *
   * @param  string $name 属性一
   * @return void
   */
  public function __constuct($name='')
  {
    $this->_name = $name;
  }

  /**
   * 魔术方法 设置属性
   *
   * @param string $name  属性名称
   * @param string $value 属性值
   */
  public function __set($name='', $value='')
  {
    $this->$name = $value;
  }

  /**
   * 打印对象名称
   *
   * @return sting
   */
  public function getName()
  {
    echo '我是对象' . $this->_name . " \n\n";
  }

  /**
   * 获取原型对象
   *
   * @return object
   */
  public function getPrototype()
  {
     return clone $this;
  }
}
