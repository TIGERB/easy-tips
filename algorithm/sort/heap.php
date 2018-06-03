<?php
/**
 * php算法实战.
 * php algorithm's practice
 * 
 * 排序算法-堆排序
 * sort algorithm - heap sort algorithm
 * 
 * @author cugblbs <https://github.com/cugblbs>
 */

/**
 * 堆排序.
 * heap sort algorithm.
 *
 * @param array $value 待排序数组 the array that is waiting for sorting
 *
 * @return array
 */
function heap(&$values = [])
{
    //堆化数组
    // the array that is simulated a heap
    $heap = [];
    foreach ($values as $i=>$v) {
        $heap[$i] = $v;
        $heap = minHeapFixUp($heap, $i);
    }
    $values = $heap;

    //堆排序
    //heap sort algorithm
    $n = count($values);
    for ($i = $n-1; $i>=1; $i--) {
        swap($values[$i], $values[0]);
        minHeapFixDown($values, 0, $i);
    }
    return $values;
}

function swap(&$a, &$b) {
    $temp = $a;
    $a= $b;
    $b = $temp;
}

/**
 * 堆插入数据
 * the method that is used to insert data to a heap 
 * 
 * @param $values
 * @param $i
 * @return mixed
 */
function minHeapFixUp($values, $i) {

    $j = ($i-1)/2;
    $temp = $values[$i];

    while($j >= 0 && $i != 0) {

        if($values[$j] <= $temp) {
            break;
        }
        $values[$i] = $values[$j];
        $i = $j;
        $j = ($i-1)/2;
    }
    $values[$i] = $temp;
    return $values;
}

/**
 * 调整堆,可用于删除堆节点
 * to adjust heap, it can be used to remove a node of heap.
 * 
 * @param $heap
 * @param $i
 * @param $n
 */
function minHeapFixDown(&$heap, $i, $n) {
    $j = 2*$i + 1;
    $temp = $heap[$i];

    while ($j < $n) {
        if($j+1 <$n && $heap[$j+1] < $heap[$j]) {
            $j++;
        }
        if($heap[$j] >= $temp) {
            break;
        }
        $heap[$i] = $heap[$j];
        $i = $j;
        $j = 2*$i + 1;
    }
    $heap[$i] = $temp;
}
