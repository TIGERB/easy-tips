### linux 常用命令

1. 切换目录：cd [目录]
2. 查看当前目录文件：ls -a[查看所有文件包括隐藏]/-l[查看文件显示权限和所属]
3. 查看当前所在路径: pwd
4. 复制文件或者文件夹：cp [filename/-r folder]
5. 远程复制文件或者文件夹：
  - 复制本地到远程： scp [-r] local_path username@ip:path
  - 复制远程到本地： scp [-r] username@ip:path local_path
6. 移动或重命名文件或文件夹： mv [file/folder]
7. 创建文件夹： mkdir [folder_name];
8. 变更文件或文件夹权限：chmod [-R:遍历文件夹下所有文件] [权限] [file/folder]
  - 解释： 例如权限为777 代表 user/group/other 的权限为 4+2+1/4+2+1/4+2+1，
  4代表read读权限， 2代表写权限， 1代表执行权限
  - drwxr--r--中的第一位: d代表文件夹，s代表socket文件，-代表普通文件，l代表软链
9. 变更文件所属用户或用户组： chown owner:group [file/folder]
10. 新建文件：
 - touch [filename]
 - vi/vim [filename]
11. 查看文件：
 - 输出文件内容：cat [filename]
 - tail [-f:实时输出文件内容] [filename]
 - less
12. 查找内容：
 - grep [正则]
 - awk
13. 建立软链： ln -s [realpath/filename] [realpath]
14. 查看包含所有用户的进程：ps -aux
15. 查看端口： netstat -anp
 - a代表：显示所有，默认不显示LISTEN的
 - n代表：不显示数字别名
 - p代表：显示关联的程序
16. 压缩
 - 解压缩：tar -zxvf [filename]
 - 压缩：tar -zcvf [filename]
17. 查看当前命令所在的路径: which
18. 查看当前用户
  - who
  - whoami
19. 查看当前系统运行多长时间：uptime
20. 可读性好的查看磁盘空间：df -h
21. 可读性好的查看文件空间：du -f --max-depth=[遍历文件夹的深度] [file/folder]
22. debian添加软件源：apt-add-repository [源]
23. 查找文件：
 - find [path] -name  [filename]
 - find [path] -user  [owername]
 - find [path] -group [groupname]
24. 删除文件或者文件夹： rm [-r] [file/folder]
25. 进程：
 - 杀掉进程：kill [pid]
 - 查看进程
        * 查看：ps -aux
        * 查看父进程ID(ppid)：ps -ef
26. 关机/重启
 - 关机：shutdown -h now
 - 关机: init 0
 - 关机: halt
 - 关机: poweroff
 - 重启: shutdown -r now reboot

27. 我的常用tmux系列命令

```
新建一个会话：
tmux new -s <会话名称>
切到一个会话：
tmux at  -t <会话名称>
删除一个会话：
tmux kill-session -t <会话名称>
获取会话列表：
tmux list
临时切换一个窗口到最大或最小：
prefix z
推出tmux但是保存会话：
prefix d
创建一个窗口:
prefix c
垂直拆分一个窗口：
prefix %
水平拆分一个窗口：
prefix "
```

28. logrotate

增加配置/etc/logrotate.d:

nginx示例文件
```
/var/log/nginx/*.log {
        # 打包日志频率 daily:每天 weekly:每周 monthly:每月
        daily
        # 打包文件添加日期后缀
        dateext
        # 找不到日志也ok
        missingok
        # 保存14份日志
        rotate 14
        # 压缩日志 默认gzip
        compress
        # 延时压缩到下次rotate
        delaycompress
        # 忽略空日志
        notifempty
        # ？
        create 0640 www-data adm
        # 执行完所有rotate再执行脚本
        sharedscripts
        # ?
        prerotate
                if [ -d /etc/logrotate.d/httpd-prerotate ]; then \
                        run-parts /etc/logrotate.d/httpd-prerotate; \
                fi \
        endscript
        # ？
        postrotate
                invoke-rc.d nginx rotate >/dev/null 2>&1
        endscript
}
```

#### 强制执行：
logrotate -f /etc/logrotate.d/nginx


#### 附录：shell 判断文件
```
-e 文件名	如果文件存在则为真
-d 文件名	如果文件存在且为目录则为真
```

29. supervisor

#### 安装 debian:
sudo apt-get install supervisor

#### 增加配置文件：
cd /etc/supervisor/conf.d

#### 配置文件示例：
```
[program:demo]
# ？
directory = yourpath
# 启动进程的命令
command = yourcommand
# 启动supervisor时启动
autostart = true
# 进程exit自动重启
autorestart = true
# 执行命令的用户
user = www-data
# 日志路径
stdout_logfile = /var/log/supervisor/demo.log
# 这个no意思是启动例如nginx或者php-fpm时，由supervior接管守护
daemonize = no
```

#### 启动或重启supervisor
sudo service supervisor start
sudo service supervisor restart

#### 启动我们的进程
sudo supervisorctl start demo

30. 查找文件命令  

#### 查找文件位置  
whereis 文件名   或者是 find / -name 文件名

#### 查找文件夹位置
locate 文件夹名

31. linux用户相关

#### 用户相关

```
# 新建用户组
groupadd groupname

# 查看当前用户分组
groups accountname

# 新建用户
useradd -g ubuntu -G sudo -d /home/accountname -m accountname

-g: 用户所属用户分组
-G: 所属其他分组
-d: 指定用户目录
-m: 配合-d使用，如果不存在-d目录则创建

# 修改用户信息
usermod -G plugindev accountname

# 删除
userdel accountname

# 修改密码
passwd accountname
```
