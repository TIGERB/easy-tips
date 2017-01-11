<?php
namespace proxy;

/**
 * 运动鞋实体
 */
class ShoesSport implements ShoesInterface
{
  public function product()
  {
    echo "生产一双球鞋";
  }
}
