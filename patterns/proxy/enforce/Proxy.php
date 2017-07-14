<?php

namespace proxy\enforce;

class Proxy implements Subject {

    private $subject = null;

    /**
     * Proxy constructor.
     */
    public function __construct(Subject $_subject) {
        $this->subject = $_subject;
    }

    public function doSomething() {
        $this->subject->doSomething();
    }

    public function getProxy()
    {
        return $this;
    }


}