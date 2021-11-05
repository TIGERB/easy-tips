<!-- # Go服务监控搭建入门 | 教程 -->

## 前言

一直以来都想知道现在「Go服务监控」是如何搭建和工作的，于是最近就抽了点时间去学习下这服务监控的搭建过程。

我选用的技术栈是「prometheus + grafana」。

## 架构简介

整体的简易架构如下：

<p align="center">
  <img src="http://cdn.tigerb.cn/20210531132351.png" style="width:100%">
</p>

- Grafana：作为UI，提供了丰富的监控面板。
- Prometheus：Prometheus是一个监控&时序数据库。
- 需要被监控的服务：需要被监控的服务按照标准提供一个`metrics`接口，Prometheus会去通过暴露的这个接口拉取数据。Go已经有封装好的包`github.com/prometheus/client_golang/prometheus`，我们直接采用就可以了。

## 准备镜像

选取Prometheus镜像，如下：
```
docker pull bitnami/prometheus:2.26.0
docker run -d -p 9090:9090 bitnami/prometheus:2.26.0
```

选取Grafana镜像，如下：
```
docker pull bitnami/grafana:7.5.4
docker run -d -p 3000:3000 bitnami/grafana:7.5.4
```

Go服务demo代码镜像：

首先我们选用现有封装好的代码包，如下：
```
// 选用现有的prometheus包
go get github.com/prometheus/client_golang/prometheus
go get github.com/prometheus/client_golang/prometheus/promauto
go get github.com/prometheus/client_golang/prometheus/promhttp
```

demo代码如下：
```go
package main

import (
	"net/http"

	"github.com/prometheus/client_golang/prometheus/promhttp"
)

func main() {
	// 对外提供/metrics接口
	http.Handle("/metrics", promhttp.Handler())
	http.ListenAndServe(":2112", nil)
}
```

启动Go服务，curl请求接口：

> curl http://localhost:2112/metrics

获取到监控指标数据如下：
```
# HELP go_gc_duration_seconds A summary of the pause duration of garbage collection cycles.
# TYPE go_gc_duration_seconds summary
go_gc_duration_seconds{quantile="0"} 2.8697e-05
go_gc_duration_seconds{quantile="0.25"} 3.8094e-05
go_gc_duration_seconds{quantile="0.5"} 0.000125819
go_gc_duration_seconds{quantile="0.75"} 0.000190862
go_gc_duration_seconds{quantile="1"} 0.0098972
go_gc_duration_seconds_sum 0.025042382
go_gc_duration_seconds_count 45
......略
```

通过prometheus的配置文件`prometheus.yml`注册我们Go样例服务，如下

```yml
# 略...
scrape_configs:
  # prometheus自身的监控
  - job_name: 'prometheus'

    static_configs:
    - targets: ['prometheus:9090']

  # 重点是这里 
  # 注册我们Go服务的job
  - job_name: 'go-demo'

    static_configs:
	# go服务的地址 IP:端口
    - targets: ['go-demo:2112']
```

编写Go服务的dockerfile，如下：
```dockerfile
FROM amd64/golang:1.16.3-alpine3.12

RUN mkdir -p /home/deploy/go-demo

CMD ["/home/deploy/go-demo/run.sh"]
```

启动脚本`/home/deploy/go-demo/run.sh`，如下：
```sh
#!/bin/sh

cd /home/deploy/go-demo \
    && go run main.go \
    && sleep 100000
```

## 服务编排(docker-compose)

上面我们是单独启动每个容器来实现的，接着我们用docker-compose来编排服务。

首先，创建network:

```
// 创建network
docker network create example_default
```

接着，编写docker-compose.yaml文件如下：

```yml
version: "3"

services:
  prometheus:
    image: bitnami/prometheus:2.26.0
    container_name: prometheus
    volumes:
      - ./prometheus/prometheus.yml:/opt/bitnami/prometheus/conf/prometheus.yml
    ports:
      - "9090:9090"
    privileged: true
    networks: 
      - default
  grafana:
    image: bitnami/grafana:7.5.4
    container_name: grafana
    ports:
      - "3000:3000"
    privileged: true
    networks: 
      - default
  go-demo:
    container_name: go-demo
    build: ./go-demo
    volumes:
      - ./go-demo:/home/deploy/go-demo
    ports:
      - "2112:2112"
    privileged: true
    networks: 
      - default
networks: 
  default:
    external: 
      name: example_default
```

最后，启动服务：
```
// 启动服务
docker-compose up -d
```

## Grafana监控面板配置

首先prometheus其实自己是有监控面板的，我们可以通过下面的地址访问：

```
prometheus http://localhost:9090
```

举个例子，比如通过如下的操作，我们就可以看见我们Go服务的`Goroutines`监控。

<p align="center">
  <img src="http://cdn.tigerb.cn/20210529155608.png" style="width:100%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20210529160638.png" style="width:100%">
</p>

但是呢，Grafana提供了更丰富的监控面板，接着我们来搭建一个简单的Go服务监控。

访问如下地址：
```
使用grafana的UI
grafana http://localhost:3000
```

初始账号密码：
```
admin
admin
```

进入首页：
<p align="center">
  <img src="http://cdn.tigerb.cn/20210529160749.png" style="width:100%">
</p>

添加数据源：
<p align="center">
  <img src="http://cdn.tigerb.cn/20210529160803.png" style="width:100%">
</p>

<p align="center">
  <img src="http://cdn.tigerb.cn/20210529160812.png" style="width:100%">
</p>

选择数据源为Prometheus:
<p align="center">
  <img src="http://cdn.tigerb.cn/20210529160823.png" style="width:100%">
</p>

填写Prometheus服务的地址：
<p align="center">
  <img src="http://cdn.tigerb.cn/20210529160842.png" style="width:100%">
</p>

点击添加：
<p align="center">
  <img src="http://cdn.tigerb.cn/20210529160856.png" style="width:100%">
</p>

切换到Dashboards面板，选择导入一个面板：
<p align="center">
  <img src="http://cdn.tigerb.cn/20210529160917.png" style="width:100%">
</p>

接着我们就可以看见一个已经存在的面板了，这个面板是Prometheus自身监控的面板。
<p align="center">
  <img src="http://cdn.tigerb.cn/20210529160925.png" style="width:100%">
</p>

接着，我们来创建一个我们自己Go服务的面板，首先创建一个Go服务的目录(保证隔离和可读性，相当于namespace)：
<p align="center">
  <img src="http://cdn.tigerb.cn/20210529163049.png" style="width:100%">
</p>

创建一个Go服务的面板：
<p align="center">
  <img src="http://cdn.tigerb.cn/20210529163058.png" style="width:100%">
</p>

创建一个指标视图：
<p align="center">
  <img src="http://cdn.tigerb.cn/20210529163105.png" style="width:100%">
</p>

选择视图类型为折线统计图，选择数据指标为`go_gotoutines`:
<p align="center">
  <img src="http://cdn.tigerb.cn/20210529163114.png" style="width:100%">
</p>

只展示我们的Go样例服务数据，这里采用的是 语法：
<p align="center">
  <img src="http://cdn.tigerb.cn/20210529163122.png" style="width:100%">
</p>

以此类推，我们就创建了一些列监控视图数据，比如goroutine数量、线程数量、heap内存数据、stack内存数据、mcache数据、mspan数据、GC数据等等，如下：
<p align="center">
  <img src="http://cdn.tigerb.cn/20210529163131.png" style="width:100%">
</p>


## 总结

如上我们就成功搭建了一个入门级别的「Go服务监控」，但是这还不能够上生产环境，为什么，留下两个课后思考：

- 除了系统、性能指标外，业务指标怎么统计？
- 上面注册服务是通过人工修改的，实际我们的服务都是动态服务发现了，所以配合服务发现我们怎么注册服务到`prometheus`呢？
