#!/bin/bash
# @author: Dani Morte
# @original: 
# @Version: 1.0
# @Script: backup.sh
# Programa que arrancara diversas aplicaciones de esta máquina
# En la parte inferior existen varias técnicas

echo "DMR-scripts de Arranque"
echo "-----------------------"
export DISPLAY=:0
# piding
SERVICE='pidgin'
#if pidof -x $SERVICE; then echo "$SERVICE ya está funcionando.. ok"; else  echo ".. Arrancando $SERVICE"; $SERVICE; fi


# google desktop
SERVICE='gdl_box'
#if pidof -x $SERVICE; then echo "$SERVICE ya está funcionando.. ok"; else  echo ".. Arrancando $SERVICE"; /opt/google/desktop/bin/gdlinux start; fi


# wally = change background desktop
#SERVICE='wally'
#if pidof -x $SERVICE; then echo "$SERVICE ya está funcionando.. ok"; else  echo ".. Arrancando $SERVICE"; /usr/bin/wally; fi


# conky doble
SERVICE='conky'
if pidof -x $SERVICE; then echo "$SERVICE ya está funcionando.. ok"; else  echo ".. Arrancando $SERVICE"; conky -c ~/.conkyrc2 -q -d; conky -c ~/.conkyrc -q -d; fi

echo "Delay de 20 segundos..."
sleep 20
echo "Puedes cerrar la ventana"

######################  TECNICAS  ##################

#ejecutar pidgin
#SERVICE='pidgin'
#if ps ax | grep -v grep | grep $SERVICE > /dev/null
#then
#    echo "$SERVICE service running, everything is fine"
#else
#    echo "$SERVICE is not running"
#    $SERVICE
#fi



#ejecutar conky
#conky -c ~/.conkyrc;
#conky -c ~/.conkyrc2;


#google desktop
#opt/google/desktop/bin/gdlinux start


#!/bin/bash

#Check if Transmission is running; If running then do nothing; If not then start
#export DISPLAY=:0
#cd /usr/bin/
#if pidof -x transmission; then exit; else transmission -m; fi

#otro:
#pgrep ktorrent &> /dev/null
#if  [ $? -eq 0 ]
#then
#   echo "ktorrent allready running!"
#else
#   echo "ktorrent not running... Restarting..."
#fi

