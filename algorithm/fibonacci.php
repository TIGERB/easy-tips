<?php
/**
 * php算法实战
 *
 * 斐波纳耶数列
 *
 * @author TIGERB <https://github.com/TIGERB>
 */

/**
 * for循环斐波纳耶
 *
 * @param  integer $n 数列长度
 * @return array
 */
function forcycle($n = 0)
{
    $res = [];
    for ($i = 0; $i < $n; $i++) {
        if ($i === 0) {
            $res[] = 0;
            continue;
        }
        if ($i === 1) {
            $res[] = 1;
            continue;
        }
        $res[] = $res[$i - 1] + $res[$i - 2];
    }
    return $res;
}

/**
 * 递归循环斐波纳耶
 *
 * @param  integer $n   数列长度
 * @param  integer $i   当前位置
 * @param  integer $res 数列
 * @return array
 */
function recursion($n = 0, $i = 0, $res = [])
{
    if ($i >= $n) {
        return $res;
    }
    if ($i === 0) {
        $res[] = 0;
    } elseif ($i === 1) {
        $res[] = 1;
    } else {
        $res[] = $res[$i - 1] + $res[$i - 2];
    }
    return recursion($n, ++$i, $res);
}
