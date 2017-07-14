<?php

namespace proxy\common;

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

}