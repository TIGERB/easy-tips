<?php
namespace factoryAbstract;

/**
 * 实体大米
 *
 */
class Rice implements PlantInterface
{
  /**
   * 构造函数
   */
  public function __construct()
  {
    echo "生产了一些大米~ \n\n";
  }
}
