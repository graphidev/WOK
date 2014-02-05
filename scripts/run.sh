#!/bin/bash

dir=$(dirname $0)

while getopts vl opt; do
   case $opt in
    v)
        php $dir/cli.php -v
        exit;
    ;;
    l)
        php $dir/cli.php -l
        exit;
    ;;
   esac
done



if [ -z "$1" ]; then
    echo "Illegal number of parameters"
    echo "  -v                  Know WOK version"
    echo "  -l                  List available scripts"
    echo "  [script] [args,..]  Run a script"
    exit;
fi

if [ -e "$dir/$1.php" ]
then
    php $dir/$1.php $2 $3 $4 $5 $6 $7 $8 $9
else
    echo "Illegal script name"
fi