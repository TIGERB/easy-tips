<?php
namespace state;

/**
 * 农耕接口
 */
class FarmSummer implements Farm
{
  /**
   * 作物名称
   * @var string
   */
  private $_name = '黄瓜';

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
