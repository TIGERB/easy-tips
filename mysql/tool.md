# mysql慢日志分析工具 mysqldumpslow

```
    -v          详细数据
    -d          调试模式
    -s ORDER    what to sort by (al, at, ar, c, l, r, t), 'at' is default
                al: 平均上锁的时间
                ar: 平均查询的行数
                at: 平均查询的时间
                c: 数量
                l: 锁时间
                r: 行数
                t: 查询时间
    -r          倒序
    -t NUM      前n条数据
    -a          不转化数字为N,字符串为S 比如查询条件里的数字 反之where id = 1 会被转化为 where id = N
    -n NUM      abstract numbers with at least n digits within names
    -g PATTERN  正则查询
    -h HOSTNAME hostname of db server for *-slow.log filename (can be wildcard),
                default is '*', i.e. match all
    -i NAME     name of server instance (if using mysql.server startup script)
    -l          don't subtract lock time from total time

```