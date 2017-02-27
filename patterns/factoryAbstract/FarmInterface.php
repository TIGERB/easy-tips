<?php
namespace factoryAbstract;

/**
 * 农场接口
 */
interface FarmInterface extends Income
{
    public function harvest();
}
