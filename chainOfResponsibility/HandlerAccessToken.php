<?php
namespace chainOfResponsibility;

/*
 * handler接口
 */
class HandlerAccessToken extends Handler
{
  /**
   * 校验方法
   *
   * @param Request $request 请求对象
   */
  public function Check(Request $request)
  {
    echo "请求{$request->requestId}: 令牌校验通过～ \n";
  }
}
