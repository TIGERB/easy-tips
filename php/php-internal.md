宏   | 含义
-----|--------------------------
EG() | 访问符号表，函数，资源信息，常量
CG() | 访问全局核心变量
PG() | PHP全局变量
FG() | 文件全局变量
---

# 我目前所能理解的php生命周期过程(其他更为细致的操作，我并不知道具体的含义，待我理解了再逐步添加)

- php_module_startup()
    + 注册常量
    + 加载.ini
    + 注册(_GET, _POST, _REQUEST, _FILES, _SERVER, _ENV, _COOKIE)全局变量处理handler
    + 加载扩展
        * 核心扩展
        * 静态扩展 - 扩展被编译到程序内部
        * 动态扩展 - 扩展未被编译到程序内部 (根据php.ini配置加载动态扩展.so)
        * 调用各扩展的MINIT
        * 调用各扩展的starup
    + ...
- php_request_startup()
    + 加载全局变量handler
    + 调用个扩展的RINIT
    + ...
- php_execute_php() 执行阶段
    + 词法分析
    + 语法分析
    + 抽象语法树ast
    + ...
- php_request_shutdown()
    + 依次调用register_shutdown_function注册的钩子函数
    + 执行析构函数
    + 调用各扩展的RSHOTDOWN
    + flush输出
    + ...
- php_module_shutdown()
    + 调用各扩展的MSHUTDOWN
    + ...

# php的垃圾回收 - 同步周期回收算法

回收的目标变量类型：非标量类型的复合类型，数组／对象，原因：这类数据类型会产生自身的循环引用，举个例子：

```php
<php

$a = [1, 2, 3];
$a[1] = &$a;
unset($a);
```

分析：

首先得提到zval, php的变量都存在这个容器中，简单的zval结构如下:

```c
struct _zval_struct {
    type
    value
    is_ref_gc
    refcount_gc
} zval
```

接着分析上面的php代码：

同步周期回收的基本步骤：

- 变量的refcount_gc减1后不为0，则标记上紫色(疑似垃圾)，丢入垃圾回收池(根缓冲区，10000个值)
- 垃圾回收池(根缓冲区)满了开始垃圾回收算法
- 垃圾回收池(根缓冲区)内的紫色变量refcount_gc模拟减1，标记为灰色
- 垃圾回收池(根缓冲区)内标记为灰色且refcount_gc>0的变量refcount_gc模拟加1，标记上黑色
- refcount_gc为0则为垃圾，标记上蓝色，说明自身持有了自身的引用
- 清除垃圾回收池(根缓冲区)内标记为蓝色的变量，此次垃圾回收结束


