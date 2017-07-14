<?php
namespace proxy\dynamic;

class SubjectIH implements InvocationHandler
{
    private $obj = null;

    /**
     * SubjectIH constructor.
     * @param null $obj
     */
    public function __construct($obj)
    {
        $this->obj = $obj;
    }

    /**
     * @param $proxy
     * @param $method
     * @param $args
     * @return mixed
     */
    public function invoke($proxy, $method, $args)
    {
        return call_user_func_array(array($this->obj, $method), $args);
    }


}