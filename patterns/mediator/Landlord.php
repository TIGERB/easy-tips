<?php
namespace mediator;

/**
 * 房东
 */
class Landlord extends Person
{
  /**
   * 租出去房子
   *
   * @return mixed
   */
  public function doSomthing(Person $person)
  {
    // 租出去闲置房子
    return "‘{$this->name}’租出去一件闲置房给‘{$person->name}’ ～ \n";
  }
}
