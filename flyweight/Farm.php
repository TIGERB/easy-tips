<?php
namespace flyweight;

/**
 * 农场
 *
 * 生产动物
 */
class Farm
{
  /**
   * 对象缓存池
   * @var array
   */
  private $_farmMap = [];

  /**
   * 构造函数
   */
  public function __construct()
  {
    echo "-----------初始化了一个农场----------- \n\n";
  }

  /**
   * 生产方法
   *
   * 生产农物
   *
   * @param  string $type 农场类型
   * @return mixed
   */
  public function produce($type='')
  {
    // 对象缓存池判断
    if (key_exists($type, $this->_farmMap)) {
      echo "来自缓存池-> ";
      return $this->_farmMap[$type];// 返回缓存
    }

    switch ($type) {
      case 'chicken':
        return $this->_farmMap[$type] =  new Chicken();
        break;

      case 'pig':
        return $this->_farmMap[$type] =  new Pig();
        break;

      default:
        echo "该农场不支持生产该农物~ \n";
        break;
    }
  }
}
