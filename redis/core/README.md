## Redis底层数据结构

|数据结构|
|------|
|简单动态字符串(sds)|
|双端链表(linkedlist)|
|字典(dict)|
|跳跃表(skiplist)|
|整数集合(intset)|
|压缩列表(ziplist)|

### 简单动态字符串

SDS结构
```go

```

## Redis底层数据对象

```

+----------+
|   type   |
+----------+
| encoding |
+----------+
|   ptr    |
+----------+

```

### 字符串对象

编码类型|对应Redis底层数据结构
------|------
int|C的long int
embstr|emb编码的sds
raw|sds

### 列表对象

编码类型|对应Redis底层数据结构
------|------
ziplist|压缩列表
linkedlist|双端链表

### 哈希对象

编码类型|对应Redis底层数据结构
------|------
ziplist|压缩列表
hashtale|dict字典

### 集合对象

编码类型|对应Redis底层数据结构
------|------
intset|整数集合
hashtale|dict字典

### 有序集合对象

编码类型|对应Redis底层数据结构
------|------
ziplist|压缩列表
skiplist|跳跃表

