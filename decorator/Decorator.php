<?php

namespace decorator;

/**
 * 装饰器抽象类.
 */
abstract class Decorator implements ShoesInterface
{
    /**
   * 产品生产线对象
   */
  protected $shoes;

  /**
   * 构造函数.
   */
  public function __construct(ShoesInterface $shoes)
  {
      $this->shoes = $shoes;
  }

  /**
   * 生产.
   */
  public function product()
  {
      $this->shoes->product();
  }

  /**
   * 装饰操作.
   */
  abstract public function decorate($value);
}
