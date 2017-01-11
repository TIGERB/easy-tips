<?php
namespace builder;

/**
 * 摄像头实体
 */
class HardwareCamera implements Hardware
{
  public function produce($pixel=32)
  {
    echo "摄像头像素：" . $pixel . "像素\n";
  }
}
