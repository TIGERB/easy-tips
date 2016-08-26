# career-tips | 踩坑路
a little tips in my code career | 码字踩过的那些坑


## 设计模式

###### 面向对象的设计原则
- 对接口编程，不要对实现编程
- 使用对象之间的组合，减少对继承的使用

###### 设计模式的设计原则
- 开闭原则：对扩展开放，对修改封闭
- 依赖倒转：对接口编程，依赖于抽象而不依赖于具体
- 接口隔离：使用多个接口，而不是对一个接口编程，去依赖降低耦合
- 最少知道：减少内部依赖，尽可能的独立
- 合成复用：多个独立的实体合成聚合，而不是使用继承
- 里氏代换：超类（父类）出现的地方，派生类（子类）都可以出现
- 抽象用于不同的事物，而接口用于事物的行为

###### 简单设计原则
- 通过所有测试:及需求为上
- 尽可能的消除重复：高内聚低耦合
- 尽可能的清晰表达：可读性
- 更少代码元素：常量，变量，函数，类，包 …… 都属于代码元素，降低复杂性
- 以上四个原则的重要程度依次降低

>

## php笔记

###### client和nginx简易交互过程
- step1:
- step2:
- step3:
- step4:
- step5:

###### nginx和php简易交互过程
- 背景：web server和服务端语言交互依赖的是cgi(Common Gateway Interface)协议，php-cgi实现了cgi,由于cgi效率
不高，后期产生了fastcgi协议,php-fpm是对fastcgi的实现
- 流程：
  + step1:nginx接收到一条http请求，会把环境变量，请求参数转变成php能懂的php变量
  ```
  // nginx 配置资料
  location ~ \.php$ {
        include snippets/fastcgi-php.conf; //step1
        fastcgi_pass unix:/run/php/php7.0-fpm.sock;
  }
  ```
  + step2:nginx匹配到.php结尾的访问通过fastcgi_pass命令传递给php-fpm.sock文件，其实这里
  的ngnix发挥的是反向代理的角色，把http协议请求转到cgi协议请求
  ```
  // nginx 配置资料
  location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php7.0-fpm.sock;// step2
  }
  ```
  + step3:php-fpm.sock文件会被php-fpm的master进程所引用，这里nginx和php-fpm使用的是
  linux的进程间通信方式unix domain socks，是一种基于文件而不是网络底册协议的通信方式
  + step4:php-fpm的master进程接收到请求后，会把请求分发到php-fpm的子进程，每个php-fpm
  子进程都包含一个php解析器
  + step5:php-fpm进程处理完请求后返回给nginx



```
//课外知识
pm.max_children = 最大并发数

详细的答案：
pm.max_children 表示 php-fpm 能启动的子进程的最大数量。
因为 php-fpm 是多进程单线程同步模式，即一个子进程同时最多处理一个请求，所以子进程数等于最大并发数。
```

```
/**
 * 超级调试
 *
 * 写入变量值到\var\log\php_super_debug.log
 * @param  mixed  $data     日志数据
 * @param  string $log_path 日志路径
 * @param  string $log_name 日志名称
 * @return void       
 */
function super_debug($data, $log_path='\var\log\', $log_name='debug')
{
  error_log(json_encode($data, JSON_UNESCAPED_UNICODE)."\n", 3, $log_path.$log_name);
}
```

```
// php下载图片
header('Content-type: image/jpeg');
header('Content-Disposition: attachment; filename=download_name.jpg');
readfile($yourFilePath);
```

```
// php5.5以上的
if (class_exists('\CURLFile')) {
    curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
    $data = array('file' => new \CURLFile(realpath($destination)));//5.5+
} else {
    if (defined('CURLOPT_SAFE_UPLOAD')) {
        curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
    }
    $data = array('file' => '@' . realpath($destination));//<=5.5
}
```

// pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000

- linux
    + df -h: 更易读的查看磁盘空间
    + sudo rm -rf \*.log：清理日志
    + socket
        * http socket = ip:port
        * unix domain socket: unix process communication 进程间通信
    + ubuntu16.04安装php5源：sudo apt-add-repository ppa:ondrej/php

- mysql
    + 数据清理：TRUNCATE TABLE XXX

- php:
    + json_encode($data, JSON_UNESCAPED_UNICODE)
    + php的自定义头信息都可以使用$_SERVER['HTTP_*']来获取
    + 如果你想知道脚本开始执行(译注：即服务器端收到客户端请求)的时刻，使用$_SERVER[‘REQUEST_TIME’]要好于time()
    + error_log(json_encode($data, JSON_UNESCAPED_UNICODE), 1/3, 'tigerbcoder@gmail.com/log_path')
    + sudo watch service php5.6-fpm status
    + foreach后的好习惯reset指针位置，unset掉$key，$value
    + curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    + laravel ['lærə,vɛl]
- git:
    + git commit --amend 重写最近commit message
    + git cherry-pick 移花接木
- composer:
    + 修改包来源: sudo composer config repositories.包名 vcs 包地址


## PHP的不足
- PHP还是有很多不足的地方，比如无法进行高效的运算


## 互联网协议(internet protocol)：从上到下，越上越接近用户，越下越接近硬件
- 应用层:
    + 规定应用程序的数据格式
    + [HEAD(以太网标头) [HEAD(IP标头) [HEAD(TCP标头) DATA(应用层数据包)]]]

- 传输层(端口到端口的通信):
    + 端口：
        * 0到65535(2^16)的整数
        * 进程使用网卡的编号
        * 通过IP+mac确定主机，只要确定主机+端口(套接字socket)，就能进行程序间的通信
    + UDP协议：
        * 数据包中加入端口依赖的新协议
        * 数据包[HEAD(发送、接收mac) [HEAD(发送、接收ip) [HEAD(发送、接收端口) DATA]]]
        * 简单，可靠性差，不知道对方是否接受包
    + TCP协议：
        * 带有确认机制的UDP协议
        * 过程复杂，实现困难，消耗资源

- 网络层(主机到主机的通信):
    + IP协议
        * ipv4:
            - 32个二进制位表示，由网络部分和主机部分构成，
            - 子网掩码: 网络部分都为1，主机部分都为0，目的判断ip的网络部分，如255.255.255.0(11111111.11111111.11111111.00000000)
            - IP数据包：标头Head+数据Data,放进以太网数据包的Data部分[HEAD [HEAD DATA]]
            - IP数据包的传递：
                + 非同一网络：无法获得mac地址,发送数据到网关，网关处理
                + 同一网络：mac地址填写FF:FF:FF:FF:FF:FF:FF，广播数据，对比ip，不符合丢包

- 链接层：
    + 定义数据包(帧Frame)
        * 标头(Head):数据包的一些说明项, 如发送者、接收者、数据类型
        * 数据(Data):数据包的具体内容
        * 数据包:[HEAD DATA]
    + 定义网卡和网卡唯一的mac地址
        * 以太网规定接入网络的所有终端都应该具有网卡接口，数据包必须是从一个网卡的mac地址到另一网卡接口的mac地址
        * mac全球唯一，16位16位进制组成，前6厂商编号，后6网卡流水号
    + 广播发送数据
        * 向本网络内的所有设备发送数据包，对比接收者mac地址，不是丢包，是接受

- 实体层：
    + 终端(pc，phone，pad...)的物理连接(光缆，电缆，路由...)，负责传递0和1信号


tcp/ip connect:
```
        syn握手信号
        ------------->
        syn/ack确认字符
client  <-------------  server
        ack确认包
        -------------->
```
