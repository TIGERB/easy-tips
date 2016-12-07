<?php
namespace proxy;

use Exception;

/**
 * 代理工厂
 */
class Proxy
{
  /**
   * 产品生产线对象
   */
  private $_shoes;

  /**
   * 产品生产线类型
   */
  private $_shoesType;

  /**
   * 构造函数.
   */
  public function __construct($shoesType)
  {
    $this->_shoesType = $shoesType;
  }

  /**
   * 生产.
   */
  public function product()
  {
      switch ($this->_shoesType) {
        case 'sport':
          echo "我可以偷点工减点料";
          $this->_shoes = new ShoesSport();
          break;
        case 'skateboard':
          echo "我可以偷点工减点料";
          $this->_shoes = new ShoesSkateboard();
          break;

        default:
          throw new Exception("shoes type is not available", 404);
          break;
      }
      $this->_shoes->product();
  }
}
