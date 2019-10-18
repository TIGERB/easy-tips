<?php

$liuChaoFan = new DemoPeople;
$liuChaoFan->state = 'tired';
$liuChaoFan->doSomething();

interface BehaviorInterface
{
    public function do();
}



class DemoEat implements BehaviorInterface 
{
    public function do()
    {
        // 怎么怎么样...如果怎么怎么样...怎么怎么样...
        echo "eating \n";
    }
}

class DemoDrink implements BehaviorInterface 
{
    public function do()
    {
        // 怎么怎么样...如果怎么怎么样...怎么怎么样...
        echo "drinking \n";
    }
}

class DemoSleep implements BehaviorInterface 
{
    public function do()
    {
        // 怎么怎么样...如果怎么怎么样...怎么怎么样...
        echo "sleeping \n";
    }
}

class DemoPeople
{
    // 状态
    private $state = '';

    public function __set($key = '', $val = '')
    {
        $this->$key = $val;
    }

    public function doSomething()
    {
        // 怎么怎么样...如果怎么怎么样...怎么怎么样...
        $this->makeDecision()->do();
        // 怎么怎么样...如果怎么怎么样...怎么怎么样...
    }

    /**
     * 决策
     */
    private function makeDecision()
    {
        switch ($this->state) {
            case 'hunger':
                return new DemoEat;
                break;
            case 'thirsty':
                return new DemoDrink;
                break;
            case 'tired':
                return new DemoSleep;
                break;
            
            default:
                throw new Exception('not support', 500);
                break;
        }
    }
}