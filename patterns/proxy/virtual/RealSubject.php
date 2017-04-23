<?php
namespace proxy\virtual;


class RealSubject implements Subject {


    /**
     * RealSubject constructor.
     */
    public function __construct()
    {
        echo "初始化对象\n";
    }

    public function doSomething()
    {
        echo "具体的对象处理过程\n";
    }

}