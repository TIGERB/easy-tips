<?php
namespace bridge;

/**
 * 男人实类
 */
class PersonMale extends PersonAbstract
{
  /**
   * 吃的具体方式
   *
   * @param  string $food 食物
   * @return string
   */
  public function eat($food='')
  {
    $this->_tool->eat($food);
  }
}
