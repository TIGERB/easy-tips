<?php
namespace command;

/**
 * 命令接口
 */
interface Order
{
  /**
   * 执行命令
   * @return void
   */
  public function execute();
}
