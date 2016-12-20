# easy-tips

> 一个php技术栈后端猿的知识储备大纲

## 前言

为什么把php,mysql,redis放在前三位？因为php/mysql/redis基础是一个当代phper的根基。

## 备注

状态        | 含义
--------- | -------
not-start | 当前未开始总结
doing     | 总结中
done      | 总结完毕
fixing    | 查漏补缺修改中

## 目录

- PHP(doing)

  - [符合PSR-1/PSR-2的PHP编程规范实例](https://github.com/TIGERB/easy-tips/blob/master/standard.php)

  - 基础知识[读(R)好(T)文(F)档(M)]

    - [数据类型](http://php.net/manual/zh/language.types.php)
    - [运算符优先级](http://php.net/manual/zh/language.operators.precedence.php)
    - [string函数](http://php.net/ref.strings)
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

  - [记一些坑](https://github.com/TIGERB/easy-tips/blob/master/tips-2016.md#记一些坑)

- Mysql(doing)

  - [常用sql语句](https://github.com/TIGERB/easy-tips/blob/master/sql.md)
  - 引擎
  - 索引
  - 锁机制

- Redis(not-start)

  - 常用命令
  - 常见使用场景实战

    - 队列
    - 订阅/推送

- 设计模式(done/fixing)

  - [概念](https://github.com/TIGERB/easy-tips/blob/master/tips-2016.md#设计模式)

  - 创建型模式实例

    - [单例模式](https://github.com/TIGERB/easy-tips/blob/master/singleton/test.php)
    - [工厂模式](https://github.com/TIGERB/easy-tips/blob/master/factory/test.php)
    - [抽象工厂模式](https://github.com/TIGERB/easy-tips/blob/master/factoryAbstract/test.php)
    - [原型模式](https://github.com/TIGERB/easy-tips/blob/master/prototype/test.php)
    - [建造者模式](https://github.com/TIGERB/easy-tips/blob/master/builder/test.php)

  - 结构型模式实例

    - [桥接模式](https://github.com/TIGERB/easy-tips/blob/master/bridge/test.php)
    - [享元模式](https://github.com/TIGERB/easy-tips/blob/master/flyweight/test.php)
    - [外观模式](https://github.com/TIGERB/easy-tips/blob/master/facade/test.php)
    - [适配器模式](https://github.com/TIGERB/easy-tips/blob/master/adapter/test.php)
    - [装饰器模式](https://github.com/TIGERB/easy-tips/blob/master/decorator/test.php)
    - [组合模式](https://github.com/TIGERB/easy-tips/blob/master/composite/test.php)
    - [代理模式](https://github.com/TIGERB/easy-tips/blob/master/proxy/test.php)
    - [过滤器模式](https://github.com/TIGERB/easy-tips/blob/master/filter/test.php)

  - 行为型模式实例

    - [模板模式](https://github.com/TIGERB/easy-tips/blob/master/template/test.php)
    - [策略模式](https://github.com/TIGERB/easy-tips/blob/master/strategy/test.php)
    - [状态模式](https://github.com/TIGERB/easy-tips/blob/master/state/test.php)
    - [观察者模式](https://github.com/TIGERB/easy-tips/blob/master/observer/test.php)
    - [责任链模式](https://github.com/TIGERB/easy-tips/blob/master/chainOfResponsibility/test.php)
    - [访问者模式](https://github.com/TIGERB/easy-tips/blob/master/visitor/test.php)
    - [解释器模式](https://github.com/TIGERB/easy-tips/blob/master/interpreter/test.php)
    - [备忘录模式](https://github.com/TIGERB/easy-tips/blob/master/memento/test.php)
    - [命令模式](https://github.com/TIGERB/easy-tips/blob/master/command/test.php)
    - [迭代器模式](https://github.com/TIGERB/easy-tips/blob/master/iterator/test.php)
    - [中介者器模式](https://github.com/TIGERB/easy-tips/blob/master/mediator/test.php)
    - [空对象模式](https://github.com/TIGERB/easy-tips/blob/master/nullObject/test.php)

- 数据结构(not-start)

  - 数组
  - 堆/栈
  - 树
  - 队列
  - 链表
  - 图
  - 散列表

- 算法(not-start)

  - 算法分析

    - 时间复杂度/空间复杂度/正确性/可读性/健壮性

  - 算法实战

- 网络基础(doing)

  - [互联网协议概述](https://github.com/TIGERB/easy-tips/blob/master/tips-2016.md#互联网协议)
  - [client和nginx简易交互过程](https://github.com/TIGERB/easy-tips/blob/master/tips-2016.md#client和nginx简易交互过程)
  - [nginx和php-fpm简易交互过程](https://github.com/TIGERB/easy-tips/blob/master/tips-2016.md#nginx和php简易交互过程)

- 计算机基础(not-start)

  - linux常用命令

## 设计模式测试

运行脚本： php [文件夹名称]/test.php

```
例如,

测试责任链模式： 运行 php chainOfResponsibility/test.php

运行结果：

请求5850c8354b298: 令牌校验通过～
请求5850c8354b298: 请求频率校验通过～
请求5850c8354b298: 参数校验通过～
请求5850c8354b298: 签名校验通过～
请求5850c8354b298: 权限校验通过～
```

## 纠错

如果大家发现有什么不对的地方，可以发起一个[issue](https://github.com/TIGERB/easy-tips/issues)或者[pull request](https://github.com/TIGERB/easy-tips),我会及时纠正，THX～

> 补充:发起pull request的commit message请参考文章[Commit message 和 Change log 编写指南](http://www.ruanyifeng.com/blog/2016/01/commit_message_change_log.html)

## 感谢

感谢以下朋友的issue或pull request：

- @[faynwol](https://github.com/faynwol)
- @[whahuzhihao](https://github.com/whahuzhihao)
- @[snriud](https://github.com/snriud)
- @[fhefh2015](https://github.com/fhefh2015)
- @[RJustice](https://github.com/RJustice)
- @[ooing](https://github.com/ooing)
