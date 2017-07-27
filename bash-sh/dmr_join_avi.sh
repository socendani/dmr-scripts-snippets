#!/bin/bash
# DMR: este me ha funcionado PERFECTO y recursivamente COMODO !!!!


echo "Join AVI"
#for f in ./*.mp4; do echo "file '$f'" >> mylist.txt; done


#for f in ./*.mp4; do echo "$f|" >> mylist.txt; done

#ffmpeg -i concat:mylist.txt -codec copy "output.mp4"

#ffmpeg -f concat -i mylist.txt -codec copy "output.mp4"

[ -e list.txt ] && rm list.txt
for f in *.avi
do
   echo "file $f" >> list.txt
done

ffmpeg -f concat -i list.txt -c copy joined-out.avi && rm list.txt


