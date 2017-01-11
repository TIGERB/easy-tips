<?php
namespace visitor;

/**
 * 访问者实体
 *
 * 亚洲
 */
class VisitorAsia implements VisitorInterface
{
  /**
   * 行为吃
   * 
   * @return void
   */
  public function eat()
  {
    echo "身处亚洲，所以主要吃大米咯~ \n";
  }
}
