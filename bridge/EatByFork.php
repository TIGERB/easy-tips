<?php
namespace bridge;

/**
 * 用叉子吃实体
 */
class EatByFork implements EatInterface
{
  /**
   * 吃
   *
   * @param  string $food 食物
   * @return string
   */
  public function eat($food='')
  {
    echo "用叉子吃{$food}~";
  }
}
