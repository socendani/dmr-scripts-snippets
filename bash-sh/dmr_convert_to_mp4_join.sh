#!/bin/bash
# FILE: mpg2mp4
# Convertir varios archivos MPG a MP4
# autor: dani 2017

echo "CONVERT mpg -> mp4... $@"
#echo "================================"
for video in *.mpg
do
echo "Encoding $video..."

#ffmpeg -i "$video" "$video".mp4
#ffmpeg -f rawvideo -pix_fmt yuv420p -s:v 1920x1080 -r 25 -i "$video" -c:v libx264 -f rawvideo "$video".mp4
ffmpeg -i "$video" -vf yadif -c:v libx264 -preset slow -crf 19 -c:a aac -b:a 256k "$video".mp4
echo "Done MPG!"
done


echo "CONVERT mov -> mp4... $@"
#echo "================================"
for video in *.mov
do
echo "Encoding $video..."

#ffmpeg -i "$video" "$video".mp4
#ffmpeg -f rawvideo -pix_fmt yuv420p -s:v 1920x1080 -r 25 -i "$video" -c:v libx264 -f rawvideo "$video".mp4
ffmpeg -i "$video" -vf yadif -c:v libx264 -preset slow -crf 19 -c:a aac -b:a 256k "$video".mp4
echo "Done mov!"
done


echo "JOIN MP4"
echo "================================"

[ -e list.txt ] && rm list.txt
for f in *.mp4
do
   echo "file $f" >> list.txt
done

ffmpeg -f concat -i list.txt -c copy joined-out.mp4 && rm list.txt

