<?php
/**
 * php算法实战
 *
 * 排序算法-冒泡排序
 *
 * @author TIGERB <https://github.com/TIGERB>
 */

  /**
   * 冒泡排序
   *
   * @param  array $value 待排序数组
   * @return array
   */
  function bubble($value = [])
  {
      $length = count($value) - 1;
      // 外循环
      for ($j = 0; $j < $length; ++$j) {
          // 内循环
          for ($i = 0; $i < $length; ++$i) {
              // 如果后一个值小于前一个值，则互换位置
              if ($value[$i + 1] < $value[$i]) {
                  $tmp = $value[$i + 1];
                  $value[$i + 1] = $value[$i];
                  $value[$i] = $tmp;
              }
          }
      }

      return $value;
  }

  /**
   * 优化冒泡排序
   *
   * @param  array $value 待排序数组
   * @return array
   */
  function bubble_better($value = [])
  {
    $flag   = true; // 标示 排序未完成
    $length = count($value)-1; // 数组长度
    $index  = $length; // 最后一次交换的索引位置 初始值为最后一位
    while ($flag) {
      $flag = false; // 假设排序已完成
      for ($i=0; $i < $index; $i++) {
        if ($value[$i] > $value[$i+1]) {
          $flag  = true; // 如果还有交换发生 则排序未完成
          $last  = $i; // 记录最后一次发生交换的索引位置
          $tmp   = $value[$i];
          $value[$i] = $value[$i+1];
          $value[$i+1] = $tmp;
        }
      }
      $index = $last;
    }

    return $value;
  }
