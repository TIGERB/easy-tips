<?php

namespace proxy\dynamic;

interface InvocationHandler
{
    public function invoke($obj, $method, $args);
}