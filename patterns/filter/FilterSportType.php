<?php
namespace filter;

/**
 * 按运动项目过滤实体
 */
class FilterSportType implements FilterInterface
{
  /**
   * 按照本运动项目过滤
   * @var string
   */
  private $_sportType = '';

  /**
   * 构造函数
   * @param string $sportType
   */
  public function __construct($sportType='')
  {
    $this->_sportType = $sportType;
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
      if ($v->sportType === $this->_sportType) {
        $personsFilter[] = $persons[$k];
      }
    }
    return $personsFilter;
  }
}
