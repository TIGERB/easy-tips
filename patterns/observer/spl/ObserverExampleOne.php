<?php
namespace spl;

/**
 * 观察者实体类示例1
 */
class ObserverExampleOne implements \SplObserver
{
    /**
     * 行为
     * @param \SplSubject $observable
     * @return mixed
     */
    public function update(\SplSubject $observable)
    {
        echo $observable->getMessage() . "通知了观察者1 \n";
    }
}
