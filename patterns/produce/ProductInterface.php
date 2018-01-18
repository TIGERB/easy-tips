<?php
namespace builder;

/**
 * 构建器接口
 */
Interface ProductInterface
{
  /**
   * 硬件构建
   * @return void
   */
  public function hardware();

  /**
   * 构建软件
   * @return void
   */
  public function software();
}
