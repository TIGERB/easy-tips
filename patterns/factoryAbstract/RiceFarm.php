<?php
namespace factoryAbstract;

class RiceFarm implements FarmInterface
{
    public function harvest()
    {
        echo "种植部门收获大米\n";
    }

    public function money()
    {
        $this->harvest();
        echo "卖大米\n\n";
    }
}
