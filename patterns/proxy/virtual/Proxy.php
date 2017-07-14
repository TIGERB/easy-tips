<?php

namespace proxy\virtual;

class Proxy implements Subject {

    private $subject = null;

    public function doSomething() {
        // 虚拟代理就是指需要的时候再初始化对象，不然只有代理没有对象
        if ($this->subject == null)
            $this->subject = new RealSubject();

        $this->subject->doSomething();
    }

}