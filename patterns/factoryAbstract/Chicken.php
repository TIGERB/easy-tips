<?php
namespace factoryAbstract;

/**
 * 实体鸡
 *
 */
class Chicken implements AnimalInterface
{
  /**
   * 构造函数
   */
  public function __construct()
  {
    echo "生产了一只鸡~ \n\n";
  }
}
