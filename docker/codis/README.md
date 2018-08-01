
### 直接获取镜像获取
```
docker pull tigerbcode/codis:3.2-alpine3.8
```


### 完整搭建流程

1. 构建基础镜像 golang:1.10-alpine3.8
```
docker build ./
```

2. 构建基础容器
```
docker run -it -v <your_path>/easy-tips/docker/codis:/go/src/github.com/CodisLabs -p 6379:6379 -p 9090:9090--name codis golang:1.10-alpine3.8 sh
```

3. 编译codis
```shell
apk add git make gcc cmake libc-dev jemalloc linux-headers bash autoconf

mkdir -p $GOPATH/src/github.com/CodisLabs

cd $_ && git clone https://github.com/CodisLabs/codis.git -b release3.2

make MALLOC=libc

```

4. 启动codis
```shell

sh ./admin/codis-dashboard-admin.sh start
# [::]:18080

sh ./admin/codis-proxy-admin.sh start

sh ./admin/codis-server-admin.sh start
# 6379

sh ./admin/codis-fe-admin.sh start
# 0.0.0.0:9090
```