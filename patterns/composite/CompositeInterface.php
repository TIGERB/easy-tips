<?php
namespace composite;

/**
 * 根节点和树节点都要实现的接口
 */
interface CompositeInterface
{
  /**
   * 增加一个节点对象
   *
   * @return mixed
   */
  public function add(CompositeInterface $composite);

  /**
   * 删除节点一个对象
   *
   * @return mixed
   */
  public function delete(CompositeInterface $composite);

  /**
   * 实体类要实现的方法
   *
   * @return mixed
   */
  public function operation();

  /**
   * 打印对象组合
   *
   * @return mixed
   */
  public function printComposite();
}
