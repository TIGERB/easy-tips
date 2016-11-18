<?php
namespace builder;

/**
 * 储存实体
 */
class HardwareStorage implements Hardware
{
  public function produce($size=32)
  {
    echo "储存大小：" . $size . "G\n";
  }
}
