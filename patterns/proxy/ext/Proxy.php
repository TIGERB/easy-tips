<?php

namespace proxy\ext;

class Proxy implements Subject,IProxy {

    private $subject = null;

    /**
     * Proxy constructor.
     */
    public function __construct(Subject $_subject) {
        $this->subject = $_subject;
    }

    public function doSomething() {
        $this->subject->doSomething();
        $this->extension();
    }

    public function extension()
    {
        echo "实现一个扩展\n";
    }


}