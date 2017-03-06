## 常用sql语句整理：mysql


1. 增

- 增加一张表
```
CREATE TABLE `table_name`(
  ...
  )ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

- 增加记录
```
INSERT INTO `your_table_name`(`column_name`)
VALUES
('your_value_one'),
('your_value_two');
```

- 增加字段
```
ALTER TABLE `your_table_name`
ADD `your_column_name` ...
AFTER `column_name`;
```

- 增加索引
  + 主键
  ```
  ALTER TABLE `your_table_name`
  ADD PRIMARY KEY your_index_name(your_column_name);
  ```
  + 唯一索引
  ```
  ALTER TABLE `your_table_name`
  ADD UNIQUE your_index_name(your_column_name);
  ```
  + 普通索引
  ```
  ALTER TABLE `your_table_name`
  ADD INDEX your_index_name(your_column_name);
  ```
  + 全文索引
  ```
  ALTER TABLE `your_table_name`
  ADD FULLTEXT your_index_name(your_column_name);
  ```


2. 删

- 逐行删除
```
DELETE FORM `table_name`
WHERE ...;
```

- 清空整张表
```
TRUNCATE TABLE `your_table_name`;
```

- 删除表
```
DROP TABLE `your_table_name`;
```

- 删除字段
```
ALTER TABLE `your_table_name`
DROP `column_name`;
```

- 删除索引
```
ALTER TABLE `your_table_name`
DROP INDEX your_index_name(your_column_name);
```


3. 改

- 变更数据
```
UPDATE `table_name`
SET column_name=your_value
WHERE ...;
```

- 变更字段
```
ALTER TABLE `your_table_name`
CHANGE `your_column_name` `your_column_name` ...(变更);
```

- 变更字段值为另一张表的某个值
```
UPDATE `your_table_name`
AS a
JOIN `your_anther_table_name`
AS b
SET a.column = b.anther_column
WHERE a.id = b.a_id...;
```

4. 查


- 普通查询
```
SELECT `column_name_one`, `column_name_two`
FROM `table_name`;
```

- 关联查询
```
SELECT *
FROM `your_table_name`
AS a
JOIN `your_anther_table_name`
AS b
WHERE a.column_name = b.column_name...;
```

- 合计函数条件查询：WHERE 关键字无法与合计函数一起使用
```
SELECT aggregate_function(column_name)
FROM your_table_name
GROUP BY column_name
HAVING aggregate_function(column_name)...;
```

- 同一个实例下跨库查询
```
SELECT *
FROM database_name.your_table_name
AS a
JOIN another_database_name.your_another_table_name
AS b
WHERE a.column_name = b.column_name...;
```

5. 复制一张表结构
```
CREATE `your_table_name`
LIKE `destination_table_name`;
```

6. 完全复制一张表：表结构+全部数据
```
CREATE TABLE `your_table_name`
LIKE `destination_table_name`;

INSERT INTO `your_table_name`
SELECT *
FROM `destination_table_name`;
```

---

### 附录：mysql常用命令
- 登陆： mysql -h host -u username -p
- 列出数据库：SHOW DATABESES;
- 列出表:SHOW TABLES;
- 列出表结构:DESC table_name
- 使用一个数据库：USE database_name;
- 导入：source 'file';
- 导出：mysqldump -h 127.0.0.1 -u root -p "database_name" "table_name" --where="condition" > file_name.sql;
- 查看慢日志：mysqldumpslow -s [c:按记录次数排序/t:时间/l:锁定时间/r:返回的记录数] -t [n:前n条数据] -g "正则"　/path
