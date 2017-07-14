<?php
/**
 * php算法实战.
 *
 * 排序算法-插入排序
 *
 * @author TIGERB <https://github.com/TIGERB>
 */

 /**
  * 插入排序.
  *
  * @param  array   $value 待排序数组
  * @param  integer $point 起始位置
  *
  * @return array
  */
  function insert(&$value=[], $point=0)
  {
    if ($point >= count($value) - 1) {
      return;
    }
    $next  = $value[$point + 1]; // 下一个待插入值
    // 从后向前遍历已排序数组
    for ($i=$point; $i >= 0; --$i) {
      // 如果当前已排序值大于 待插入值
      // 把当前值后往后移动一位
      // 继续向前遍历
      if ($value[$i] > $next) {
        $value[$i+1] = $value[$i];
        // 如果到开头，自动到插入头位
        if ($i === 0) {
          $value[$i] = $next;
          break;
        }
        continue;
      }
      // 如果，当前已排序值小于或等于 待插入值
      // 则，在当前值后插入 当前待插入值
      // 特殊：如果末尾值小于或等于待插入值 则当前值后插入本身
      $value[$i+1] = $next;
      break;
    }
    $point += 1;// 已排序末尾位置

    // 递归
    insert($value, $point);

    return $value;
  }

  /**
   * 插入排序 for循环版
   *
   * @param  array   $value 待排序数组
   *
   * @return array
   */
  function insert_for($arr=array())
  {
      $len = count($arr);
      for($i = 1; $i < $len; $i++) {
          $base = $arr[$i];
          for($j = $i - 1; $j >= 0; $j--) {
              if ($base < $arr[$j]) {
                  $arr[$j + 1] = $arr[$j];
                  if ($j === 0) {
                      $arr[$j] = $base;
                      break;
                  }
                  continue;
              }
              $arr[$j + 1] = $base;
              break;
          }
      }
      return $arr;
  }
