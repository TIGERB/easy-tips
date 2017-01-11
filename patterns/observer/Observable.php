<?php
namespace observer;

/**
 * 被观察者实体类
 *
 * 实现附加观察者，删除观察者，通知观察者方法
 */
class Observable implements ObservableInterface
{
  /**
   * 观察者们
   * @var array
   */
  private $_observers = [];

  /**
   * 被观察者名称
   * @var string
   */
  private $_name = '【被观察者:香菇】';

  /**
   * 魔术方法 __get
   * @param  string $name 属性名称
   * @return mixed       
   */
  public function __get($name='')
  {
    return $this->$name;
  }

  /**
   * 附加观察者
   * @return void
   */
  public function attach(ObserverInterface $observer)
  {
    if (!in_array($observer, $this->_observers, true)) {
      $this->_observers[] = $observer;
    }
  }

  /**
   * 解除观察者
   * @return void
   */
  public function detach(ObserverInterface $observer)
  {
    foreach ($this->_observers as $k => $v) {
      if ($v === $observer) {
        unset($this->_observers[$k]);
      }
    }
  }

  /**
   * 通知观察者
   * @return void
   */
  public function notify()
  {
    foreach ($this->_observers as $v) {
      $v->doSomething($this);
    }
  }
}
