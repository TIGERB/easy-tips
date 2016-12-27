<?php
/**
 * php算法实战
 *
 * @author TIGERB <https://github.com/TIGERB>
 */

// 初始值
$data = [11, 9, 3, 16, 6, 10, 45, 2, 32];

if (!isset($argv[1])) {
    echo "\n";
    echo "参数有误，正确示例：php method.php bubble \n";
    echo "====================================== \n";
    echo "参数列表： \n";
    print_r([
    '冒泡排序' => 'bubble',
    '冒泡排序优化版' => 'bubble-better',
    '快速排序' => 'quick',
    '快速排序while版' => 'quick-while',
    '选择排序' => 'select',
    '插入排序' => 'insert',
    '合并有序数组' => 'merge-array',
    '归并排序' => 'merge',
    '堆排序'   => 'heap',
    '希尔排序' => 'hill',
    '基数排序' => 'radix',
    '二分查找' => 'binary-search'
    ]);
    die;
}
$method = $argv[1];


/*---------------------------- bubble ---------------------------------*/

if ($method === 'bubble') {
  require('./sort/bubble.php');
  echo "\n";
  echo "==========================冒泡排序========================= \n";
  echo "\n";
  print_r($data);
  echo "\n";
  echo "=========上为初始值==================下为排序后值============= \n";
  echo "\n";
  // run
  print_r(bubble($data));
}


/*---------------------------- bubble better ---------------------------------*/

if ($method === 'bubble-better') {
  require('./sort/bubble.php');
  echo "\n";
  echo "==========================优化冒泡排序========================= \n";
  echo "\n";
  print_r($data);
  echo "\n";
  echo "=========上为初始值=====================下为排序后值============= \n";
  echo "\n";
  // run
  print_r(bubble_better($data));
}


/*---------------------------- quick ---------------------------------*/

if ($method === 'quick') {
  require('./sort/quick.php');
  echo "\n";
  echo "==========================快速排序========================= \n";
  echo "\n";
  print_r($data);
  echo "\n";
  echo "=========上为初始值==================下为排序后值============= \n";
  echo "\n";
  // run
  print_r(quick($data, 0, count($data) - 1));
}


/*---------------------------- quick while版本 ---------------------------------*/

if ($method === 'quick-while') {
  require('./sort/quick.php');
  echo "\n";
  echo "==========================快排while版========================= \n";
  echo "\n";
  print_r($data);
  echo "\n";
  echo "=========上为初始值==================下为排序后值============= \n";
  echo "\n";
  // run
  print_r(quick_while($data, 0, count($data) - 1));
}


/*---------------------------- select ---------------------------------*/

if ($method === 'select') {
  require('./sort/select.php');
  echo "\n";
  echo "==========================选择排序========================= \n";
  echo "\n";
  print_r($data);
  echo "\n";
  echo "=========上为初始值==================下为排序后值============= \n";
  echo "\n";
  // run
  print_r(select_sort($data));
}


/*---------------------------- insert ---------------------------------*/

if ($method === 'insert') {
  require('./sort/insert.php');
  echo "\n";
  echo "==========================插入排序========================= \n";
  echo "\n";
  print_r($data);
  echo "\n";
  echo "=========上为初始值==================下为排序后值============= \n";
  echo "\n";
  // run
  print_r(insert($data, 0));
}


/*---------------------------- merge_array ---------------------------------*/

if ($method === 'merge-array') {
  require('./sort/merge.php');
  echo "\n";
  echo "================合并两个有序数组为一个有序数组================== \n";
  echo "\n";
  print_r([
    [1, 13,  22, 39],
    [2, 3, 6, 10, 16, 32, 66, 88, 99]
  ]);
  echo "\n";
  echo "=========上为初始值==================下为排序后值============= \n";
  echo "\n";
  // run
  print_r(merge_array(
    [1, 13,  22, 39],
    [2, 3, 6, 10, 16, 32, 66, 88, 99]
  ));
}


/*---------------------------- merge ---------------------------------*/

if ($method === 'merge') {
  require('./sort/merge.php');
  echo "\n";
  echo "==========================归并排序========================= \n";
  echo "看归并排序前，建议先看一下怎么合并两个有序数组为一个有序数据的逻辑 \n";
  echo "执行 php method.php merge-array \n";
  echo "\n";
  print_r($data);
  echo "\n";
  echo "=========上为初始值==================下为排序后值============= \n";
  echo "\n";
  // run
  print_r(merge($data, true));
}

/*---------------------------- heap ---------------------------------*/

if ($method === 'heap') {
  require('./sort/heap.php');
  echo "\n";
  echo "==========================堆排序========================= \n";
  echo "\n";
  print_r($data);
  echo "\n";
  echo "=========上为初始值==================下为排序后值============= \n";
  echo "\n";
  // run
  print_r(bubble($data));
}

/*---------------------------- hill ---------------------------------*/

if ($method === 'hill') {
  require('./sort/hill.php');
  echo "\n";
  echo "==========================希尔排序========================= \n";
  echo "\n";
  print_r($data);
  echo "\n";
  echo "=========上为初始值==================下为排序后值============= \n";
  echo "\n";
  // run
  print_r(bubble($data));
}

/*---------------------------- radix ---------------------------------*/

if ($method === 'radix') {
  require('./sort/radix.php');
  echo "\n";
  echo "==========================基数排序========================= \n";
  echo "\n";
  print_r($data);
  echo "\n";
  echo "=========上为初始值==================下为排序后值============= \n";
  echo "\n";
  // run
  print_r(bubble($data));
}
