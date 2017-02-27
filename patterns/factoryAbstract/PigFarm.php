<?php
namespace factoryAbstract;

class PigFarm implements FarmInterface {

    public function harvest()
    {
        echo "养殖部门收获猪肉(不清真)\n";
    }

    public function money()
    {
        $this->harvest();
        echo "卖猪肉\n\n";
    }
}
