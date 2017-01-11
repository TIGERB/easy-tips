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
25. 杀掉进程： kill [pid]
26. 关机/重启
 - 关机：shutdown -h now
 - 关机: init 0
 - 关机: halt
 - 关机: poweroff
 - 重启: shutdown -r now
