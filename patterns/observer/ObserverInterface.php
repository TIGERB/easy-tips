<?php
namespace observer;

/**
 * 观察者接口
 */
interface ObserverInterface
{
  /**
   * 行为
   * @return void
   */
  public function doSomething(ObservableInterface $observable);
}
