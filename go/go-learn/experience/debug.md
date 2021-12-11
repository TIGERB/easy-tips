# 线上CPU负载异常排查&Go服务内存泄露排查

## 线上CPU负载异常排查

```
docker pull centos:centos8

docker run -it --privileged -p 80:80 centos:centos8

yum install -y nginx perf
yum install -y perf
```

```
1. 安装：
yum install perf

2. 采样：
perf record -F 99 -p 6 -g sleep 10
释义：
-F 频率 每秒采样多少次
-p 进程 进程id
-g 记录调用栈
sleep 采样多少秒

3. 分析采样结果
perf report -n --stdio

top函数查看
perf top -p 6 -g

```

## Go服务内存泄露排查

```
curl -s http://127.0.0.1:8888/debug/pprof/heap > 1.heap

curl -s http://127.0.0.1:8888/debug/pprof/heap > 2.heap

go tool pprof --http :9090 --base 1.heap 2.heap
```