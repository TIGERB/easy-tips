<?php
namespace visitor;

/**
 * 实体人
 *
 * 人吃饭的行为是不变的,但是吃什么是依照环境而定的
 */
class Person implements AnimailInterface
{
  /**
   * 行为吃
   * 具体吃什么依照访问者而定
   * 
   * @param  VisitorInterface $visitor 访问者
   * @return void
   */
  public function eat(VisitorInterface $visitor)
  {
    $visitor->eat();
  }
}
