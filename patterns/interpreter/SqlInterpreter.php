<?php
namespace interpreter;

use Exception;

/**
 * Sql解释器
 */
class SqlInterpreter
{
  /**
   * 表名
   * @var string
   */
  private $_tableName = '';

  /**
   * 当前类的实例
   * @var object
   */
  private static $_instance;

  /**
   * 设置表名
   *
   * @param string $table 表名
   */
  public static function db($tableName='')
  {
    if (empty($tableName)) {
      throw new Exception("argument tableName is null", 400);
    }
    // 单例
    if(!self::$_instance instanceof self){// instanceof运算符的优先级高于！
      self::$_instance = new self();
    }
    // 更新实例表名
    self::$_instance->_setTableName($tableName);
    // 返回实例
    return self::$_instance;
  }

  /**
   * 设置表名
   *
   * @param string $tableName 表名
   */
  private function _setTableName($tableName='')
  {
    $this->_tableName = $tableName;
  }

  /**
   *  插入一条数据
   *
   * @param  array $data 数据
   * @return mixed
   */
  public function insert($data=[])
  {
    if (empty($data)) {
      throw new Exception("argument data is null", 400);
    }
    $count = count($data);
    //拼接字段
    $field = array_keys($data);
    $fieldString = '';
    foreach ($field as $k => $v) {
      if ($k === (int)($count - 1)) {
        $fieldString .= "`{$v}`";
        continue;
      }
      $fieldString .= "`{$v}`".',';
    }
    unset($k);
    unset($v);

    //拼接值
    $value = array_values($data);
    $valueString = '';
    foreach ($value as $k => $v) {
      if ($k === (int)($count - 1)) {
        $valueString .= "'{$v}'";
        continue;
      }
      $valueString .= "'{$v}'".',';
    }
    unset($k);
    unset($v);

    $sql = "INSERT INTO `{$this->_tableName}` ({$fieldString}) VALUES ({$valueString})";

    echo $sql . "\n";
  }

  /**
   *  删除数据
   *
   * @param  array $data 数据
   * @return mixed
   */
  public function delete($data=[])
  {
    if (empty($data)) {
      throw new Exception("argument data is null", 400);
    }
    // 拼接where语句
    $count = (int)count($data);
    $where = '';
    $dataCopy = $data;
    $pop = array_pop($dataCopy);
    if ($count === 1) {
      $field = array_keys($data)[0];
      $value = array_values($data)[0];
      $where = "`{$field}` = '{$value}'";
    }else{
      foreach ($data as $k => $v) {
        if ($v === $pop) {
          $where .= "`{$k}` = '{$v}'";
          continue;
        }
        $where .= "`{$k}` = '{$v}' AND ";
      }
    }

    $sql = "DELETE FROM `{$this->_tableName}` WHERE {$where}";

    echo $sql . "\n";
  }

  /**
   *  更新一条数据
   *
   * @param  array $data 数据
   * @return mixed
   */
  public function update($data=[])
  {
    if (empty($data)) {
      throw new Exception("argument data is null", 400);
    }
    if (empty($data['id'])) {
      throw new Exception("argument data['id'] is null", 400);
    }
    $set = '';
    $dataCopy = $data;
    $pop = array_pop($dataCopy);
    foreach ($data as $k => $v) {
      if ($v === $pop) {
        $set .= "`{$k}` = '$v'";
        continue;
      }
      $set .= "`{$k}` = '$v',";
    }

    $sql = "UPDATE `{$this->_tableName}` SET {$set}";

    echo $sql . "\n";
  }

  /**
   *  查找一条数据
   *
   * @param  array $data 数据
   * @return mixed
   */
  public function find($data=[])
  {
    if (empty($data)) {
      throw new Exception("argument data is null", 400);
    }

    // 拼接where语句
    $count = (int)count($data);
    $where = '';
    $dataCopy = $data;
    $pop = array_pop($dataCopy);
    if ($count === 1) {
      $field = array_keys($data)[0];
      $value = array_values($data)[0];
      $where = "`{$field}` = '{$value}'";
    }else{
      foreach ($data as $k => $v) {
        if ($v === $pop) {
          $where .= "`{$k}` = '{$v}'";
          continue;
        }
        $where .= "`{$k}` = '{$v}' AND ";
      }
    }

    $sql = "SELECT * FROM `{$this->_tableName}` WHERE {$where}";

    echo $sql . "\n";
  }
}
