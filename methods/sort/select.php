<?php
/**
 * php算法实战.
 *
 * 排序算法-选择排序
 *
 * @author TIGERB <https://github.com/TIGERB>
 */

 /**
  * 选择排序.
  *
  * @param  array $value 待排序数组
  *
  * @return array
  */
  function select_sort(&$value=[])
  {
    $length = count($value)-1;
    for ($i=0; $i < $length; $i++) {
      $point = $i;// 最小值索引
      for ($j=$i+1; $j <= $length; $j++) {
        if ($value[$point] > $value[$j]) {
          $point = $j;
        }
      }
      $tmp = $value[$i];
      $value[$i] = $value[$point];
      $value[$point] = $tmp;
    }
    return $value;
  }
