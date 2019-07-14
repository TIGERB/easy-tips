<h1 align="center">《温故知新PHP》1.0</h1>

<p align="center">
<a href="https://github.com/TIGERB/easy-tips#目录">
  <img src="https://img.shields.io/badge/php-done-brightgreen.svg" alt="php">
</a>
<a href="https://github.com/TIGERB/easy-tips/tree/master/mysql">
  <img src="https://img.shields.io/badge/mysql-doing-blue.svg" alt="mysql">
</a>
<a href="https://github.com/TIGERB/easy-tips/tree/master/redis">
  <img src="https://img.shields.io/badge/redis-doing-blue.svg" alt="redis">
</a>
<a href="https://github.com/TIGERB/easy-tips/tree/master/patterns">
  <img src="https://img.shields.io/badge/patterns-done-brightgreen.svg" alt="patterns">
</a>
<a href="https://github.com/TIGERB/easy-tips/tree/master/algorithm">
  <img src="https://img.shields.io/badge/algorithm-%CE%B1-yellowgreen.svg" alt="algorithm">
</a>
<a href="https://github.com/TIGERB/easy-tips/tree/master/data-structure">
  <img src="https://img.shields.io/badge/data--structure-doing-blue.svg" alt="data-structure">
</a>
<a href="https://github.com/TIGERB/easy-tips/tree/master/network">
  <img src="https://img.shields.io/badge/network-doing-blue.svg" alt="network">
</a>
<a href="https://github.com/TIGERB/easy-tips/tree/master/docker">
  <img src="https://img.shields.io/badge/docker-doing-blue.svg" alt="docker">
</a>
<a href="#backers" alt="sponsors on Open Collective">
<img src="https://opencollective.com/easy-tips/backers/badge.svg" />
</a> 
<a href="#sponsors" alt="Sponsors on Open Collective">
<img src="https://opencollective.com/easy-tips/sponsors/badge.svg" />
</a> 
</p>

<p align="center"><a href="README-EN.md" >English Version</a></p>

<br>

> 一个php技术栈后端猿的知识储备仓库

<p align="center">
  <img src="http://cdn.tigerb.cn/wechat-blog-qrcode.jpg?imageMogr2/thumbnail/260x260!/format/webp/blur/1x0/quality/90|imageslim" width="200px">
</p>

## 前言

基础不牢，地动山摇，谨以此句提醒自己。

## 备注

状态        | 含义
--------- | -------
not-start | 当前未开始总结
doing     | 总结中
α         | 目前仅供参考未修正和发布
done      | 总结完毕
fixing    | 查漏补缺修改中

## 目录

- PHP基础学习(done)

  - 符合PSR的PHP编程规范(含个人建议)

    - [实例](https://github.com/TIGERB/easy-tips/blob/master/php/standard.php)
    - [文档](https://github.com/TIGERB/easy-tips/blob/master/php/standard.md)
    - [经验](https://github.com/TIGERB/easy-tips/blob/master/php/artisan.md)

  - 基础知识[读(R)好(T)文(F)档(M)]
    - [数据类型](http://php.net/manual/zh/language.types.php)

    - [运算符优先级](http://php.net/manual/zh/language.operators.precedence.php)
    - [string函数](http://php.net/ref.strings.php)
    - [array函数](http://php.net/manual/zh/ref.array.php)
    - [math函数](http://php.net/manual/zh/ref.math.php)
    - [面向对象](http://php.net/manual/zh/language.oop5.php)
    - 版本新特性

      - [7.1](http://php.net/manual/zh/migration71.new-features.php)
      - [7.0](http://php.net/manual/zh/migration70.new-features.php)
      - [5.6](http://php.net/manual/zh/migration56.new-features.php)
      - [5.5](http://php.net/manual/zh/migration55.new-features.php)
      - [5.4](http://php.net/manual/zh/migration54.new-features.php)
      - [5.3](http://php.net/manual/zh/migration53.new-features.php)

  - [记一些坑](https://github.com/TIGERB/easy-tips/blob/master/pit.md#记一些坑)

- PHP深入学习(doing)
  - 理解yield
  - php的协程
  - 生命周期
  - 垃圾回收
  - 扩展开发

- 网络编程(doing)
    - php实现web服务器
    - go实现web服务器
    - [c实现web服务器](https://github.com/taweisuode/webserver)
    - php扩展实现web服务器

- 问题排查(doing)
  - nginx/php/业务日志
  - 问题排查实例分析
  
- Mysql(doing)

  - [常用sql语句](https://github.com/TIGERB/easy-tips/blob/master/mysql/sql.md)
  - [引擎](https://github.com/TIGERB/easy-tips/blob/master/mysql/base.md#引擎)
    - InnoDB
    - MyISAM
    - Memory
    - Archive
    - Blackhole\CSV\Federated\merge\NDB
  - [事务](https://github.com/TIGERB/easy-tips/blob/master/mysql/base.md#事务)
    + 原子性（Atomicity）
    + 一致性（Consistency）
    + [隔离性（Isolation）](https://github.com/TIGERB/easy-tips/blob/master/mysql/base.md#mysql数据库为我们提供的四种隔离级别)
      * READ UNCOMMITTED:未提交读
      * READ COMMITTED：提交读/不可重复读
      * REPEATABLE READ：可重复读(MYSQL默认事务隔离级别)
      * SERIALIZEABLE：可串行化
    + 持久性（Durability）
  - [索引](https://github.com/TIGERB/easy-tips/blob/master/mysql/base.md#索引)
    + 建立表结构时添加的索引
      * 主键唯一索引
      * 唯一索引
      * 普通索引
      * 联合索引
        - 最左匹配原则
    + 依据是否聚簇区分
      * 聚簇索引
      * 非聚簇索引
    + 索引底层数据结构
      * hash索引
      * b-tree索引
      * b+tree索引
    
  - [锁](https://github.com/TIGERB/easy-tips/blob/master/mysql/base.md#锁)
    - 悲观锁
    - 乐观锁
  - 分表
    - 垂直分表
    - 水平分表
  - sql优化
  - 主从配置

- Redis(doing)

  - 常用命令
  - 实现原理&与memcache区别
  - 常见用途
    - [缓存](https://github.com/TIGERB/easy-tips/blob/master/redis/cache.php)
    - [队列](https://github.com/TIGERB/easy-tips/blob/master/redis/queue.php)
    - [悲观锁](https://github.com/TIGERB/easy-tips/blob/master/redis/pessmistic-lock.php)
    - [乐观锁](https://github.com/TIGERB/easy-tips/blob/master/redis/optimistic-lock.php)
    - [订阅/推送](https://github.com/TIGERB/easy-tips/blob/master/redis/subscribe-publish)
  - 常见业务实际使用
  - redis的基础数据结构

- Docker
  - [redis主从搭建](https://github.com/TIGERB/easy-tips/blob/master/docker/redis-master-slave/README.md)
  - [mysql主从搭建](https://github.com/TIGERB/easy-tips/blob/master/docker/mysql-master-slave/README.md)
  - [codis环境](https://github.com/TIGERB/easy-tips/blob/master/docker/codis/README.md)
  - mysql多主环境
  - kafka的环境搭建和使用
  - rabbitMQ的环境搭建和使用
  - zookeeper的环境搭建和使用
  - etcd的环境搭建和使用
  - ELK的环境搭建和使用
  - 网关服务kong的环境搭建和使用
  - 我所理想的架构

- 设计模式(done/fixing)

  - [概念](https://github.com/TIGERB/easy-tips/blob/master/patterns/thought.md#设计模式)

  - 创建型模式实例

    - [单例模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/singleton/test.php)
    - [工厂模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/factory/test.php)
    - [抽象工厂模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/factoryAbstract/test.php)
    - [原型模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/prototype/test.php)
    - [建造者模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/produce/test.php)

  - 结构型模式实例

    - [桥接模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/bridge/test.php)
    - [享元模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/flyweight/test.php)
    - [外观模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/facade/test.php)
    - [适配器模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/adapter/test.php)
    - [装饰器模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/decorator/test.php)
    - [组合模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/composite/test.php)
    - [代理模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/proxy/test.php)
    - [过滤器模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/filter/test.php)

  - 行为型模式实例

    - [模板模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/template/test.php)
    - [策略模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/strategy/test.php)
    - [状态模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/state/test.php)
    - [观察者模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/observer/test.php)
    - [责任链模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/chainOfResponsibility/test.php)
    - [访问者模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/visitor/test.php)
    - [解释器模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/interpreter/test.php)
    - [备忘录模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/memento/test.php)
    - [命令模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/command/test.php)
    - [迭代器模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/iterator/test.php)
    - [中介者器模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/mediator/test.php)
    - [空对象模式](https://github.com/TIGERB/easy-tips/blob/master/patterns/nullObject/test.php)

- [数据结构(doing)](https://github.com/TIGERB/easy-tips/blob/master/data-structure.md)

  - 数组
  - 堆/栈
  - 树
  - 队列
  - 链表
  - 图
  - 散列表

- 算法(doing)

  - 算法分析

    - 时间复杂度/空间复杂度/正确性/可读性/健壮性

  - 算法实战

    - 排序算法(α)

      - [冒泡排序](https://github.com/TIGERB/easy-tips/blob/master/algorithm/sort/bubble.php)
      - [快速排序](https://github.com/TIGERB/easy-tips/blob/master/algorithm/sort/quick.php)
      - [选择排序](https://github.com/TIGERB/easy-tips/blob/master/algorithm/sort/select.php)
      - [插入排序](https://github.com/TIGERB/easy-tips/blob/master/algorithm/sort/insert.php)
      - [归并排序](https://github.com/TIGERB/easy-tips/blob/master/algorithm/sort/merge.php)
      - [希尔排序](https://github.com/TIGERB/easy-tips/blob/master/algorithm/sort/shell.php)
      - [基数排序](https://github.com/TIGERB/easy-tips/blob/master/algorithm/sort/radix.php)

- 网络基础(doing)

  - [互联网协议概述](https://github.com/TIGERB/easy-tips/blob/master/network/internet-protocol.md#互联网协议)
  - [client和nginx简易交互过程](https://github.com/TIGERB/easy-tips/blob/master/network/nginx.md#client和nginx简易交互过程)
  - [nginx和php-fpm简易交互过程](https://github.com/TIGERB/easy-tips/blob/master/network/nginx.md#nginx和php简易交互过程)
  - [http](https://github.com/TIGERB/easy-tips/blob/master/network/http.md)
    - 报文
      - 报文头部
      - 报文体
    - 常见13种状态码
    - 方法method
    - https
    - http2
    - websocket

- 计算机基础(doing)

  - [linux常用命令](https://github.com/TIGERB/easy-tips/blob/master/linux/command.md)
  - shell

- 高并发相关(not-start)

- golang进阶(doing)
  
  - go框架源码阅读&解析
    - [golang框架解析-beego](http://tigerb.cn/2018/12/06/beego/)
    - [golang框架解析-iris](http://tigerb.cn/2019/06/29/go-iris/)
    - [golang框架解析-gin](http://tigerb.cn/2019/07/06/go-gin/)
    - [golang框架解析-echo](http://tigerb.cn/2019/07/13/go-echo/)
    - golang框架解析-revel
    - golang框架解析-Martini
  - go常用包解析
    - go常用包解析-fasthttp

---

## 测试用例

### 设计模式

运行脚本： php patterns/[文件夹名称]/test.php

```
例如,

测试责任链模式： 运行 php patterns/chainOfResponsibility/test.php

运行结果：

请求5850c8354b298: 令牌校验通过～
请求5850c8354b298: 请求频率校验通过～
请求5850c8354b298: 参数校验通过～
请求5850c8354b298: 签名校验通过～
请求5850c8354b298: 权限校验通过～
```

### 算法

运行脚本： php algorithm/test.php [算法名称｜空获取列表]

```
例如,

测试冒泡排序： 运行 php algorithm/test.php　bubble

运行结果：

==========================冒泡排序=========================
Array
(
    [0] => 11
    [1] => 67
    [2] => 3
    [3] => 121
    [4] => 71
    [5] => 6
    [6] => 100
    [7] => 45
    [8] => 2
)
=========上为初始值==================下为排序后值=============
Array
(
    [0] => 2
    [1] => 3
    [2] => 6
    [3] => 11
    [4] => 45
    [5] => 67
    [6] => 71
    [7] => 100
    [8] => 121
)
```

### 常见redis运用实现

运行脚本： php redis/test.php [名称｜空获取列表]

```
例如,

测试悲观锁： 运行 php redis/test.php p-lock

运行结果：

执行count加1操作～

count值为：1

```

## 纠错

如果大家发现有什么不对的地方，可以发起一个[issue](https://github.com/TIGERB/easy-tips/issues)或者[pull request](https://github.com/TIGERB/easy-tips),我会及时纠正，THX～

> 补充:发起pull request的commit message请参考文章[Commit message编写指南](http://www.ruanyifeng.com/blog/2016/01/commit_message_change_log.html)

## 英文版

因为国外开发者的要求和个人的时间有限，征集大家有兴趣的可以把本项目进行英文版翻译。希望国外的developer也可以受益于这个项目～

翻译文件认领申请：<https://github.com/TIGERB/easy-tips/issues/36>

## 赞赏

<img src="money-qrcode.jpg" width="300px">

## Contributors

This project exists thanks to all the people who contribute. 
<a href="graphs/contributors"><img src="https://opencollective.com/easy-tips/contributors.svg?width=890&button=false" /></a>


## Backers

Thank you to all our backers! 🙏 [[Become a backer](https://opencollective.com/easy-tips#backer)]

<a href="https://opencollective.com/easy-tips#backers" target="_blank"><img src="https://opencollective.com/easy-tips/backers.svg?width=890"></a>


## Sponsors

Support this project by becoming a sponsor. Your logo will show up here with a link to your website. [[Become a sponsor](https://opencollective.com/easy-tips#sponsor)]

<a href="https://opencollective.com/easy-tips/sponsor/0/website" target="_blank"><img src="https://opencollective.com/easy-tips/sponsor/0/avatar.svg"></a>
<a href="https://opencollective.com/easy-tips/sponsor/1/website" target="_blank"><img src="https://opencollective.com/easy-tips/sponsor/1/avatar.svg"></a>
<a href="https://opencollective.com/easy-tips/sponsor/2/website" target="_blank"><img src="https://opencollective.com/easy-tips/sponsor/2/avatar.svg"></a>
<a href="https://opencollective.com/easy-tips/sponsor/3/website" target="_blank"><img src="https://opencollective.com/easy-tips/sponsor/3/avatar.svg"></a>
<a href="https://opencollective.com/easy-tips/sponsor/4/website" target="_blank"><img src="https://opencollective.com/easy-tips/sponsor/4/avatar.svg"></a>
<a href="https://opencollective.com/easy-tips/sponsor/5/website" target="_blank"><img src="https://opencollective.com/easy-tips/sponsor/5/avatar.svg"></a>
<a href="https://opencollective.com/easy-tips/sponsor/6/website" target="_blank"><img src="https://opencollective.com/easy-tips/sponsor/6/avatar.svg"></a>
<a href="https://opencollective.com/easy-tips/sponsor/7/website" target="_blank"><img src="https://opencollective.com/easy-tips/sponsor/7/avatar.svg"></a>
<a href="https://opencollective.com/easy-tips/sponsor/8/website" target="_blank"><img src="https://opencollective.com/easy-tips/sponsor/8/avatar.svg"></a>
<a href="https://opencollective.com/easy-tips/sponsor/9/website" target="_blank"><img src="https://opencollective.com/easy-tips/sponsor/9/avatar.svg"></a>


