<?php
/**
 * php算法实战.
 *
 * 排序算法-希尔排序
 *
 * 按照增量分组插入排序
 *
 * @author TIGERB <https://github.com/TIGERB>
 */

/**
 * 希尔排序.
 *
 * 算法思路：
 * 给定一个初始步长，一般为序列长度的一半
 * 按步长分组
 * 每组进行插入排序
 * 取当前步长的一半为下个步长，继续上面的算法
 * 直到步长为1结束
 *
 * @param array $value 待排序数组
 * @param array $increment 步长 初始数组长度向下取整
 *
 * @return array
 */
function shell(&$value = [], $increment)
{
  // 步长小于1为止
  if ($increment<1) {
    return;
  }
  // 分组插入排序
  // 循环步长次
  $a = 0;
  while ($a < $increment) {
    // 每组进行插入排序
    // 直到待插入值不存在为止
    $point = $a; // 已排序末尾初始位置
    while (isset($value[$point+$increment])) {
      $next = $value[$point + $increment]; // 待插入值
      // 插入逻辑
      for ($i=$point; $i >= $a; $i -= $increment) {
        // 当前值大于待插入值 则后移步长当前值
        if ($value[$i] > $next) {
          $value[$i+$increment] = $value[$i];
          // 如果当前为首位索引位置 则当前位置插入待插入值
          if ($i === $a) {
            $value[$i] = $next;
          }
          continue;
        }
        // 当前值小于或者等于待插入值 则在上一个步长位置插入待插入值
        $value[$i+$increment] = $next;
        break;
      }
      $point += $increment;
    }
    ++$a;
  }
  // 递归
  shell($value, floor($increment/2));
  return $value;
}
