<?php
namespace state;

/**
 * 农耕接口
 */
class FarmAutumn implements Farm
{
  /**
   * 作物名称
   * @var string
   */
  private $_name = '白菜';

  /**
   * 种植
   *
   * @return string
   */
  function grow()
  {
    echo "种植了一片 {$this->_name} \n";
  }

  /**
   * 收割
   *
   * @return string
   */
  function harvest()
  {
    echo "收获了一片 {$this->_name} \n";
  }

}
