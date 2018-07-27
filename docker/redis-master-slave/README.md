# 搭建redis主从环境

> cd /easy-tips/docker/redis-master-slave

执行下面命令，提供如下两种方式:

### 使用 docker-compose 编排

```bash
# 创建容器和启动容器
docker-compose up
# 创建容器和启动容器 并后台运行
docker-compose up -d
# 删除容器
docker-compose kill
```

### 手动创建和启动容器

```bash
docker build ./

docker run --name redis-master -p 60370:6379 -d redis:4-alpine <yourpath>/easy-tips/docker/redis-master-slave/redis.master.conf:/home/redis/redis.conf -d redis:4-alpine redis-server /home/redis/redis.conf

docker run --name redis-slave -p 60371:6379 -v <yourpath>/easy-tips/docker/redis-master-slave/redis.slave.conf:/home/redis/redis.conf -d redis:4-alpine redis-server /home/redis/redis.conf
```