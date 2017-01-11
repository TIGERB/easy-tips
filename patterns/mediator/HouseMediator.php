<?php
namespace mediator;

/**
 * 房屋中介
 */
class HouseMediator
{
  /**
   * 提供租房服务
   *
   * @param  Person $person 租客
   * @return Person
   */
  function rentHouse(Person $person)
  {
    // 初始化一个房东
    $landlord = new Landlord('小梅');
    // 租房子
    echo '通过房屋中介，' . $landlord->doSomthing($person);
  }
}
