```
这可能会出现一个问题 Get http://localhost:10248/healthz: dial tcp 127.0.0.1:10248: connect: connection refused.
解决方式

解决方法

[root@k8s-node2 ~]# vim /etc/docker/daemon.json

{
"exec-opts": ["native.cgroupdriver=systemd"],
}
```

```md
kubeadm join 10.206.0.15:6443 --token nzk9iq.kplkpfr497mfrszy \
        --discovery-token-ca-cert-hash sha256:4cc3d99a8d5345f3a62ada878df287d14daa6d11a2b93744776e449ed9832e04
```

```md
[root@k8s-01 ~]# containerd config default > /etc/containerd/config.toml

--config,-c可以在启动守护程序时更改此路径
配置文件的默认路径位于/etc/containerd/config.toml
默认情况下k8s.gcr.io无法访问，所以使用我提供的阿里云镜像仓库地址即可
sed -i 's/k8s.gcr.io/registry.cn-hangzhou.aliyuncs.com\/google_containers/' /etc/containerd/config.toml
（此处应该改为手动vim修改配置文件，搜索k8s.gcr.io，修改为registry.cn-hangzhou.aliyuncs.com/google_containers）
```


```md
获取：<your node name>：kubectl get node

获取<taint name> ：kubectl describe node <your node name> | grep Taints

移除taint：kubectl taint node <your node name> <taint name>
```

获取dashborad token:
```
<!-- 查看所有角色 -->
kubectl get role -n kube-system

kubectl get secret -n kubernetes-dashboard

创建示例用户: https://github.com/kubernetes/dashboard/blob/master/docs/user/access-control/creating-sample-user.md

生成token: 
kubectl -n kubernetes-dashboard create token admin-user
```


获取token 方式3:

kubectl get secret -n kubernetes-dashboard

kubectl describe secrets -n kubernetes-dashboard kubernetes-dashboard-token-wvr8b  | grep token | awk 'NR==3{print $2}'


重启集群
```
systemctl restart docker
```

118.195.228.232（公）
10.206.0.4（内）

kubeadm reset -f

journalctl -u kubelet 
systemctl status kubelet

安装ingress
```
kubectl apply -f https://raw.githubusercontent.com/kubernetes/ingress-nginx/controller-v1.3.1/deploy/static/provider/cloud/deploy.yaml

https://kubernetes.github.io/ingress-nginx/deploy/#quick-start
```

```
当Pod状态是 ImagePullBackOff 表示和镜像下载有关错误，可以通过 kubectl describe pod <pod-id> 来查看原因

kubectl describe pod <pod id> -n <namespace>
```

docker pull registry.cn-hangzhou.aliyuncs.com/google_containers/kube-webhook-certgen:v1.3.0


- 删除pod `kubectl delete -n <namespace name> deployment <pod name>`

kubectl get all -n ingress-nginx

kubectl logs ingress-nginx-admission-patch-drzpw -n ingress-nginx

source<(kubectl completion bash)

kubectl create ingress demo --class=nginx \
  --rule="k8s.tigerb.cn/*=demo:80"


kubectl get -A ValidatingWebhookConfiguration

kubectl create ingress demo --class=nginx   --rule="k8s.tigerb.cn/*=demo:80"


https://github.com/danderson/metallb


---

`dashboard-adminuser.yaml`配置文件示例：

```
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
```

官方教程创建示例用户: https://github.com/kubernetes/dashboard/blob/master/docs/user/access-control/creating-sample-user.md

创建用户：
kubectl apply -f dashboard-adminuser.yaml

创建成功之后提示：
```
serviceaccount/admin-user created
clusterrolebinding.rbac.authorization.k8s.io/admin-user created
```

chrome浏览器访问 `https://外网IP:30000`

浏览器 thisisunsafe 回车

生成token: 
kubectl -n kubernetes-dashboard create token admin-user

kubectl create token admin-user -n kubernetes-dashboard