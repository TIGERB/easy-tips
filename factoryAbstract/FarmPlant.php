<?php
namespace factoryAbstract;

/**
 * 农场
 *
 * 生产植物
 */
class FarmPlant
{
  /**
   * 生产方法
   *
   * 生产植物
   * @param  string $type 植物类型
   * @return mixed       
   */
  public function produce($type='')
  {
    switch ($type) {
      case 'rice':
        return new Chicken();
        break;

      case 'corn':
        return new Pig();
        break;
      
      default:
        echo "该农场不支持生产该农物~ \n";
        break;
    }
  }
}
