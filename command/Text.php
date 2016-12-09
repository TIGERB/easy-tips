<?php
namespace command;

/**
 * 文本类
 */
class Text
{
  /**
   * 创建
   *
   * @param  string $name 文件名称
   * @return string
   */
  public function create($filename='')
  {
    echo "创建了一个文件: {$filename} \n";
  }

  /**
   * 写入
   *
   * @param  string $content 文件名称
   * @return string
   */
  public function write($filename='', $content='')
  {
    echo "文件{$filename}写入了内容: {$content} \n";
  }

  /**
   * 保存
   *
   * @param  string $filename 文件名称
   * @return string
   */
  public function save($filename='')
  {
    echo "保存了一个文件: {$filename} \n";
  }
}
