<?php
namespace template;

use Exception;

/**
 * 某厂商two
 */
class SmsCompanyTwo extends Sms
{

  /**
   * 初始化运营商配置
   *
   * 每个厂商的配置不一定相同，所以子类复写这个方法即可
   *
   * @param  array $config 运营商配置
   * @return void
   */
  function initialize($config=[])
  {
    // 实现具体算法
    $this->_config = $config;
  }

  /**
   * 厂商发送短信方法
   *
   * 每个厂商复写这个方法即可
   *
   * @param  integer $mobile 手机号
   * @return void
   */
  function sendSms($mobile=0)
  {
    // 实现具体的短信发送方法
    echo "厂商‘two’给手机号{$mobile}发送了短信：{$this->_text} \n";
  }

}
