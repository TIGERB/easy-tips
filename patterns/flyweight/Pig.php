<?php
namespace flyweight;

/**
 * 实体鸡
 *
 */
class Pig implements AnimalInterface
{
  /**
   * 类别
   * @var string
   */
  private $_type = '';

  /**
   * 构造函数
   */
  public function __construct()
  {

  }

  /**
   * 类型获取
   * 
   * @return string
   */
  public function getType()
  {
    echo "这是只猪～ \n";
  }
}
