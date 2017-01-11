<?php
namespace adapter;

/**
 * mp4高级播放器实体
 */
class AdvanceMp4Player implements MediaAdvanceInterface
{
  public function playMp4($file='')
  {
      echo 'AdvanceMp4Player driver playing file: ' . $file . ".mp4\n";
  }

  public function playWma($file='')
  {
      //do nothing
  }
}
