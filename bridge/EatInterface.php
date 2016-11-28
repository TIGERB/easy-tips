<?php
namespace bridge;

/**
 * 吃接口
 */
interface EatInterface
{
  /**
   * 吃
   *
   * @param  string $food 食物
   * @return mixed
   */
  public function eat($food='');
}
