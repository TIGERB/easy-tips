<?php

//实例化对象
$redis = new \Redis();

//连接客户端ip以及端口
$redis->connect('127.0.0.1', '6379');

//第三部：配置连接密码 检测redis服务器连接状态
//连接失败直接结束 并输出
$auth = $redis->auth('auth')  or die("redis 服务器连接失败");

//判断是否连接成功
if(!$auth){
    die('连接失败!');
}

//判断可用
if ($redis->ping() != "+PONG") {
   die('redis 服务器不可用');
}