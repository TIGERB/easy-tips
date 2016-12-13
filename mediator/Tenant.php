<?php
namespace mediator;

/**
 * 租客
 */
class Tenant extends Person
{
  /**
   * 组间房子
   *
   * @return mixed
   */
  public function doSomthing(Person $person)
  {
    // 本来是之间找房东租房,但是茫茫人海错综复杂
    // 下面我们通过一家正规的房屋中介租房
    $houseMediator = new HouseMediator();
    $houseMediator->rentHouse($this);
  }
}
