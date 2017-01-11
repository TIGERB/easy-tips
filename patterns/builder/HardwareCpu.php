<?php
namespace builder;

/**
 * 处理器实体
 */
class HardwareCpu implements Hardware
{
  public function produce($quantity=8)
  {
    echo "cpu核心数：" . $quantity . "核\n";
  }
}
