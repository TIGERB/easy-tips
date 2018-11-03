<?php
namespace builder;

/**
 * 内存实体
 */
class HardwareRam implements Hardware
{
  public function __construct($size=6)
  {
    echo "内存大小：" . $size . "G\n";
  }
}
