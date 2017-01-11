<?php
namespace nullObject;

/**
 * 学生
 */
class Student extends Person
{
  /**
   * 答题方法
   *
   * @return mixed
   */
  function doSomthing($person)
  {
    echo "老师‘{$person->name}’让学生‘{$this->name}’回答了一道题~ \n";
  }
}
