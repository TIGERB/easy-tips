<?php
namespace spl;

/**
 * 被观察者实体类
 *
 * 实现附加观察者，删除观察者，通知观察者方法
 */
class Observable implements \SplSubject
{
    /**
     * 观察者们
     * @var array
     */
    private $_observers = [];

    private $_message = "通知消息";

    /**
     * 附加观察者
     * @param \SplObserver $observer
     * @return void
     */
    public function attach(\SplObserver $observer)
    {
        if (!in_array($observer, $this->_observers, true)) {
            $this->_observers[] = $observer;
        }
    }

    /**
     * 解除观察者
     * @param \SplObserver $observer
     * @return void
     */
    public function detach(\SplObserver $observer)
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
        foreach ($this->_observers as $observer) {
            $observer->update($this);
        }
    }


    /**
     * 获取消息
     * @return string
     */
    public function getMessage()
    {
       return $this->_message;
    }

    public function setMessage($msg)
    {
        $this->_message = $msg;
    }
}

