1. 主库从库创建复制账号

```sql
grant replication slave, replication client on *.* to repl@'%.%.%.%' identified by 'repl';
```

dcoker
```

```

2. my.cnf修改

master's conf/mysqld.cnf:
```
log_bin = mysql-bin
server_id = 1
```

查看主库状态：
```sql
show master status \G;
```

```
File: mysql-bin.000003
Position: 154
Binlog_Do_DB:
Binlog_Ignore_DB:
Executed_Gtid_Set:
```

slave's conf/mysqld.cnf:
```
log_bin = mysql-bin
server_id = 2
relay_log = /var/lib/mysql/mysql-relay-bin
log_slave_updates = 1
read_only = 1
```

3. 从库连接主库

```
change master to master_host='192.168.199.191',
master_port=33006,
master_user='repl',
master_password='repl',
master_log_file='mysql-bin.000003',
master_log_pos=0;
```

查看从库状态：
```sql
show slave status \G;
```

```
                  Slave_IO_State:
                  Master_Host: server1
                  Master_User: repl
                  Master_Port: 3306
                Connect_Retry: 60
              Master_Log_File: mysql-bin.000003
          Read_Master_Log_Pos: 4
               Relay_Log_File: mysql-relay-bin.000001
                Relay_Log_Pos: 4
        Relay_Master_Log_File: mysql-bin.000003
             Slave_IO_Running: No
            Slave_SQL_Running: No
              Replicate_Do_DB:
          Replicate_Ignore_DB:
           Replicate_Do_Table:
       Replicate_Ignore_Table:
      Replicate_Wild_Do_Table:
  Replicate_Wild_Ignore_Table:
                   Last_Errno: 0
                   Last_Error:
                 Skip_Counter: 0
          Exec_Master_Log_Pos: 4
              Relay_Log_Space: 154
              Until_Condition: None
               Until_Log_File:
                Until_Log_Pos: 0
           Master_SSL_Allowed: No
           Master_SSL_CA_File:
           Master_SSL_CA_Path:
              Master_SSL_Cert:
            Master_SSL_Cipher:
               Master_SSL_Key:
        Seconds_Behind_Master: NULL
Master_SSL_Verify_Server_Cert: No
                Last_IO_Errno: 0
                Last_IO_Error:
               Last_SQL_Errno: 0
               Last_SQL_Error:
  Replicate_Ignore_Server_Ids:
             Master_Server_Id: 0
                  Master_UUID:
             Master_Info_File: /var/lib/mysql/master.info
                    SQL_Delay: 0
          SQL_Remaining_Delay: NULL
      Slave_SQL_Running_State:
           Master_Retry_Count: 86400
                  Master_Bind:
      Last_IO_Error_Timestamp:
     Last_SQL_Error_Timestamp:
               Master_SSL_Crl:
           Master_SSL_Crlpath:
           Retrieved_Gtid_Set:
            Executed_Gtid_Set:
                Auto_Position: 0
         Replicate_Rewrite_DB:
                 Channel_Name:
           Master_TLS_Version:
```

4. 从库启动复制

```sql
start slave;
```

查看从库状态：
```sql
show slave status \G;
```