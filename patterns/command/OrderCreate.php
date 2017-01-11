<?php
namespace command;

/**
 * 创建文本命令
 */
class OrderCreate implements Order
{
  /**
   * 文本类实体
   * @var object
   */
  private $_text;

  /**
   * 命令参数
   * @var array
   */
  private $_arguments = [
    'filename' => ''
  ];

  /**
   * 构造函数
   *
   * @param Text $text
   * @param array $arguments
   */
  public function __construct(Text $text, $arguments=[])
  {
    $this->_text      = $text;
    $this->_arguments = $arguments;
  }

  /**
   * 执行命令
   *
   * @return void
   */
  public function execute()
  {
    $this->_text->create($this->_arguments['filename']);
  }
}
