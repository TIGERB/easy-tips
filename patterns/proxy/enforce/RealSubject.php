<?php
namespace proxy\enforce;


class RealSubject implements Subject {

    private $proxy = null;

    public function doSomething()
    {
        if ($this->isProxy())
            echo "具体的对象处理过程\n";
        else
            echo "请使用代理访问";
    }

    public function getProxy()
    {
        $this->proxy = new Proxy($this);
        return $this->proxy;

    }

    private function isProxy() {
        return ($this->proxy instanceof Proxy);
    }

}