<?php
namespace chainOfResponsibility;

/*
 * 请求对象
 */
class Request
{
  /**
   * 请求对象身份标识
   * @var string
   */
  private $_requestId = '';

  /**
   * 魔术方法 设置私有属性
   * @param string $name  属性名称
   * @param string $value 属性值
   */
  public function __set($name='', $value='')
  {
    $name = '_' . $name;
    $this->$name = $value;
  }

  /**
   * 魔术方法 获取私有属性
   * @param string $name  属性名称
   */
  public function __get($name='')
  {
    $name = '_' . $name;
    return $this->$name;
  }
}
