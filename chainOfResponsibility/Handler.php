<?php
namespace chainOfResponsibility;

/**
 * handler抽象类
 */
abstract class Handler
{
  /**
   * 下一个hanler对象
   * @var [type]
   */
  private $_nextHandler;

  /**
   * 校验方法
   *
   * @param Request $request 请求对象
   */
  abstract public function Check(Request $request);

  /**
   * 设置责任链上的下一个对象
   *
   * @param Handler $handler
   */
  public function setNext(Handler $handler)
  {
    $this->_nextHandler = $handler;
    return $handler;
  }

  /**
   * 启动
   *
   * @param Handler $handler
   */
  public function start(Request $request)
  {
    $this->check($request);
    // 调用下一个对象
    if (!empty($this->_nextHandler)) {
      $this->_nextHandler->start($request);
    }
  }

}
