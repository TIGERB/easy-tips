<?php

$demo = range(1, 3);
foreach ($demo as &$v) {

}
foreach ($demo as $v) {

}

var_dump($demo);

// $a = 0;
// var_dump([$a, isset($a)]);