#!/bin/bash
# FILE: mpg2mp4
# REDUCIR videos de HD1080 a SD720
# autor: dani 2017

echo "starting... $@"

for video in *.mp4
do
	echo "=================================================="
	echo " Reduciendo video $video ..."
	echo "=================================================="
  ffmpeg -i "$video" -s hd720 -c:v libx264 -crf 23 -c:a aac -strict -2 "$video".sd.mp4	 
  # ffmpeg -i "$video" -s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 "$video".hd.mp4	 
	echo "Done !"
done
