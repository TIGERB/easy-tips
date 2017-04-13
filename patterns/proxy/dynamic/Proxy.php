<?php

namespace proxy\dynamic;

class Proxy {

    private $cls = null;
    private $interface = array();
    private $handler = null;
    private $reflection = null;

    /**
     * Proxy constructor.
     * @param \ReflectionClass $reflection
     * @param InvocationHandler $handler
     */
    private function __construct(\ReflectionClass $reflection, InvocationHandler $handler)
    {
        $this->cls = $reflection->getNamespaceName().$reflection->getName();
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as &$method) {
            $method = $method->getName();
        }
        unset($method);
        $this->interface = $methods;
        $this->handler = $handler;
        $this->reflection = $reflection;
    }


    public static function newProxyInstance($object, $handler) {
        return new self(new \ReflectionObject($object), $handler);
    }

    public static function newProxyClass($class, $handler) {
        return new self(new \ReflectionClass($class), $handler);
    }

    function __call($name, $arguments) {
        if (in_array($name, $this->interface)) {
            $this->handler->invoke($this, $name, $arguments);
        }
    }


}