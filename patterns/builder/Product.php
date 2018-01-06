<?php
namespace builder;

use builder\Hardware;
use builder\Software;

/**
 * 产品类
 */
class Product
{
  /**
   * 名称
   * @var array
   */
  private $name = '';

  /**
   * 硬件
   * @var array
   */
  private $hardwares = array();

  /**
   * 软件
   * @var array
   */
  private $softwares = array();

  /**
   * 构造函数
   *
   * @param string $name 名称
   */
  public function __construct($name='')
  {
    // 名称
    $this->_name = $name;
    echo $this->_name . " 配置如下：\n";
  }

  /**
   * 构建硬件
   *
   * @param  Hardware  $hardware 硬件参数
   * @return void
   */
  public function addHardware(Hardware $instance)
  {
    $this->hardwares[] = $instance;
  }

  /**
   * 构建软件
   * 
   * @param  Software  $software 软件参数
   * @return void
   */
  public function addSoftware(Software $instance)
  {
    $this->softwares[] = $instance;
  }
}
