<?php
namespace nullObject;

/**
 * 老师
 */
class Teacher extends Person
{
  /**
   * 老师提问
   *
   * @return mixed
   */
  function doSomthing($person)
  {
    if (!$person instanceof Student) {
      $person = new NullPerson('');
    }
    $person->doSomthing($this);
  }
}
