#!/usr/bin/env bash

#sudo apt-get install exif
#http://www.nicnocquee.com/2016/01/13/organize-photos-via-command-line.html
#https://manurevah.com/blah/en/p/a-script-for-unloading-photos

#modificado por socendani (Juliol 2017)


#Image timestamp : 2016:09:01 15:57:36

BASEDIR="$(pwd)";

function readImage () {
  #echo $(pwd);
echo $2;
  if [ "$1" == ".xxx" ] ; then
          #  BASEDIR=".";
            #BASEDIR="/home/dani/aTEMP/kk";

          filename="$1";
          extension="${filename##*.}"
          filename="${filename%.*}"
          extension=${extension,,}  #para convertir en lowercase.
          #echo $filename;

          RANDOMNUMBER=$(( ( 999+RANDOM % 1000 )  + 1 ));
          YEAR=`exiv2 "$1" 2>/dev/null |grep timestamp|cut -d " " -f 4|cut -c 1-4`
          MONTH=`exiv2 "$1" 2>/dev/null |grep timestamp|cut -d " " -f 4|cut -c 6,7`
          DAY=`exiv2 "$1" 2>/dev/null |grep timestamp|cut -d " " -f 4|cut -c 9,10`
          HOUR=`exiv2 "$1" 2>/dev/null |grep timestamp|cut -d " " -f 5|cut -c 1,2`
          MINUTE=`exiv2 "$1" 2>/dev/null |grep timestamp|cut -d " " -f 5|cut -c 4,5`
          SECOND=`exiv2 "$1" 2>/dev/null |grep timestamp|cut -d " " -f 5|cut -c 7,8`

          trimmed=${BASEDIR}/${YEAR}/${YEAR}.${MONTH}/${YEAR}.${MONTH}.${DAY}
          WHEREIGO=${BASEDIR}/${YEAR}/${YEAR}.${MONTH}/${YEAR}.${MONTH}.${DAY}/${YEAR}.${MONTH}.${DAY}.${HOUR}.${MINUTE}.${SECOND}_${RANDOMNUMBER}.$extension

          #DIRTOMAKE=`identify -verbose "$1" | grep exif:DateTime: | sed 's/exif\:DateTime\: //g' | sed 's/\:[0-9][0-9] [0-9][0-9]\:[0-9][0-9]\:[0-9][0-9]//g' | sed 's|:|/|g'`
          #trimmed=`echo "$DIRTOMAKE" | xargs`
          #trimmed=$WHEREIGO;
          if [ -z "$trimmed" ] ; then
              trimmed="Unknown_foto"
          fi

          #echo "Moving $1 to $WHEREIGO ..."
          #mkdir -p "${trimmed}"
          #mv -n "$1" "${WHEREIGO}"
    fi
}

function readVideo () {



    if [ "$1" != "." ] ; then
            BASEDIR=".";
            #BASEDIR="/home/dani/aTEMP/kk";

            filename="$1";
            extension="${filename##*.}"
            filename="${filename%.*}"
            extension=${extension,,}  #para convertir en lowercase.
            #echo $filename;

            RANDOMNUMBER=$(( ( 999+RANDOM % 1000 )  + 1 ));
            YEAR=`exiv2 "$1" 2>/dev/null |grep timestamp|cut -d " " -f 4|cut -c 1-4`
            MONTH=`exiv2 "$1" 2>/dev/null |grep timestamp|cut -d " " -f 4|cut -c 6,7`
            DAY=`exiv2 "$1" 2>/dev/null |grep timestamp|cut -d " " -f 4|cut -c 9,10`
            HOUR=`exiv2 "$1" 2>/dev/null |grep timestamp|cut -d " " -f 5|cut -c 1,2`
            MINUTE=`exiv2 "$1" 2>/dev/null |grep timestamp|cut -d " " -f 5|cut -c 4,5`
            SECOND=`exiv2 "$1" 2>/dev/null |grep timestamp|cut -d " " -f 5|cut -c 7,8`

            trimmed=${BASEDIR}/${YEAR}/${YEAR}.${MONTH}/VIDEOS
            WHEREIGO=${BASEDIR}/${YEAR}/${YEAR}.${MONTH}/VIDEOS/${YEAR}.${MONTH}.${DAY}.${HOUR}.${MINUTE}.${SECOND}_${RANDOMNUMBER}.$extension

            #DIRTOMAKE=`identify -verbose "$1" | grep exif:DateTime: | sed 's/exif\:DateTime\: //g' | sed 's/\:[0-9][0-9] [0-9][0-9]\:[0-9][0-9]\:[0-9][0-9]//g' | sed 's|:|/|g'`

            #trimmed=`echo "$DIRTOMAKE" | xargs`
            #trimmed=$WHEREIGO;
            if [ -z "$trimmed" ] ; then
                trimmed="Unknown_video"
            fi

            echo "Moving $1 to $WHEREIGO ..."
            mkdir -p "${trimmed}"
            mv -n "$1" "${WHEREIGO}"
    fi
}

echo "======================================="
echo "      dmr_ordenar_fotos.sh  v.1.3"
echo "======================================="
echo " .. FOTOS .."
export -f readImage
BASEDIR="ooo";
echo $BASEDIR;
#for FILE in `find -maxdepth 1 -type f | egrep "(png$|jpg$|mov$|mp4$|JPG$|PNG$|MOV$|MP4$)"`; 
find "$1" -maxdepth 5 -iregex ".*\.[PNG|JPG|jpeg|png]*" -execdir bash -c 'readImage "$0" "${BASEDIR}" ' {}  \;
echo " FIN Fotos... "
echo " "
#echo " .. VIDEOS .. "
#export -f readVideo
#find "$1" -maxdepth 5 -iregex ".*\.[AVI|avi|mov|3gp]*" -execdir bash -c 'readVideo "$0"' {}  \;
#echo " FIN Videos... "
