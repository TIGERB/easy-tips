<?php
/**
 * 时间戳
 *
 * @param  integer $accuracy 精度 默认微妙
 * @return int
 */
function getmicrotime($accuracy = 1000000)
{
    $microtime = explode(' ', microtime());
    return $microtime = (int)round(($microtime[1]+$microtime[0])*$accuracy, 0);
}
