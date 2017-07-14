<?php

namespace decorator;

/**
 * Created by PhpStorm.
 * User: zhudong
 * Date: 2017/7/14
 * Time: 下午7:39
 */
class MusicPublisher extends PulisherDerector {



    public function pulishText() {
        $this->addMusicCompnent();
        parent::pulishText();
    }

    public function addMusicCompnent() {
        echo 'add music compnent'.PHP_EOL;
    }

}