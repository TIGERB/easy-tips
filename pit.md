###### 记一些坑

```
//phpfpm配置

pm.max_children = 最大并发数

详细的答案：
pm.max_children 表示 php-fpm 能启动的子进程的最大数量。
因为 php-fpm 是多进程单线程同步模式，即一个子进程同时最多处理一个请求，所以子进程数等于最大并发数。
但是实际使用中一般不用考虑，因为php默认配置为动态均衡的子进程管理，不用手动设置这些配置。
```

```
//日志调试方法

/**
 * 超级调试
 *
 * 调试非本地环境或分布式环境，通过Log查看变量传递
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
// php实现下载图片

header('Content-type: image/jpeg');
header('Content-Disposition: attachment; filename=download_name.jpg');
readfile($yourFilePath);
```

```
// php5.6开始干掉了@语法，php上传图片兼容版本写法

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

```
// 序列化与反序列化

概念:
序列化：把变量(所有类型)转成能传输和储存的变量(不丢失原变量的属性和结构)
反序列化：把字符串转成原变量

函数：
序列化：serialize, json_encode(不能序列化对象)
反序列化：unserialize, json_decode
```

```
// 组合和聚合的区别
组合：A类在实例化对象的过程中产生了对B类的引用
聚合：A类在实例化对象的过程中，不会立即实例化B类，而是等待外界非A类的对象传递

```

```
// 记一个坑

ip2long函数
- 32位系统下会转成带符号的int，范围-2^31~2^31-1
- 64位系统下会转成不带符号的int，范围0~2^32-1
```

```
// static和self的区别

第一种解释:
- static: 代表当前所引用的类
- self: 代表当前代码片断所在的类

第二种解释：
如果子类和父类都有一个“A”方法。那么
- static: 会调用到子类的A方法
- self: 会调用到当前类的A方法，如果在子类中self::A()，将会调用到子类的A方法，如果在父类中，将会调用父类的A方法。

```

###### 技巧

- linux
    + df -h: 更易读的查看磁盘空间
    + du -h --max-depth=1 file_path:查看文件夹占用的空间，--max-depth文件夹下显示层级
    + sudo rm -rf \*.log：清理日志
    + socket
        * http socket = ip:port
        * unix domain socket: unix process communication 进程间通信
    + ubuntu16.04安装php5源：sudo apt-add-repository ppa:ondrej/php
    + ubuntu中文支持：sudo apt-get install language-pack-zh-hant language-pack-zh-hans
    + debian使用lantern无法启动： 安装依赖apt-get install libappindicator3-1
    + 查看端口占用：lsof -i:[端口号] / netstat -a（显示所有选项，默认不显示LISTEN）p(显示关联的程序)n（不显示别名显示数字） | grep [端口号]

- php:
    + json_encode($data, JSON_UNESCAPED_UNICODE)
    + php的自定义头信息都可以使用$_SERVER['HTTP_*']来获取
    + 如果你想知道脚本开始执行(译注：即服务器端收到客户端请求)的时刻，使用$_SERVER[‘REQUEST_TIME’]要好于time()
    + error_log(json_encode($data, JSON_UNESCAPED_UNICODE), 1/3, 'tigerbcoder@gmail.com/log_path')
    + sudo watch service php5.6-fpm status
    + foreach后的好习惯reset指针位置，unset掉$key，$value
    + curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    + laravel ['lærə,vɛl]
    + php中的语言结构：echo,exit(),print,empty(),unset(),isset(),list(),eval(),array()
- git:
    + git commit --amend 重写最近commit message
    + git cherry-pick 移花接木
- composer:
    + 修改包来源: sudo composer config repositories.包名 vcs 包地址


###### PHP的不足
- PHP还是有很多不足的地方，比如无法进行高效的运算
