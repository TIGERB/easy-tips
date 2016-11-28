<?php
namespace bridge;

/**
 * 人抽象类
 */
abstract class PersonAbstract
{
  /**
   * 性别
   * @var string
   */
  protected $_gender = '';

  /**
   * 使用的吃饭工具
   * @var string
   */
  protected $_tool   = '';

  /**
   * 构造函数
   *
   * @param string       $gender 性别
   * @param EatInterface $tool   [description]
   */
  public function __construct($gender='',EatInterface $tool)
  {
    $this->_gender = $gender;
    $this->_tool   = $tool;
  }

  /**
   * 吃的行为
   *
   * @param  string $food 实物
   * @return void
   */
  abstract public function eat($food='');
}
