 #!/bin/bash
sqlfile=source.sql         # 执行SQL文件名
hostname=10.128.68.32 # 数据库地址
user=root          # 数据库账号
port=3306          # 数据库端口
pwd=2wsx@WSX   # 数据库密码
mysql  -h $hostname -P$port -u$user -p$pwd -e "source /home/db/pro/now/${sqlfile}"
