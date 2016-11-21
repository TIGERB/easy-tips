<?php
namespace adapter;

/**
 * 音频设备实体
 */
class AudioPlayer implements MediaInterface
{
  public function play($file='', $type='')
  {
    switch ($type) {
      case 'mp3':
        echo 'playing file: ' . $file . ".mp3\n";
        break;
      case 'mp4':
        $adapter = new Adapter($type);
        $adapter->play($file);
        break;
      case 'wma':
        $adapter = new Adapter($type);
        $adapter->play($file);
        break;

      default:
        throw new Exception("$type is not supported", 400);
        break;
    }

  }
}
