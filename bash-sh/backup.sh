#!/bin/bash
# @author: Dani Morte
# @original:
# @Version: 1.0
# @Script: backup.sh
# @Uses: with cron
#
REAL=false    #si REAL=true.. RUN !!!
BACKUP_MYSQL=true
BACKUP_PHP=true
BACKUPDIR_MYSQL=/home/dani/Dropbox/danibck/mysql
BACKUPDIR_PHP=/home/dani/Dropbox/danibck/htdocs

if ( $BACKUP_MYSQL ); then
    HOST=localhost
    USER=root
    PWD=root
    BORRAR=3      #Borrar los ficheros anteriores a 7 dias
    DUMP="/usr/bin/mysqldump --skip-extended-insert --force -u $USER -p$PWD  "
    MYSQL=/usr/bin/mysql
    TODAY="$(date +"%Y-%m-%d")"   #Today_old=$(date "+%a")
    DATABASES=$(echo "SHOW DATABASES" | $MYSQL -u $USER -p$PWD -h $HOST)

    for db in $DATABASES; do
            date=`date`
            file="$BACKUPDIR_MYSQL/$TODAY-$HOST-$db.sql.gz"
            echo "Backing up '$db' from '$HOST' on '$date' to:  $file"
            #echo "   $file"
            if ( $REAL ); then
                #Hacemos el DUMP
                $DUMP -h $HOST $db | gzip > $file
                #Borramos los anteriores ficheros
                find $BACKUPDIR_MYSQL -type f -mtime +$BORRAR -exec rm -f {} \;
            fi

    done

fi


if ( $BACKUP_PHP ); then
     TODAY="$(date +"%Y-%m-%d")"
     HTDOCS="/home/dani/Dropbox/daniphp"
     INCLUIR="db1 db2 portfolio"
     BORRAR=7      #Borrar los ficheros anteriores a 7 dias
     date=`date`
     for x in $INCLUIR; do
        file="$BACKUPDIR_PHP/$TODAY-$x.tar.gz"
        echo "Backing up '$HTDOCS/$x' to:  $file"
        if ( $REAL ); then
            #comprimimos
            tar -czPf  $file  "$HTDOCS/$x";
            #Borramos los anteriores ficheros
            find $BACKUPDIR_PHP  -type f -mtime +$BORRAR -exec rm -f {} \;
         fi
     done
fi
