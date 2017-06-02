<?php
/**
 * php算法实战.
 *
 * 排序算法-归并排序
 *
 * @author TIGERB <https://github.com/TIGERB>
 */

 /**
  * 合并两个有序数组为一个有序数组
  *
  * @param  array $value 待排序数组
  *
  * @return array
  */
  function merge_array($arr_1, $arr_2)
  {
    $length_1 = count($arr_1);
    $length_2 = count($arr_2);
    // 归并算法
    // arr_1[$i]和arr_2[$j]比较
    // <= 则 arr_3[$k] = arr_1[$i] 且 ++$i ++$k
    // >= 则 arr_3[$k] = arr_2[$j] 且 ++$j ++$k
    // 直到 $i >= $length_1 或 $j >= $length_2
    //
    // 接着，如果先$i >= $length_1
    // 则， $arr_2[$j~$length_2] 放到 $arr_3后
    // 如果先$j >= $length_2
    // 则， $arr_1[$i~$length_1] 放到 $arr_3后
    $arr_3 = [];
    $i = 0;
    $j = 0;
    $k = 0;
    while ($i < $length_1 && $j < $length_2) {
      if ($arr_1[$i] <= $arr_2[$j]) {
        $arr_3[$k] = $arr_1[$i];
        ++$i;
        ++$k;
        continue;
      }
      $arr_3[$k] = $arr_2[$j];
      ++$j;
      ++$k;
    }
    if ($i === $length_1) {
      for ($s=$j; $s < $length_2; $s++) {
        $arr_3[] = $arr_2[$s];
      }
    }
    if ($j === $length_2) {
      for ($w=$i; $w < $length_1; $w++) {
        $arr_3[$k] = $arr_1[$w];
      }
    }
    return $arr_3;
  }

  /**
   * 归并排序.
   * 将序列每相邻的两个数字进行归并操作
   *
   * @param  array $value 待排序数组
   *
   * @return array
   */
  function merge(&$value=[])
  {
    $length = count($value);
    if ($length === 1) {
      return;
    }
    $arr = [];
    for ($i=0; $i < $length; $i++) {
      if ($i%2 === 0) {
        // 合并每两个元素
        // 判断值类型 integer 直接比大小 合并
        if (is_int($value[$i]) || is_string($value[$i])) {
          if (isset($value[$i+1])) {
            if ($value[$i] < $value[$i+1]) {
              $arr[floor($i/2)][] = $value[$i];
              $arr[floor($i/2)][] = $value[$i+1];
              continue;
            }
            $arr[floor($i/2)][] = $value[$i+1];
            $arr[floor($i/2)][] = $value[$i];
            continue;
          }
          $arr[floor($i/2)][] = $value[$i];
          continue;
        }
        // 判断值类型 array 遍历元素比大小 合并
        // 把两个有序数组合并成一个有序数组
        // 归并算法详情请看 merge-array
        if (is_array($value[$i])) {
          if (isset($value[$i+1])) {
            $length_arr = count($value[$i]);
            $length_arr_last = count($value[$i+1]);
            $arr_tmp = [];
            $s = 0;
            $w = 0;
            $k = 0;
            while ($s < $length_arr && $w < $length_arr_last) {
              if ($value[$i][$s] <= $value[$i+1][$w]) {
                $arr_tmp[$k] = $value[$i][$s];
                ++$s;
                ++$k;
                continue;
              }
              $arr_tmp[$k] = $value[$i+1][$w];
              ++$w;
              ++$k;
              continue;
            }
            if ($s === $length_arr) {
              for ($j=$w; $j < $length_arr_last; $j++) {
                $arr_tmp[$k] = $value[$i+1][$j];
                ++$k;
              }
            }
            unset($j);
            if ($w === $length_arr_last) {
              for ($j=$s; $j < $length_arr; $j++) {
                $arr_tmp[$k] = $value[$i][$j];
                ++$k;
              }
            }
            unset($j);
            $arr[floor($i/2)] = $arr_tmp;
            continue;
          }
          $arr[floor($i/2)] = $value[$i];
          continue;
        }
      }
    }
    $value = $arr;
    merge($value);

    return $value[0];
  }


/* ----------------- 归并写法二 ------------------ */

  $mergeFirst = function ($arr=array())
  {
      $len = count($arr);
      $res = [];
      for ($i = 0; $i < $len; $i += 2) {
          $j = floor($i/2);
          if (!isset($arr[$i + 1])) {
              $res[$j][] = $arr[$i];
              continue;
          }
          if ($arr[$i] < $arr[$i + 1]) {
              $res[$j][] = $arr[$i];
              $res[$j][] = $arr[$i + 1];
              continue;
          }
          $res[$j][] = $arr[$i + 1];
          $res[$j][] = $arr[$i];
      }
      return $res;
  };

  $mergeArray = function ($arr1, $arr2)
  {
      $len1 = count($arr1);
      $len2 = count($arr2);
      $arr3 = [];
      $a = 0;
      $b = 0;
      $k = 0;
      while ($a < $len1 && $b < $len2) {
          if ($arr1[$a] < $arr2[$b]) {
              $arr3[$k] = $arr1[$a];
              $a++;
              $k++;
              continue;
          }
          $arr3[$k] = $arr2[$b];
          $b++;
          $k++;
      }
      if ($a === $len1) {
          for ($i = $b; $i < $len2; $i++) {
              $arr3[] = $arr2[$i];
          }
      }
      unset($i);
      if ($b === $len2) {
          for ($i = $a; $i < $len1; $i++) {
              $arr3[] = $arr1[$i];
          }
      }
      return $arr3;
  };

  function sorta($arr=array(), $mergeFirst, $mergeArray)
  {
      if (count($arr) === 1) {
          return $arr[0];
      }
      if (!is_array($arr[0])) {
          $arr = $mergeFirst($arr);
      }
      $len = count($arr);
      $arrNew = [];
      for ($i=0; $i < $len; $i += 2) {
          $j = floor($i/2);
          if (!isset($arr[$i + 1])) {
              $arrNew[$j] = $arr[$i];
              continue;
          }
          $arrNew[$j] = $mergeArray($arr[$i], $arr[$i+1]);
      }

      $res = sorta($arrNew, $mergeFirst, $mergeArray);

      return $res;
  }
