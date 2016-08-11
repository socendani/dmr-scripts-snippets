#!bin/sh
/usr/bin/mysqldump -A -u root -proot | gzip > ~/Dropbox/danibck/mysqldump_TOTAL_`date +%m_%d_%y`.gz
