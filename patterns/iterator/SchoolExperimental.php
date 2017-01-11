<?php
namespace iterator;

/**
 * 实验小学实体
 */
class SchoolExperimental implements School
{
  /**
   * 老师集合
   * @var
   */
  private $_teachers = [];

  /**
   * 魔法方法
   *
   * @param  string $name 属性名称
   * @return mixed
   */
  public function __get($name='')
  {
    $name = '_' . $name;
    return $this->$name;
  }

  /**
   * 添加老师
   * @param string $name
   */
  public function addTeacher($name='')
  {
    $this->_teachers[] = $name;
  }

  /**
   * 获取教师迭代器
   *
   * @return mixed
   */
  public function getIterator()
  {
    return new TeacherIterator($this);
  }
}
