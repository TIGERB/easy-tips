<?php
namespace memento;

class Editor
{
  /**
   * 编辑器内容
   * @var string
   */
  private $_content = '';

  /**
   * 备忘录实例
   * @var Memento
   */
  private $_memento;

  /**
   * 构造函数
   *
   * @param string $content 打开的文件内容
   */
  function __construct($content='')
  {
    $this->_content = $content;
    // 打印初始内容
    $this->read();

    // 初始化备忘录插件
    $this->_memento = new Memento();
    // 第一次打开编辑器自动保存一次以提供重置状态操作
    $this->save($content);
  }

  /**
   * 写入内容
   *
   * @param  string $value 文本
   * @return boolean
   */
  function write($value='')
  {
    $this->_content .= $value;
    $this->read();
  }

  /**
   * 读取当前内容
   *
   * @param  string $value 文本
   * @return boolean
   */
  function read()
  {
    echo $this->_content? $this->_content . "\n": "空文本" . "\n";
  }


  /**
   * 保存内容
   *
   * @return boolean
   */
  function save()
  {
    $this->_memento->add(clone $this);
  }

  /**
   * 后退
   *
   * @return boolean
   */
  function undo()
  {
    // 获取上个状态
    $undo = $this->_memento->undo();
    // 重置当前状态为上个状态
    $this->_content = $undo->_content;
  }

  /**
   * 复原
   *
   * @return boolean
   */
  function redo()
  {
    // 获取开始状态
    $undo = $this->_memento->redo();
    // 重置当前状态为开始状态
    $this->_content = $undo->_content;
  }
}
