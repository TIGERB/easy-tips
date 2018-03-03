# 阻塞io，非阻塞io，同步io，异步io

io过程：
1. 内核准备数据的阶段
2. 内核向用户空间拷贝数据阶段

### 阻塞

client阻塞block到 内核准备数据的阶段

### 非阻塞

client在 内核准备数据的阶段 未阻塞

### 同步io

client 持续阻塞到 内核准备数据的阶段 和 内核向用户空间拷贝数据阶段 完成

### 异步io

内核准备数据的阶段(内核直接return调和开始此阶段) 和 内核向用户空间拷贝数据阶段(数据拷贝完成 发送信号给client) 均未发生阻塞


# select, poll, epoll

io多路复用, 阻塞形io, select、poll采用的轮询的方式，epoll采用回调的方式

### select

### poll

### epoll
