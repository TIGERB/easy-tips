<?php
namespace strategy;

/**
 * 观察者实体类示例2
 */
class StrategyExampleTwo implements StrategyInterface
{
  /**
   * 行为
   * @return mixed
   */
  public function doSomething()
  {
    echo "你选择了策略2 \n";
  }
}
