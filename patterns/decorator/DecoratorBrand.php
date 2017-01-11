<?php
namespace decorator;

/**
 * 贴标装饰器
 */
class DecoratorBrand extends Decorator
{

  private $_brand;

  /**
   * 构造函数
   */
  public function __construct(ShoesInterface $phone)
  {
    $this->phone = $phone;
  }

  public function __set($name='', $value='')
  {
    $this->$name = $value;
  }

  /**
   * 生产
   */
  public function product()
  {
    $this->phone->product();
    $this->decorate($this->_brand);
  }

  /**
   * 贴标操作
   */
  public function decorate($value='')
  {
    echo "贴上{$value}标志 \n";
  }

  }
