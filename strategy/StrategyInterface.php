<?php
namespace strategy;

/**
 * 观察者接口
 */
interface StrategyInterface
{
  /**
   * 行为
   * @return void
   */
  public function doSomething();
}
