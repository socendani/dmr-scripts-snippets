#!/bin/sh

LIST="/home/dani/Dropbox/dmr-github/dmr-scripts-snippets/php-cli"
CONFIG="/usr/bin/php "

for i in $LIST
do
    ${CONFIG}${i}/dmr_videos.php 
done