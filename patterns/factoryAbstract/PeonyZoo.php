<?php
namespace factoryAbstract;
class PeonyZoo implements ZooInterface {

    public function show()
    {
        echo "牡丹园开馆\n";
    }

    public function money()
    {
        $this->show();
        echo "卖门票\n\n";
    }

}
