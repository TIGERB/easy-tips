<?php
namespace composite;

/**
 * 文件夹实体
 */
class Folder implements CompositeInterface
{
  /**
   * 对象组合
   * @var array
   */
  private $_composite = [];

  /**
   * 文件夹名称
   * @var string
   */
  private $_name = '';

  /**
   * 构造函数
   *
   * @param string $name
   */
  public function __construct($name='')
  {
    $this->_name = $name;
  }

  /**
   * 魔法函数
   * @param  string $name  属性名称
   * @return mixed
   */
  public function __get($name='')
  {
    $name = '_' . $name;
    return $this->$name;
  }

  /**
   * 增加一个节点对象
   *
   * @return void
   */
  public function add(CompositeInterface $composite)
  {
    if (in_array($composite, $this->_composite, true)) {
      return;
    }
    $this->_composite[] = $composite;
  }

  /**
   * 删除节点一个对象
   *
   * @return void
   */
  public function delete(CompositeInterface $composite)
  {
    $key = array_search($composite, $this->_composite, true);
    if (!$key) {
      throw new Exception("not found", 404);
    }
    unset($this->_composite[$key]);
    $this->_composite = array_values($this->_composite);
  }

  /**
   * 打印对象组合
   *
   * @return void
   */
  public function printComposite()
  {
    foreach ($this->_composite as $v) {
      if ($v instanceof Folder) {
        echo '---' . $v->name . "---\n";
        $v->printComposite();
        continue;
      }
      echo $v->name . "\n";
    }
  }

  /**
   * 实体类要实现的方法
   *
   * @return mixed
   */
  public function operation()
  {
    return;
  }
}
