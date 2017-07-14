<?php

namespace decorator;

/**
 * Created by PhpStorm.
 * User: zhudong
 * Date: 2017/7/14
 * Time: 下午7:36
 */

class PulisherDerector implements PulisherInterface {

    protected $pulisher = null;

    function derect(PulisherInterface $pulisher) {
        $this->pulisher = $pulisher;
    }

    public function pulishText() {
        $this->pulisher->pulishText();
    }

}