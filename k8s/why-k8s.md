# 为什么需要k8s?

## 前言

目前对k8s的一期学习规划如下：

- [实践k8s搭建(已完成，点击查看)](https://tigerb.cn/2022/12/03/k8s/install/)
- 了解k8s前世今生(本文)
- 由点到面认识k8s架构
- 由面到点深入k8s架构

今天开始逐步去了解k8s前世今生，本文结构如下：

- 物理机以及存在的问题
- 虚拟主机以及存在的问题
- docker诞生
- docker存在的问题

## 物理机以及存在的问题

直接使用物理机部署业务服务：

部署方式|问题|
------|------
独立部署业务服务|资源利用率低
混合部署业务服务|耦合/互相影响


## 虚拟主机以及存在的问题

物理机通过虚拟化技术，可以虚拟出多台虚拟主机，即提升了物理设备的利用率又达到了隔离的目的。

<p align="center">
  <img src="https://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/why%20k8s/%E8%99%9A%E6%9C%BA-multi.png" style="width:50%">
</p>

但是虚拟硬件 + 虚拟操作系统不够轻量，于是诞生了`docker`。

## `docker`诞生

`docker`如何解决隔离问题，依赖Linux核心能力`Namespace`实现：
+ 进程隔离
+ 网络隔离
+ 文件隔离
+ 用户隔离
+ 等等

依赖Linux核心能力`Control Group`实现：资源隔离/限制。

使用`docker`部署的业务应用直接运行在宿主机上，更加的轻量：

<p align="center">
  <img src="https://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/why%20k8s/docker.png" style="width:50%">
</p>

虚拟主机和`docker`对比图：

<p align="center">
  <img src="https://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/why%20k8s/%E8%99%9A%E6%9C%BA%20VS%20docker%20.png" style="width:70%">
</p>

## `docker`存在的问题

但是面对复杂的业务的场景，直接使用`docker`仍然存在如下问题：

### 容器和宿主机管理问题

> 单宿主机上N个容器如何管理？

<p align="center">
  <img src="https://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/why%20k8s/%E5%8D%95%E5%AE%BF%E4%B8%BB%E6%9C%BA%E4%B8%8AN%E4%B8%AA%E5%AE%B9%E5%99%A8%E5%A6%82%E4%BD%95%E7%AE%A1%E7%90%86%EF%BC%9F.png" style="width:60%">
</p>

> 一个容器集群N个宿主机如何管理？

<p align="center">
  <img src="https://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/why%20k8s/%E5%8D%95%E5%AE%BF%E4%B8%BB%E6%9C%BA%E4%B8%8AN%E4%B8%AA%E5%AE%B9%E5%99%A8%E5%A6%82%E4%BD%95%E7%AE%A1%E7%90%86%EF%BC%9F.png" style="width:60%">
</p>

### 如何实现负载均衡

> 一个业务应用对应多个容器如何实现负载均衡？

<p align="center">
  <img src="https://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/why%20k8s/%E4%B8%80%E4%B8%AA%E4%B8%9A%E5%8A%A1%E5%BA%94%E7%94%A8%E5%AF%B9%E5%BA%94%E5%A4%9A%E4%B8%AA%E5%AE%B9%E5%99%A8%E5%A6%82%E4%BD%95%E5%AE%9E%E7%8E%B0%E8%B4%9F%E8%BD%BD%E5%9D%87%E8%A1%A1%EF%BC%9F.png" style="width:80%">
</p>

### 新创建的容器如何调度

> 创建一个容器该创建在哪台宿主机上？

<p align="center">
  <img src="https://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/why%20k8s/%E5%88%9B%E5%BB%BA%E4%B8%80%E4%B8%AA%E5%AE%B9%E5%99%A8%E8%AF%A5%E5%88%9B%E5%BB%BA%E5%9C%A8%E5%93%AA%E5%8F%B0%E5%AE%BF%E4%B8%BB%E6%9C%BA%E4%B8%8A%EF%BC%9F.png" style="width:80%">
</p>

### 如何达到高可用

> 单个容器挂了如何自动重启？

<p align="center">
  <img src="https://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/why%20k8s/%E9%AB%98%E5%8F%AF%E7%94%A8-%E5%8D%95%E4%B8%AA%E5%AE%B9%E5%99%A8%E6%8C%82%E4%BA%86%E5%A6%82%E4%BD%95%E8%87%AA%E5%8A%A8%E9%87%8D%E5%90%AF%EF%BC%9F.png" style="width:80%">
</p>

> 单个宿主机挂了如何自动剔除？

<p align="center">
  <img src="https://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/why%20k8s/%E9%AB%98%E5%8F%AF%E7%94%A8-%E5%8D%95%E4%B8%AA%E5%AE%BF%E4%B8%BB%E6%9C%BA%E6%8C%82%E4%BA%86%E5%A6%82%E4%BD%95%E8%87%AA%E5%8A%A8%E5%89%94%E9%99%A4%EF%BC%9F.png" style="width:80%">
</p>

> 如何实现自动按需伸缩容器数量？

<p align="center">
  <img src="https://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/why%20k8s/%E5%A6%82%E4%BD%95%E5%AE%9E%E7%8E%B0%E8%87%AA%E5%8A%A8%E6%8C%89%E9%9C%80%E4%BC%B8%E7%BC%A9%E5%AE%B9%E5%99%A8%E6%95%B0%E9%87%8F%EF%BC%9F.png" style="width:70%">
</p>

> 如何实现平滑发布？

<p align="center">
  <img src="https://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/why%20k8s/%E9%AB%98%E5%8F%AF%E7%94%A8-%E5%A6%82%E4%BD%95%E5%AE%9E%E7%8E%B0%E5%B9%B3%E6%BB%91%E5%8F%91%E5%B8%83%EF%BC%9F.png" style="width:70%">
</p>

## 权限如何管理

> 不同团队或租户权限如何管理？

<p align="center">
  <img src="https://blog-1251019962.cos.ap-beijing.myqcloud.com/qiniu_img_2022/why%20k8s/%E4%B8%8D%E5%90%8C%E7%BB%84%E7%BB%87%E6%88%96%E7%A7%9F%E6%88%B7%E6%9D%83%E9%99%90%E5%A6%82%E4%BD%95%E7%AE%A1%E7%90%86%EF%BC%9F.png" style="width:70%">
</p>

## 总结

综上所述，直接使用`docker`部署服务会存在以下问题，这也就是`k8s`要解决的问题：

- 容器和宿主机管理问题
    + 单宿主机上N个容器如何管理？
    + 一个容器集群N个宿主机如何管理？
- 如何实现负载均衡
    + 一个业务应用对应多个容器如何实现负载均衡？
- 新创建的容器如何调度
    + 创建一个容器该创建在哪台宿主机上？
- 如何达到高可用
    + 单个容器挂了如何自动重启？
    + 单个宿主机挂了如何自动剔除？
    + 如何实现自动按需伸缩容器数量？
    + 如何实现平滑发布？
- 权限如何管理
    + 不同团队或租户权限如何管理？

所以，为什么需要k8s，你理解了吗？




