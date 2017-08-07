#!/bin/bash
# Demo-menu shell script
## ----------------------------------
# Define variables
# ----------------------------------
# EDITOR=nano
# PASSWD=/etc/passwd
RED='\033[0;41;30m'
STD='\033[0;0;39m'
PHP_SCRIPTS="/home/dani/Dropbox/dmr-github/dmr-scripts-snippets/php-cli"
PHP_BIN="/usr/bin/php "

# function to display menus
show_menus() {
	clear
	echo "~~~~~~~~~~~~~~~~~~~~~"
	echo "  Menu - socendani   "
	echo "~~~~~~~~~~~~~~~~~~~~~"
	echo "1. Reducir peli de internet (convert mp4 + reduce)"
	echo "2. Ordenar fotos-videos en carpetas"
	echo "3. Join Recursivo de videos (nivel de DIA)"
	echo "4. Join videos del directorio actual"
	echo "5. Rename Recursive. Fotos y Videos renombradas"
	echo " "
	echo "0. Exit !!"
	echo " "
}



# Lee la accion sobre el teclado y la ejecuta.
# Invoca el () cuando el usuario selecciona 1 en el menú.
# Invoca a los dos () cuando el usuario selecciona 2 en el menú.
# Salir del menu cuando el usuario selecciona 3 en el menú.
read_options(){
	local choice
	read -p "Enter choice [1 - 0] " choice
	case $choice in
		1) reduce ;;
		2) ordenar ;;
		3) join_recursive ;;
		4) join_videos_folder ;;
		5) renombrar ;;
		0) exit 0;;
		*) echo -e "${RED}Error, para salir pulsa (0)...${STD}" && sleep 1
	esac
}

# ----------------------------------------------
# Trap CTRL+C, CTRL+Z and quit singles
# ----------------------------------------------
trap '' SIGINT SIGQUIT SIGTSTP



# ----------------------------------
# User defined function
# ----------------------------------
pause(){
  read -p "Press [Enter] key to continue..." fackEnterKey
}

renombrar(){
	${PHP_BIN}${PHP_SCRIPTS}/dmr/rename.php
    pause
}

ordenar(){
	${PHP_BIN}${PHP_SCRIPTS}/dmr/ordenar.php
    pause
}

join_recursive(){
	${PHP_BIN}${PHP_SCRIPTS}/dmr/join_recursive.php
    pause
}
join_videos_folder(){
	${PHP_BIN}${PHP_SCRIPTS}/dmr/join_videos_folder.php
    pause
}

reduce(){
	${PHP_BIN}${PHP_SCRIPTS}/dmr/reduce.php
    pause
}



# -----------------------------------
# Main logic - infinite loop
# ------------------------------------
while true
do

	show_menus
	read_options
done

