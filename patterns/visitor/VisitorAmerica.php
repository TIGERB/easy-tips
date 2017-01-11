<?php
namespace visitor;

/**
 * 访问者实体
 *
 * 美洲
 */
class VisitorAmerica implements VisitorInterface
{
  /**
   * 行为吃
   * 
   * @return void
   */
  public function eat()
  {
    echo "身处美洲，所以主要吃油炸食物咯~ \n";
  }
}
