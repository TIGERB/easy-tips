<?php
namespace iterator;

/**
 * 迭代器接口
 */
interface Iterator
{
  /**
   * 是否还有下一个
   *
   * @return boolean
   */
  public function hasNext();

  /**
   * 下一个
   *
   * @return object
   */
  public function next();

  /**
   * 当前
   *
   * @return mixed
   */
  public function current();

  /**
   * 当前索引
   *
   * @return mixed
   */
  public function index();
}
