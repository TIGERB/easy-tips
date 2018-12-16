<?php
/**
 * php算法实战
 * php algorithm's practice
 * 
 * 排序算法-冒泡排序
 * sort algorithm - bubble sort algorithm
 * 
 * @author TIGERB <https://github.com/TIGERB>
 */

  /**
   * 冒泡排序
   * bubble sort algorithm
   * 
   * @param  array $value 待排序数组 the array that is waiting for sorting
   * @return array
   */
  function bubble($value = [])
  {
      $length = count($value) - 1;
      // 外循环
      // outside loop
      for ($j = 0; $j < $length; ++$j) {
          // 内循环
          // inside loop
          for ($i = 0; $i < $length; ++$i) {
              // 如果后一个值小于前一个值，则互换位置
              // if the next value is less than the current value, exchange each other.
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
   * optimized bubble sort algorithm
   * 
   * @param  array $value 待排序数组 the array that is waiting for sorting
   * @return array
   */
  function bubble_better($value = [])
  {
    $flag   = true; // 标示 排序未完成 the flag about the sorting is whether or not finished.
    $length = count($value)-1; // 数组最后一个元素的索引 the index of the last item about the array.
    $index  = $length; // 最后一次交换的索引位置 初始值为最后一位 the last exchange of index position, default value is equal to the last index.

    while ($flag) {
      $flag = false; // 假设排序已完成 let's suppose the sorting is finished.
      for ($i=0; $i < $index; $i++) {
        if ($value[$i] > $value[$i+1]) {
          $flag  = true; // 如果还有交换发生，则排序未完成  if the exchange still happen, it show that the sorting is not finished. 
          $last  = $i; // 记录最后一次发生交换的索引位置 taking notes the index position of the last exchange.
          $tmp   = $value[$i];
          $value[$i] = $value[$i+1];
          $value[$i+1] = $tmp;
        }
      }
      $index = !$flag ? : $last;
    }

    return $value;
  }
