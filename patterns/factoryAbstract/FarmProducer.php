<?php
namespace factoryAbstract;

/**
 * 工厂生成器
 *
 * 生产工厂
 */
class FarmProducer
{
  /**
   * 生产工厂
   *
   * @param  string $type 植物类型
   * @return mixed
   */
  public function produceFarm($type='')
  {
    switch ($type) {
      case 'animal':
        echo "初始化了一个动物农场~ \n";
        return new FarmAnimal();
        break;

      case 'plant':
        echo "初始化了一个植物农场~ \n";
        return new FarmPlant();
        break;

      default:
        echo "该工厂生产器不支持生产该农场~ \n";
        break;
    }
  }
}
