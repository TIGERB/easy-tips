<?php
namespace observer;

/**
 * 被观察者接口
 *
 * 需要实现附加观察者，删除观察者，通知观察者方法
 */
interface ObservableInterface
{
  /**
   * 附加观察者
   * @return void
   */
  public function attach(ObserverInterface $observer);

  /**
   * 解除观察者
   * @return void
   */
  public function detach(ObserverInterface $observer);

  /**
   * 通知观察者
   * @return void
   */
  public function notify();
}
