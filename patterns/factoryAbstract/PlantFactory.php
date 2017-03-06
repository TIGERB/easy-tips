<?php
namespace factoryAbstract;

class PlantFactory implements Factory
{
    public function createFarm()
    {
        return new RiceFarm();
    }

    public function createZoo()
    {
        return new PeonyZoo();
    }
}
