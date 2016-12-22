<?php
namespace factoryAbstract;

/**
 * 实体玉米
 *
 */
class Corn implements PlantInterface
{
  /**
   * 构造函数
   */
  public function __construct()
  {
    echo "生产了一些玉米~ \n\n";
  }
}
