#!/bin/bash

php_file="/etc/php5/apache2/php.ini"


usage(){
	echo -e "usage:\nphpiniconf -g (--get-var) parámetro\nphpiniconf -s (--set-var) parámetro valor\nphpiniconf --pmb\nphpiniconf -h (--help)\nphpiniconf --restore "
}

do_get_variable(){
	valor=$(sed -ne "/^\[PHP\]/,/^\[/s%^$1[^=]\+=[[:space:]]\+%%p" "$php_file")
	echo $valor
	return 0	
}

do_set_variable(){
	valor=$(sed -ne "/^\[PHP\]/,/^\[/s%^$1[^=]\+=[[:space:]]\+%%p" "$php_file")
	origen=$1" = "$valor
	destino=$1" = "$2
	echo "=================================================="
	echo "Se ha cambiado ..... " $origen
	echo "Por el valor........ " $destino
	echo "=================================================="
	sed -i 's/'"$origen"'/'"$destino"'/' "$php_file"
	return 0
}
#main

if [[ $EUID -ne 0 ]]; then
   echo "You must be root my friend" 1>&2
   exit 1
fi

case "$1" in
	"-g"|"--get-var")
		if [ $# -ne 2 ]; then
			echo "Parameters number error"
			exit 1
		fi
		do_get_variable $2
		;;
	"-s"|"--set-var")
		if [ $# -ne 3 ]; then
			echo "Parameters number error"
			exit 1
		fi
		do_set_variable $2 $3
		systemctl restart apache2-lliurex
		;;
	"--pmb")
		do_set_variable upload_max_filesize 200M
		do_set_variable max_execution_time 150
		do_set_variable post_max_size 200M
		systemctl restart apache2-lliurex
		;;
	"--restore")
		do_set_variable upload_max_filesize 20M
		do_set_variable max_execution_time 30
		do_set_variable post_max_size 20M
		systemctl restart apache2-lliurex
		;;
	*)	usage
		;;
esac

exit 0

