<h1 align="center">Easy Tips</h1>

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
</p>

<br>

> A knowledge storage for the PHP developer

## Remark

flag      | meaning
--------- | -------
not-start | not start
doing     | ing
α         | for reference only
done      | complete
fixing    | fix

## 目录

- PHP(doing)

  - PHP code standard with PSR(Include personal suggestion)

    - [example](https://github.com/TIGERB/easy-tips/blob/master/php/standard.php)
    - [doc](https://github.com/TIGERB/easy-tips/blob/master/php/standard.md)
    - [experience](https://github.com/TIGERB/easy-tips/blob/master/php/artisan.md)

  - Base knowledge[RTFM]

    - [datastruct](http://php.net/manual/zh/language.types.php)
    - [operator level](http://php.net/manual/zh/language.operators.precedence.php)
    - [string functions](http://php.net/ref.strings.php)
    - [array functions](http://php.net/manual/zh/ref.array.php)
    - [math functions](http://php.net/manual/zh/ref.math.php)
    - [object oriented](http://php.net/manual/zh/language.oop5.php)
    - Feature

      - [7.1](http://php.net/manual/zh/migration71.new-features.php)
      - [7.0](http://php.net/manual/zh/migration70.new-features.php)
      - [5.6](http://php.net/manual/zh/migration56.new-features.php)
      - [5.5](http://php.net/manual/zh/migration55.new-features.php)
      - [5.4](http://php.net/manual/zh/migration54.new-features.php)
      - [5.3](http://php.net/manual/zh/migration53.new-features.php)

  - [Some pit in my code career](https://github.com/TIGERB/easy-tips/blob/master/pit.md#记一些坑)

- Mysql(doing)

  - [Some base sql](https://github.com/TIGERB/easy-tips/blob/master/mysql/sql.md)
  - [Engine](https://github.com/TIGERB/easy-tips/blob/master/mysql/base.md#引擎)
    - InnoDB
    - MyISAM
    - Memory
    - Archive
    - Blackhole\CSV\Federated\merge\NDB
  - [Transaction](https://github.com/TIGERB/easy-tips/blob/master/mysql/base.md#事务)
    + Atomicity
    + Consistency
    + [Isolation](https://github.com/TIGERB/easy-tips/blob/master/mysql/base.md#mysql数据库为我们提供的四种隔离级别)
      * READ UNCOMMITTED
      * READ COMMITTED
      * REPEATABLE READ
      * SERIALIZEABLE
    + Durability
  - [Index](https://github.com/TIGERB/easy-tips/blob/master/mysql/base.md#索引)
    + Index
      * Primary unique index
      * Unique index
      * Index
      * Union index
        - Left match principle
    + Cluster
      * Cluster index
      * Non-Cluster index
    + Datastruct
      * hash index
      * b-tree index
      * b+tree index
    
  - [Lock](https://github.com/TIGERB/easy-tips/blob/master/mysql/base.md#锁)
    - Pessimistic lock
    - Optimistic lock
  - Submeter
    - Vertical
    - Horizontal
  - Sql optimize
  - Master-Slave

- Redis(doing)

  - Command
  - Diff with memcache 
  - Some Example
    - [cache](https://github.com/TIGERB/easy-tips/blob/master/redis/cache.php)
    - [queue](https://github.com/TIGERB/easy-tips/blob/master/redis/queue.php)
    - [pessimistic lock](https://github.com/TIGERB/easy-tips/blob/master/redis/pessmistic-lock.php)
    - [optimistic lock](https://github.com/TIGERB/easy-tips/blob/master/redis/optimistic-lock.php)
    - [subscribe&publish](https://github.com/TIGERB/easy-tips/blob/master/redis/subscribe-publish)

- Design Pattern(done/fixing)

  - [Concept](https://github.com/TIGERB/easy-tips/blob/master/patterns/thought.md#设计模式)

  - Creational Pattern

    - [singleton pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/singleton/test.php)
    - [factory pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/factory/test.php)
    - [abstract factory pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/factoryAbstract/test.php)
    - [prototype pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/prototype/test.php)
    - [produce pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/produce/test.php)

  - Construction Pattern

    - [bridge pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/bridge/test.php)
    - [flyweight pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/flyweight/test.php)
    - [facade pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/facade/test.php)
    - [adapter pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/adapter/test.php)
    - [decorator pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/decorator/test.php)
    - [composite pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/composite/test.php)
    - [proxy pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/proxy/test.php)
    - [filter pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/filter/test.php)

  - Behavior Pattern

    - [template pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/template/test.php)
    - [strategy pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/strategy/test.php)
    - [state pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/state/test.php)
    - [observer pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/observer/test.php)
    - [chain of responsibility pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/chainOfResponsibility/test.php)
    - [visitor pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/visitor/test.php)
    - [interpreter pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/interpreter/test.php)
    - [memento pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/memento/test.php)
    - [command pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/command/test.php)
    - [iterator pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/iterator/test.php)
    - [mediator pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/mediator/test.php)
    - [null object pattern](https://github.com/TIGERB/easy-tips/blob/master/patterns/nullObject/test.php)

- [Data-structure(doing)](https://github.com/TIGERB/easy-tips/blob/master/data-structure.md)

  - array
  - heap/stack
  - tree
  - queue
  - list
  - graph
  - hash

- Algorithm(doing)

  - analyze

    - time complexity/space complexity/corectness/readability/robustness

  - examples

    - sort algorithm(α)

      - [bubble sort](https://github.com/TIGERB/easy-tips/blob/master/algorithm/sort/bubble.php)
      - [quick sort](https://github.com/TIGERB/easy-tips/blob/master/algorithm/sort/quick.php)
      - [select sort](https://github.com/TIGERB/easy-tips/blob/master/algorithm/sort/select.php)
      - [insert sort](https://github.com/TIGERB/easy-tips/blob/master/algorithm/sort/insert.php)
      - [merge sort](https://github.com/TIGERB/easy-tips/blob/master/algorithm/sort/merge.php)
      - [shell sort](https://github.com/TIGERB/easy-tips/blob/master/algorithm/sort/shell.php)
      - [radix sort](https://github.com/TIGERB/easy-tips/blob/master/algorithm/sort/radix.php)

- Netwok basis (doing)

  - [Internet protocol](https://github.com/TIGERB/easy-tips/blob/master/network/internet-protocol.md#互联网协议)
  - [client with nginx](https://github.com/TIGERB/easy-tips/blob/master/network/nginx.md#client和nginx简易交互过程)
  - [nginx with php-fpm](https://github.com/TIGERB/easy-tips/blob/master/network/nginx.md#nginx和php简易交互过程)
  - [http](https://github.com/TIGERB/easy-tips/blob/master/network/http.md)
    - message
      - message head
      - message body
    - http status
    - http method
    - https
    - http2
    - websocket

- Computer basis (doing)

  - [linux command](https://github.com/TIGERB/easy-tips/blob/master/linux/command.md)
  - shell

- High concurrency (not-start)

## Test

### Design Pattern

run: php patterns/[folder-name]/test.php

```
for example,

chain of responsibility: run, php patterns/chainOfResponsibility/test.php

result：

request 5850c8354b298: token pass～
request 5850c8354b298: request frequent pass～
request 5850c8354b298: params pass～
request 5850c8354b298: sign pass～
request 5850c8354b298: auth pass～
```

### Algorithm

run: php algorithm/test.php [algorithm name｜help]

```
for example,

bubble sort: run, php algorithm/test.php　bubble

result：

==========================bubble sort=========================
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
=========up is the origin data==================below is the sort result=============
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

### Redis 

run: php redis/test.php [name｜help]

```
for example,

pessimistic-lock: run, php redis/test.php p-lock

result：

exexute count increment 1～

count value: 1

```

## Error correction

If you find some where is not right, you can make a issue[issue](https://github.com/TIGERB/easy-tips/issues)or a [pull request](https://github.com/TIGERB/easy-tips),I will fix it，THX～

## Thanks

Thanks for the issue and pull request, as follows:

- @[faynwol](https://github.com/faynwol)
- @[whahuzhihao](https://github.com/whahuzhihao)
- @[snriud](https://github.com/snriud)
- @[fhefh2015](https://github.com/fhefh2015)
- @[RJustice](https://github.com/RJustice)
- @[ooing](https://github.com/ooing)
- @[shellus](https://github.com/shellus)
- @[entimm](https://github.com/entimm)
- @[jealone](https://github.com/jealone)
- @[cugblbs](https://github.com/cugblbs)
- @[CmderQ](https://github.com/CmderQ)
- @[neetdai](https://github.com/neetdai)
- @[yangqiphp](https://github.com/yangqiphp)
- @[akmumu](https://github.com/akmumu)
- @[DukeAnn](https://github.com/DukeAnn)
- @[onlyshow](https://github.com/onlyshow)
