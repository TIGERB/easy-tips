#!/bin/bash
#         
# Author TIGERB <tigerb.cn>

mysql -uroot -pmysql-master -e "grant replication slave, replication client on *.* to repl@'%.%.%.%' identified by 'repl';"