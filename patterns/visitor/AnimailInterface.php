<?php
namespace visitor;

/**
 * 动物接口
 */
interface AnimailInterface
{
  /**
   * 行为吃
   * 
   * @param  VisitorInterface $visitor 访问者
   * @return void
   */
  public function eat(VisitorInterface $visitor);
}
