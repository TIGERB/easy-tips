---
title: 100元实践k8s搭建过程
tags:
  - Go
cover_index:
  http://rmq67gta1.sabkt.gdipper.com/20210718132238.png?imageMogr2/thumbnail/640x480!/format/webp/blur/1x0/quality/75|imageslim
categories:
  - k8s
date: 2022-12-03 21:00:11
---


# 前言

工作中越来越重度使用k8s，想进一步了解k8s的工作原理。一方面学习业界优秀系统设计思路，另一方面多了解也可以提高日常工作效率，比如和k8s开发的沟通效率等。今天第一步：自己着手搭建一个k8s服务。

```
本文采用的版本

kubectl kubelet kubeadm版本: 1.23.1
操作系统版本: CentOS 8.2 64位
```

## 准备工作

### 1.采购云主机

官方建议最低云主机配置**2核4G**，国内任意云厂商采购就行，作为K8S服务的宿主机。**本教程操作系统为CentOS 8.2 64位**。

```备注：官方文档标记最低配置内存要求2G，但是安装完dashboard、ingress等服务之后比较卡顿，所以为了流畅这里推荐4G内存。```

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221207092204.png" style="width:90%">
</p>

### 2.放开端口

外网放开`30000`端口，后续浏览器登陆k8s dashboard看板使用。并检查ssh服务端口22是否正常开启。

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221207091445.png" style="width:90%">
</p>

使用ssh登陆云主机，开始配置。

### 3.安装工具

安装常用工具：

```
yum install -y yum-utils device-mapper-persistent-data lvm2 iproute-tc
```

### 4.添加阿里源

国内存在墙的问题，添加阿里源加速：

```
yum-config-manager --add-repo https://mirrors.aliyun.com/docker-ce/linux/centos/docker-ce.repo
```

## 开始安装

### 1.安装社区版本docker

安装：
```
yum -y install docker-ce
```

enable：
```
systemctl enable docker
```

查看docker版本`docker version`：
<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221211200219.png" style="width:60%">
</p>

### 2.安装 kubectl kubelet kubeadm 

#### 2.1添加阿里源

```
cat <<EOF > /etc/yum.repos.d/kubernetes.repo
[kubernetes]
name=Kubernetes
baseurl=https://mirrors.aliyun.com/kubernetes/yum/repos/kubernetes-el7-x86_64/
enabled=1
gpgcheck=1
repo_gpgcheck=1
gpgkey=https://mirrors.aliyun.com/kubernetes/yum/doc/yum-key.gpg https://mirrors.aliyun.com/kubernetes/yum/doc/rpm-package-key.gpg
EOF
```

> 注意点：v1.24版本后kubernetes放弃docker，安装过程存在一些问题，**这里我们指定1.23.1版本安装**。

#### 2.2安装 1.23.1版本 kubectl kubelet kubeadm：
```
yum install -y kubectl-1.23.1 kubelet-1.23.1 kubeadm-1.23.1
```

启动kubelet：
```
systemctl enable kubelet
```

查看kubectl版本：
<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221211202333.png" style="width:90%">
</p>

#### 2.3修改`cgroupdriver`

执行如下命令：
```
cat <<EOF > /etc/docker/daemon.json
{
  "exec-opts": ["native.cgroupdriver=systemd"]
}
EOF
```

重启服务：
```
systemctl daemon-reload
systemctl restart docker
systemctl restart kubelet
```

#### 2.4替换镜像源

由于这里我们使用的是国内的云厂商，访问海外`k8s.gcr.io`拉取镜像存在墙的问题，所以下面我们就替换成`registry.cn-hangzhou.aliyuncs.com/google_containers`的地址，具体操作如下：

删除旧配置文件：
```
rm -f /etc/containerd/config.toml
```

生产默认配置文件：
```
containerd config default > /etc/containerd/config.toml
```

替换镜像地址：
```
sed -i 's/k8s.gcr.io/registry.cn-hangzhou.aliyuncs.com\/google_containers/' /etc/containerd/config.toml
```

重启`containerd`：
```
systemctl restart containerd
```

#### 2.4初始化k8s master节点

初始化命令：
```
kubeadm init --kubernetes-version=1.23.1  \
--apiserver-advertise-address=<你的云主机内网IP>   \
--image-repository registry.aliyuncs.com/google_containers  \
--service-cidr=10.10.0.0/16 --pod-network-cidr=10.122.0.0/16
```

通常会卡在这一步，如果大家按照本文的版本，理论不会报错，如果报错需要逐个搜索解决了。
<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221211205050.png" style="width:60%">
</p>

如果初始化失败，执行如下命令后再重新初始化：
```
kubeadm reset -f
```

初始化成功之后得到如下命令，加入新的node节点使用(本次不使用)：
```
kubeadm join <你的云主机内网IP>:6443 --token 78376v.rznvls130w3sgwb7 \
	--discovery-token-ca-cert-hash sha256:add03fb7de52ad73fd96626fa9d9f0d639186524ba34d24742c15fce8093b8c5
```

配置`kubectl`：
```
mkdir -p $HOME/.kube

cp -i /etc/kubernetes/admin.conf $HOME/.kube/config

chown $(id -u):$(id -g) $HOME/.kube/config
```

查看k8s服务启动状态：

```
kubectl get pod --all-namespaces
```

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221211210014.png" style="width:90%">
</p>


### 3.安装calico网络

```
kubectl apply -f https://docs.projectcalico.org/manifests/calico.yaml
```

安装完毕后，查看calico服务启动状态：

```
kubectl get pod --all-namespaces
```

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221211211436.png" style="width:90%">
</p>


### 4.安装kubernates-dashboard

#### 4.1 下载配置文件

```
wget https://raw.githubusercontent.com/kubernetes/dashboard/v2.0.0-rc7/aio/deploy/recommended.yaml
```

#### 4.2 添加nodeport

配置nodeport，外网访问dashboard：

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221211212325.png" style="width:50%">
</p>

#### 4.3 创建dashboard服务

创建：
```
kubectl apply -f recommended.yaml
```

查看kubernetes-dashboard启动状态：
```
kubectl get pod -n kubernetes-dashboard
```

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221211212432.png" style="width:90%">
</p>

#### 4.4 外网访问dashboard

浏览器打开dashboard，地址：<你的外网IP:30000>

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221208084417.png" style="width:90%">
</p>

如上图所示，因为https的问题，浏览器会提示「您的连接不是私密连接」。推荐使用chrome浏览器，并在当前页面上任意位置点击，然后键盘输入「thisisunsafe」再点击回车健即可。

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221208085817.png" style="width:90%">
</p>

#### 4.5 获取token

创建用户。`dashboard-adminuser.yaml`配置文件示例，执行如下命令直接创建，参考官方教程创建示例用户 `https://github.com/kubernetes/dashboard/blob/master/docs/user/access-control/creating-sample-user.md`：

创建配置文件：
```
cat <<EOF > dashboard-adminuser.yaml
apiVersion: v1
kind: ServiceAccount
metadata:
  name: admin-user
  namespace: kubernetes-dashboard
---
apiVersion: rbac.authorization.k8s.io/v1
kind: ClusterRoleBinding
metadata:
  name: admin-user
roleRef:
  apiGroup: rbac.authorization.k8s.io
  kind: ClusterRole
  name: cluster-admin
subjects:
- kind: ServiceAccount
  name: admin-user
  namespace: kubernetes-dashboard
EOF
```


创建用户：
```
kubectl apply -f dashboard-adminuser.yaml
```

创建成功之后提示：
```
serviceaccount/admin-user created
clusterrolebinding.rbac.authorization.k8s.io/admin-user created
```

执行如下命令获取token:

```
kubectl -n kubernetes-dashboard get secret $(kubectl -n kubernetes-dashboard get sa/admin-user -o jsonpath="{.secrets[0].name}") -o go-template="{{.data.token | base64decode}}"
```

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221213093543.png" style="width:90%">
</p>

#### 4.6 复制token登陆dashboard

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221208091332.png" style="width:90%">
</p>

到这里我们已经可以正常创建pod了，但是外网还不能直接访问到pod，虽然可以采用dashboard的`nodeport`的方案，但是`nodeport`只支持暴露30000-32767的端口，不适用于生产环境，接着我们就通过另一种方式`ingress`来对外暴露pod。

### 5. 安装ingress

#### 5.1 下载官方配置文件，这里使用的v1.3.1版本：

```
wget https://raw.githubusercontent.com/kubernetes/ingress-nginx/controller-v1.3.1/deploy/static/provider/cloud/deploy.yaml
```

#### 5.2 同样由于墙的问题，我们把配置文件中的镜像源换成阿里源：

替换`nginx-ingress-controller`镜像源：
```
sed -i 's/registry.k8s.io\/ingress-nginx\/controller:v1.3.1@sha256:54f7fe2c6c5a9db9a0ebf1131797109bb7a4d91f56b9b362bde2abd237dd1974/registry.cn-hangzhou.aliyuncs.com\/google_containers\/nginx-ingress-controller:v1.3.1/g' ./deploy.yaml
```

替换`kube-webhook-certgen`镜像源：
```
sed -i 's/registry.k8s.io\/ingress-nginx\/kube-webhook-certgen:v1.3.0@sha256:549e71a6ca248c5abd51cdb73dbc3083df62cf92ed5e6147c780e30f7e007a47/registry.cn-hangzhou.aliyuncs.com\/google_containers\/kube-webhook-certgen:v1.3.0/g' ./deploy.yaml
```

#### 5.3 创建ingress服务

创建：
```
kubectl apply -f deploy.yaml
```

查看状态：
```
kubectl get pod --all-namespaces
```

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221211213756.png" style="width:90%">
</p>

创建完成之后，查看ingress状态，为`pending`状态，原因是缺少LB，这里我们使用`metallb` 。

#### 5.4 安装metallb

执行安装命令：
```
kubectl apply -f https://raw.githubusercontent.com/metallb/metallb/v0.13.7/config/manifests/metallb-native.yaml
```

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221211214826.png" style="width:90%">
</p>

创建secret：
```
kubectl create secret generic -n metallb-system memberlist --from-literal=secretkey="$(openssl rand -base64 128)"
```

查看安装状态：
```
kubectl get ns
kubectl get all -n metallb-system
```

#### 5.4 绑定外网IP `EXTERNAL-IP`

```
kubectl get service ingress-nginx-controller --namespace=ingress-nginx
```

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221211222959.png" style="width:80%">
</p>


```
kubectl edit service ingress-nginx-controller --namespace=ingress-nginx

添加：
externalIPs:
  - 118.195.228.232
```

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221211223105.png" style="width:50%">
</p>

```
kubectl get service ingress-nginx-controller --namespace=ingress-nginx
```
<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221211223220.png" style="width:80%">
</p>

查看启动状态`kubectl get pod --all-namespaces`：
<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221211220300.png" style="width:80%">
</p>

metalab和`ingress-nginx`的状态还是`pending`，查看原因：

```
kubectl describe pod ingress-nginx-controller-6bfbdbdd64-jp7lw -n ingress-nginx
```

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221211221006.png" style="width:80%">
</p>

原因是现在只有`master`节点，还没有`node`节点，未了节省成本，这里我们允许master参与调度，把master节点也当node使用。

#### 5.5 允许master节点可以被调度

执行：
```
kubectl taint nodes --all node-role.kubernetes.io/master-
```

查看pod状态：
```
kubectl get pod --all-namespaces
```

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221211221338.png" style="width:80%">
</p>

pod均正常运行。到这里，一个基础的k8s服务基本安装完成。

## 体验k8s

### 解析域名

你的测试域名A解析到服务器的外网IP上，具体步骤略。

### 创建测试服务pod

```
kubectl create deployment demo --image=httpd --port=80

kubectl expose deployment demo
```

### 创建ingress映射

```
kubectl create ingress demo --class=nginx  --rule="k8s.tigerb.cn/*=demo:80"
```

### 测试

查看ingress服务service的外网端口

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221211224544.png" style="width:80%">
</p>

`demo`pod启动成功后访问`http://k8s.tigerb.cn:32374/`测试服务即可。

<p align="center">
  <img src="http://rmq67gta1.sabkt.gdipper.com/20221211224105.png" style="width:80%">
</p>

到此为止，我们就成功部署了一个k8s服务，使用dashborad就可以很轻松完成服务部署、扩容、缩容等。

