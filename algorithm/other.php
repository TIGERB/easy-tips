<?php
function decimalismtobinary($number, &$arr=[])
{
  array_unshift($arr, $number%2);
  $number = floor($number/2);
  if ((int)$number === 0) {
    return;
  }
  decimalismtobinary($number, $arr);
  return implode('', $arr);
}

function binarytodecimalism($str)
{
  $length = strlen($str)-1;
  $res = 0;
  for ($i=$length; $i >= 0; --$i) {
    $res += $str[$i]*(2**($length-$i));
  }
  return $res;
}
