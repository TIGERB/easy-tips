<?php
namespace strategy;

/**
 * 观察者实体类示例1
 */
class StrategyExampleOne implements StrategyInterface
{
  /**
   * 行为
   * @return mixed
   */
  public function doSomething()
  {
    echo "你选择了策略1 \n";
  }
}
