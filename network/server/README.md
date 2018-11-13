# 网络编程学习思路

```
1. 原生php实现http协议 -> 掌握tcpdump的使用 -> 深刻理解tcp连接过程
2. 原生php实现多进程webserver
    2.1 引入I/O多路复用
    2.2 引入php协程(yield)
    2.3 对比 I/O多路复用版本 和 协程版本的性能差异

3. 实现简单的go web框架

4. php c扩展实现简单的webserver
```