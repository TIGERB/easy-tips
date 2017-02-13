<?php
/**
 * php算法实战.
 *
 * 排序算法-基数排序
 *
 * 分为两种LSD,MSD
 *
 * LSD:
 * 从个位开始，把当前位的数放到0～9对应的桶子中，直到最高位为止
 * 适合位数较短
 *
 * MSD：
 * 从最高位开始，不立即合并，再在桶中以下一位建立子桶，直到建立了最低位桶为止
 * 适合位数较多
 *
 * @author TIGERB <https://github.com/TIGERB>
 */

/**
 * 基数排序
 *
 * Least Significant Digit first
 *
 * 最低位优先排序
 *
 * @param array $value 待排序数组
 *
 * @return array
 */
function radix_lsd(&$value = [])
{
  // 获取序列值最大位数
  $max = 0;
  foreach ($value as $v) {
    $length = strlen((string)$v);
    if ($length > $max) {
      $max = $length;// 更新
    }
  }
  unset($v);
  $splice = 1;// 取最小位 初始从右往左数第一位

  while ($splice <= $max) {
    // 分配数字到桶中
    for ($i=0; $i < 10; $i++) {
      foreach ($value as $k => $v) {
        $length = strlen((string)$v);
        // 当前位索引位置
        $index = $length-$splice;
        // 不存在该位 则认为为0
        if ($index < 0) {
          if ($i === 0) {
            $arr[0][] = $v;
          }
          continue;
        }
        $aaa = ((string)$v)[$index];
        if (((string)$v)[$index] === (string)$i) {
          $arr[$i][] = $v;
        }
      }
      unset($v);
    }
    // 合并桶中数字
    unset($value);
    foreach ($arr as $tmp) {
      foreach ($tmp as $v) {
        $value[] = $v;
      }
    }
    unset($tmp);
    unset($v);
    unset($arr);
    ++$splice;
  }
  return $value;
}

/**
 * 基数排序
 *
 * Most Significant Digit first
 *
 * 最高位优先排序
 *
 * @param array $value 待排序数组
 * @param integer $max 序列最大位数
 *
 * @return array
 */
function radix_msd(&$value = [], $max=0, $max_origin=0, $length_origin=0, &$origin=[], $key=0)
{
  if ($max < 1) {
    return;
  }

  // 按最高位分组，不存在当前位则认为０
  $arr = [];
  for ($i=0; $i < 10; $i++) {
    foreach ($value as $v) {
      $length = strlen((string)$v);
      $index = $length - $max;
      if ($index < 0) {
        if ($i === 0) {
          $arr[0][] = $v;
        }
        continue;
      }
      if (((string)$v)[$index] === (string)$i) {
        $arr[$i][] = $v;
      }
    }
    unset($v);
  }
  unset($i);
  --$max;

  if (!empty($origin)) {
    $origin[$key] = $arr;
  }else{
    $value = $arr;
  }
  foreach ($value as $k => &$v) {
    radix_msd($v, $max, $max_origin, $length_origin, $value, $k);
  }

  if ($max < $max_origin-1) {
    return;
  }

  // 重新拼接
  return get_value($value, $length_origin);
}

/**
 *　合并排序
 *
 * 合并最后按个位排序完成的值
 *
 * @param  [type]  $value  排序后值
 * @param  integer $length 原始数组长度
 * @param  array  $result 存放排序后数的新空间
 * @return array          排序后数组
 */
function get_value($value=[], $length=0, &$result=[])
{
  if (count($result) === $length) {
    return;
  }
  foreach ($value as $k => $v) {
    if (is_array($v)) {
      get_value($v, $length, $result);
      continue;
    }
    $result[] = $v;
  }
  return $result;
}
