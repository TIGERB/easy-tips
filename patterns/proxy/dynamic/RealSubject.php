<?php
namespace proxy\dynamic;


class RealSubject implements Subject {

    public function doSomething()
    {
        echo "具体的对象处理过程\n";
    }

}