<?php
namespace observer;

/**
 * 观察者实体类示例2
 */
class ObserverExampleTwo implements ObserverInterface
{
  /**
   * 行为
   * @return mixed
   */
  public function doSomething(ObservableInterface $observable)
  {
    echo $observable->_name . "通知了观察者2 \n";
  }
}
