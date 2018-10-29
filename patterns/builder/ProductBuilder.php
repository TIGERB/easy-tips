<?php
namespace builder;

use builder\Product;

/**
 * 产品构建器
 */
class ProductBuilder
{

  /**
   * 参数
   *
   * @var array
   */
  private $params = [
    'name'     => '',
    'hardware' => [],
    'software' => []
  ];

  /**
   * 构造函数
   */
  public function __construct($params = [])
  {
    
  }

  /**
   * mp3
   *
   * @param array $params 参数
   * @return Product Mp3
   */
  public function getMp3($params = [])
  {
    $this->params = $params;
    $mp3 = new Product($this->params['name']);
    $mp3->addHardware(new HardwareCpu($this->params['hardware']['cpu']));
    $mp3->addHardware(new HardwareRam($this->params['hardware']['ram']));
    $mp3->addHardware(new HardwareStorage($this->params['hardware']['storage']));
    $mp3->addSoftware(new SoftwareOs($this->params['software']['os']));
    return $mp3;
  }

  /**
   * phone
   * 
   * @param array $params 参数
   * @return Product Phone
   */
  public function getPhone($params = [])
  {
    $this->params = $params;
    $phone = new Product($this->params['name']);
    $phone->addHardware(new HardwareScreen($this->params['hardware']['screen']));
    $phone->addHardware(new HardwareCamera($this->params['hardware']['camera']));
    $phone->addHardware(new HardwareCpu($this->params['hardware']['cpu']));
    $phone->addHardware(new HardwareRam($this->params['hardware']['ram']));
    $phone->addHardware(new HardwareStorage($this->params['hardware']['storage']));
    $phone->addSoftware(new SoftwareOs($this->params['software']['os']));
    return $phone;
  }
}
