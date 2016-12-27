<?php
/**
 * php算法实战.
 *
 * 排序算法-快速排序
 *
 * @author TIGERB <https://github.com/TIGERB>
 */

 /**
  * 快速排序.
  *
  * @param  array $value 待排序数组
  * @param  array $left  左边界
  * @param  array $right 右边界
  *
  * @return array
  */
  function quick(&$value, $left, $right)
  {
    // 左右界重合 跳出
    if ($left >= $right) {
      return;
    }
    $base = $left;
    do {
      // 从最右边开始找到第一个比基准小的值，互换位置
      // 找到基准索引为止
      for ($i=$right; $i > $base; --$i) {
        if ($value[$i] < $value[$base]) {
          $tmp = $value[$i];
          $value[$i] = $value[$base];
          $value[$base] = $tmp;
          $base = $i; // 更新基准值索引
          break;
        }
      }

      // 从最左边开始找到第一个比基准大的值，互换位置
      // 找到基准索引为止
      for ($j=$left; $j < $base; ++$j) {
        if ($value[$j] > $value[$base]) {
          $tmp = $value[$j];
          $value[$j] = $value[$base];
          $value[$base] = $tmp;
          $base = $j; // 更新基准值索引
          break;
        }
      }
    } while ($i > $j);// 直到左右索引重合为止

    // 开始递归
    // 以当前索引为分界
    // 开始排序左部分
    quick($value, $left, $i-1);
    // 开始排序右边部分
    quick($value, $i+1, $right);

    return $value;
  }

  /**
   * 快速排序.while版本
   *
   * @param  array $value 待排序数组
   * @param  array $left  左边界
   * @param  array $right 右边界
   *
   * @return array
   */
  function quick_while(&$value, $left, $right)
  {
    // 左右界重合 跳出
    if ($left >= $right) {
      return;
    }

    $point = $left;
    $i = $right;
    $j = $left;
    while ($i > $j) {
      //查右边值
      while ($i > $point) {
        if ($value[$i] < $value[$point]) {
          $tmp = $value[$i];
          $value[$i] = $value[$point];
          $value[$point] = $tmp;
          $point = $i;
          break;
        }
        --$i;
      }

      //查左边值
      while ($j < $point) {
        if ($value[$j] > $value[$point]) {
          $tmp = $value[$j];
          $value[$j] = $value[$point];
          $value[$point] = $tmp;
          $point = $j;
          break;
        }
        ++$j;
      }
    }

    // 开始递归
    // 以当前索引为分界
    // 开始排序左部分
    quick_while($value, $left, $i-1);
    // 开始排序右边部分
    quick_while($value, $i+1, $right);

    return $value;
  }
