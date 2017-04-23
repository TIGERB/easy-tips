<?php
namespace proxy\enforce;

interface Subject {
    public function doSomething();
    public function getProxy();
}