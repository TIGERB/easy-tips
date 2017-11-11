<?php
/**
 * Created by PhpStorm.
 * User: mumu
 * Date: 2017/11/10
 * Time: 下午9:37
 */

/**
 * 大整数乘法
 */
//数字1
$n1 = "5624672436482632613453245";
//数字2
$n2 = "3532464567546846587658765";

//九九乘法表
$muti = array();
for ($i = 0; $i < 10; $i++) {
    for ($j = 0; $j < 10; $j++) {
        $muti[strval($i)][strval($j)] = $i * $j;
    }
}
//最长长度
$len_1 = strlen($n1);
//最短长度
$len_2 = strlen($n2);

//结果长度
$len_r = $len_1+$len_2+1;

//运算结果
$result = array_fill(0, $len_r, 0);

//数字反序
$n1 = strrev($n1);
$n2 = strrev($n2);

//按位运算
for ($i = 0; $i < $len_1; $i++) {
    for ($j = 0; $j < $len_2; $j++) {
        // echo $i . '-' . $j.PHP_EOL;
        $result[$i + $j] += $muti[$n1[$i]][$n2[$j]];
    }
}

//进位处理
$i = 0;
$j = $len_r-1;
do{
    $result[$i + 1] += (int) ($result[$i] / 10);
    $result[$i] = $result[$i] % 10;
}  while (++$i<$j);
//exit();

$i = $j - 1;
//去除前导0
while ($i && !$result[$i--]) {
};
//输出结果
$i++;
do {
    echo $result[$i];
} while ($i--);