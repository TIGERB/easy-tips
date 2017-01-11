<?php
namespace factoryAbstract;

/**
 * 实体鸡
 *
 */
class Pig implements AnimalInterface
{
  /**
   * 构造函数
   */
  public function __construct()
  {
    echo "生产了一只猪~ \n\n";
  }
}
