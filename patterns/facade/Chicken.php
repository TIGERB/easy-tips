<?php
namespace facade;

/**
 * 实体鸡工厂
 */
class Chicken implements AnimalInterface
{
  /**
   * 生产鸡
   *
   * @return string
   */
  public function produce()
  {
    echo "生产了一只鸡~ \n";
  }
}
