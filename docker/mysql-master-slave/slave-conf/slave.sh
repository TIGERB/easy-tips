#!/bin/bash
#         
# Author TIGERB <tigerb.cn>

mysql -uroot -pmysql-slave -e "grant replication slave, replication client on *.* to repl@'%.%.%.%' identified by 'repl';"

mysql -uroot -pmysql-slave -e "change master to master_host='192.168.199.191',
master_port=33006,
master_user='repl',
master_password='repl',
master_log_file='mysql-bin.000003',
master_log_pos=0;"

mysql -uroot -pmysql-slave -e "start slave;"