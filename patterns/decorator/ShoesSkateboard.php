<?php
namespace decorator;

/**
 * 滑板鞋实体
 */
class ShoesSkateboard implements ShoesInterface
{
  public function product()
  {
    echo "生产一滑板鞋";
  }
}
