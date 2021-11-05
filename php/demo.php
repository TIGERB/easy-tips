<?php

abstract class AbstractClassDemo {

    // 抽象方法
    abstract public function demoFun();
    
    // 公有方法
    public function publicFun()
    {
        $this->demoFun();
    }
}

class ClassDemo extends AbstractClassDemo {

    public function demoFun()
    {
        var_dump("Demo");
    }
}

(new ClassDemo())->demoFun();