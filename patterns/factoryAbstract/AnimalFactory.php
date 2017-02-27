<?php
namespace factoryAbstract;

class AnimalFactory implements Factory
{
    public function createFarm()
    {
        return new PigFarm();
    }

    public function createZoo()
    {
        return new PandaZoo();
    }
}
