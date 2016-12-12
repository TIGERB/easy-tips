<?php
namespace iterator;

/**
 * 老师迭代实体
 */
class TeacherIterator implements Iterator
{

  /**
   * 索引值
   * @var integer
   */
  private $_index = 0;

  /**
   * 要迭代的对象
   * @var object
   */
  private $_teachers;

  /**
   * 构造函数
   *
   * @param School $school
   */
  public function __construct(School $school)
  {
    $this->_teachers = $school->teachers;
  }

  /**
   * 是否还有下一个
   *
   * @return boolean
   */
  public function hasNext()
  {
    if ($this->_index < count($this->_teachers)) {
      return true;
    }
    return false;
  }

  /**
   * 下一个
   *
   * @return object
   */
  public function next()
  {
    if (!$this->hasNext()) {
      echo NULL;
      return;
    }
    $index = $this->_index + 1;
    echo $this->_teachers[$index];
  }

  /**
   * 当前
   *
   * @return mixed
   */
  public function current()
  {
    if (!isset($this->_teachers[$this->_index])) {
      echo  NULL;
      return;
    }
    $current = $this->_teachers[$this->_index];
    $this->_index += 1;
    echo $current . "\n";
  }

  /**
   * 当前索引
   *
   * @return integer
   */
  public function index()
  {
    echo $this->_index;
  }
}
