<?php
namespace builder;

/**
 * 屏幕实体
 */
class HardwareScreen implements Hardware
{
  public function produce($size='5.0')
  {
    echo "屏幕大小：" . $size . "寸\n";
  }
}
