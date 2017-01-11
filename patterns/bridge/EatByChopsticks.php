<?php
namespace bridge;

/**
 * 用筷子吃实体
 */
class EatByChopsticks implements EatInterface
{
  /**
   * 吃
   *
   * @param  string $food 食物
   * @return string
   */
  public function eat($food='')
  {
    echo "用筷子吃{$food}~";
  }
}
