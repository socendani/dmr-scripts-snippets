#!/bin/bash
# FILE: avi2mpeg.sh
# Convertir varios archivos AVI a MPEG
# autor: OLEKSIS FRAGA MENENDEZ modificado por dani 2010
# weblog: http://oleksisfraga-udic.blogspot.com/
echo "starting... $@"
for video in *
do
echo "Encoding $video..."

mencoder -oac lavc -ovc lavc -of mpeg -mpegopts format=dvd:tsaf -vf scale=720:480,harddup -srate 48000 -af lavcresample=48000 -lavcopts vcodec=mpeg2video:vrc_buf_size=1835:vrc_maxrate=9800:vbitrate=5000:keyint=15:vstrict=0:acodec=ac3 "$video" -o "$(echo $video | sed 's/avi$/mpg/')"

echo "Done!"
done


############################ MORE ################
        #!/bin/bash
        # FILE: mov2mpg.sh
        # Convertir varios archivos MOV a AVI
        # autor: OLEKSIS FRAGA MENENDEZ 
        # weblog: http://oleksisfraga-udic.blogspot.com/

              #  for video in "$@"
              #  do
              #  echo "Encoding $video..."
              #  mencoder -of mpeg -ovc lavc -lavcopts vcodec=mpeg1video -oac mp3lame "$video" -o "$(echo $video | sed 's/mov$/mpg/')"
              #  echo "Done!"
              #  done



        #!/bin/bash
        # FILE: rmvb2avi.sh
        # Convertir varios archivos RMVB a AVI
        # autor: OLEKSIS FRAGA MENENDEZ 
        # weblog: http://oleksisfraga-udic.blogspot.com/

               # for video in "$@"
               # do
               # echo "Encoding $video..."
               # mencoder -oac mp3lame -ovc lavc -lavcopts vbitrate=900 -ffourcc xvid "$video" -o "$(echo $video | sed 's/rmvb$/avi/')"
               # echo "Done!"
               # done



        #!/bin/bash
        # FILE: mkv2avi.sh
        # Matroska video files to AVI.
        # autor: OLEKSIS FRAGA MENENDEZ 
        # weblog: http://oleksisfraga-udic.blogspot.com/

               # INPUT=$1
               # OUTPUT=$2

               # mencoder $INPUT -mc 0 -oac mp3lame -lameopts br=192 -ovc xvid -xvidencopts pass=1 -o /dev/null

               # mencoder $INPUT -mc 0 -oac mp3lame -lameopts br=192 -ovc xvid -xvidencopts pass=2:bitrate=1200 -o $OUTPUT

               # echo "Done!"



############################ MORE ################
