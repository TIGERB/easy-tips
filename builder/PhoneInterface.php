<?php
namespace builder;

/**
 * 手机接口
 */
Interface PhoneInterface
{
  /**
   * 硬件构建
   * @return void
   */
  private function hardware();

  /**
   * 构建软件
   * @return void
   */
  private function software();
}
