<?php
namespace strategy;

/**
 * 实体类
 *
 * 依赖外部不同策略的实体类
 */
class Substance
{
  /**
   * 策略实例
   * @var object
   */
  private $_strategy;

  /**
   * 构造函数
   * 初始化策略
   * 
   * @param Strategy $strategy 策略实例
   */
  public function __construct(StrategyInterface $strategy)
  {
    $this->_strategy = $strategy;
  }

  /**
   * 模拟一个操作
   * 
   * @return mixed
   */
  public function someOperation()
  {
    $this->_strategy->doSomething();
  }
}
