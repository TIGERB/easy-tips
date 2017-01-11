<?php
namespace builder;

/**
 * 内存实体
 */
class HardwareRam implements Hardware
{
  public function produce($size=6)
  {
    echo "内存大小：" . $size . "G\n";
  }
}
