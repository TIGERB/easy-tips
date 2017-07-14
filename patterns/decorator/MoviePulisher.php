<?php

namespace decorator;

/**
 * Created by PhpStorm.
 * User: zhudong
 * Date: 2017/7/14
 * Time: 下午7:42
 */

class MoviePulisher extends PulisherDerector {

    public function pulishText() {
        $this->addMovieCompnent();
        parent::pulishText();
    }

    public function addMovieCompnent() {
        echo 'add movie compnent'.PHP_EOL;
    }

}