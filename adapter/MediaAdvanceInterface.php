<?php
namespace adapter;

/**
 * 高级媒体接口
 */
interface MediaAdvanceInterface
{
  public function playMp4($file='');
  public function playWma($file='');
}
