### 锁
- 乐观锁：
  update table table_name
  set column_name = value, version=version+1
  and
  where version = version;
- 悲观锁：update table table_name set column_name = value for update;
- 共享锁：x锁

### 事务
如果一个数据库声称支持事务的操作，那么该数据库必须要具备以下四个特性：

- 原子性（Atomicity）
- 一致性（Consistency）
- 隔离性（Isolation）
- 持久性（Durability）

以上是事务的四大特性(简称ACID)，其中事务的隔离性，
当多个线程都开启事务操作数据库中的数据时，数据库系统要能进行隔离操作，
以保证各个线程获取数据的准确性，如果不考虑事务的隔离性，会发生的几种问题：

- 脏读：一个事务内修改了数据，另一个事务读取并使用了这个数据；
- 幻读：一个事务内修改了涉及全表的数据，另一个事务往这个表里面插入了新的数据，第一个事务出现幻读；
- 不可重复读：一个事务内连续读了两次数据，中间另一个事务修改了这个数据，导致第一个事务前后两次读的数据不一致；
- 更新丢失：一个事务内变更了数据，另一个事务修改了这个数据，最后前一个事务commit导致另一个事务的变更丢失；

### MySQL数据库为我们提供的四种隔离级别

- Read Uncommitted（读取未提交内容）
- Read Committed（读取提交内容）
- Repeatable Read（可重读）Mysql默认
- Serializable（可串行化）


| 隔离级别 | 脏读 | 不可重复读 | 幻读 |
| --- | --- | --- | --- |
| Read Uncommitted | √ | √  | √ |
| Read Committed | × | √ | √ |
| Repeatable Read | × | × | √ |
| Serializable | × | × | × |


### 索引

- 主键唯一索引
- 唯一索引
- 普通索引
- 联合索引
- 聚簇索引
- 非聚簇索引
