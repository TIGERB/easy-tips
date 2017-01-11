<?php
namespace facade;

/**
 * 外观类
 */
class AnimalMaker
{
  /**
   * 鸡实工厂例
   * @var object
   */
  private $_chicken;

  /**
   * 猪实工厂例
   * @var object
   */
  private $_pig;

  /**
   * 构造函数
   *
   * @return void
   */
  public function __construct()
  {
    $this->_chicken = new Chicken();
    $this->_pig     = new Pig();
  }

  /**
   * 生产方法
   *
   * 生产鸡
   * @return string
   */
  public function produceChicken()
  {
    $this->_chicken->produce();
  }

  /**
   * 生产方法
   *
   * 生产猪
   * @return string
   */
  public function producePig()
  {
    $this->_pig->produce();
  }
}
