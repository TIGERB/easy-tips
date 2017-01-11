<?php
namespace filter;

/**
 * 过滤接口
 */
interface FilterInterface
{
  /**
   * 过滤方法
   *
   * @param  SportsMan $person 运动员
   * @return mixed
   */
  public function filter(array $person);
}
