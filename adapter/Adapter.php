<?php
namespace adapter;

/**
 * 高级播放器适配器
 */
class Adapter
{
  private $_advancePlayerInstance;

  private $_type = '';

  public function __construct($type='')
  {
    switch ($type) {
      case 'mp4':
        $this->_advancePlayerInstance = new AdvanceMp4Player();
        break;
      case 'wma':
        $this->_advancePlayerInstance = new AdvanceWmaPlayer();
        break;

      default:
        throw new Exception("$type is not supported", 400);
        break;
    }
    $this->_type = $type;
  }

  public function play($file='')
  {
    switch ($this->_type) {
      case 'mp4':
        $this->_advancePlayerInstance->playMp4($file);
        break;
      case 'wma':
        $this->_advancePlayerInstance->playWma($file);
        break;
      default:
        break;
    }
  }
}
