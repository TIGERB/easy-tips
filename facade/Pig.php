<?php
namespace facade;

/**
 * 实体猪工厂
 */
class Pig implements AnimalInterface
{
  /**
   * 生产猪
   *
   * @return string
   */
  public function produce()
  {
    echo "生产了一只猪~ \n";
  }
}
