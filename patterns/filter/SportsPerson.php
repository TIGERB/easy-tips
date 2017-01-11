<?php
namespace filter;

/**
 * 按运动项目过滤实体
 */
class SportsPerson
{
  /**
   * 性别
   * @var string
   */
  private $_gender = '';

  /**
   * 按照本运动项目过滤
   * @var string
   */
  private $_sportType = '';

  /**
   * 构造函数
   * @param string $gender
   * @param string $sportType
   */
  public function __construct($gender='', $sportType='')
  {
    $this->_gender = $gender;
    $this->_sportType = $sportType;
  }

  /**
   * 魔法函数
   * @param  string $value
   * @return mixed
   */
  public function __get($value='')
  {
    $value = '_' . $value;
    return $this->$value;
  }

}
