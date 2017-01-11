<?php
namespace filter;

/**
 * 按性别过滤实体
 */
class FilterGender implements FilterInterface
{
  /**
   * 按照本性别过滤
   * @var string
   */
  private $_gender = '';

  /**
   * 构造函数
   * @param string $gender
   */
  public function __construct($gender='')
  {
    $this->_gender = $gender;
  }

  /**
   * 过滤方法
   *
   * @param  array $persons 运动员集合
   * @return mixed
   */
  public function filter(array $persons)
  {
    foreach ($persons as $k => $v) {
      if ($v->gender === $this->_gender) {
        $personsFilter[] = $persons[$k];
      }
    }
    return $personsFilter;
  }
}
