<?php

namespace decorator;

/**
 * Created by PhpStorm.
 * User: zhudong
 * Date: 2017/7/14
 * Time: 下午7:35
 */
class BasicPulisher implements PulisherInterface {

    public function pulishText() {
        echo 'this is the text compnent'.PHP_EOL;
    }

}