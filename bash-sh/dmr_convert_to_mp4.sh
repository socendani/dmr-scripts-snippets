#!/bin/bash
# FILE: mpg2mp4
# Convertir varios archivos MPG a MP4
# autor: dani 2017

echo "starting... $@"

#for video in *
#for video in $(ls *.AVI;ls *.avi;ls *.MOV;ls *.mov;ls *.mpg;ls *.MPG;ls *.mkv;ls *.MKV)
#for video in *.avi

for video in *.{avi,AVI,mov,MOV,mpg,MPG,mkv,MKV}
do
	echo "=================================================="
	echo "Encoding $video ..."
	echo " >  ffmpeg -i "$video" -vf yadif -c:v libx264 -preset slow -crf 19 -c:a aac -b:a 256k "$video".mp4  "
	echo "=================================================="
	  #ffmpeg -i "$video" "$video".mp4
	  #ffmpeg -f rawvideo -pix_fmt yuv420p -s:v 1920x1080 -r 25 -i "$video" -c:v libx264 -f rawvideo "$video".mp4
	  ffmpeg -i "$video" -vf yadif -c:v libx264 -preset slow -crf 19 -c:a aac -b:a 256k "$video".mp4
	echo "Done convert to MP4 !"
done


# for video in *.mov
# do
# 	echo "=================================================="
# 	echo "Encoding $video ..."
# 	echo "=================================================="
# 	  #ffmpeg -i "$video" "$video".mp4
# 	  #ffmpeg -f rawvideo -pix_fmt yuv420p -s:v 1920x1080 -r 25 -i "$video" -c:v libx264 -f rawvideo "$video".mp4
# 	  ffmpeg -i "$video" -vf yadif -c:v libx264 -preset slow -crf 19 -c:a aac -b:a 256k "$video".mp4
# 	echo "Done MOV !"
# done

# for video in *.mkv
# do
# 	echo "=================================================="
# 	echo "Encoding $video ..."
# 	echo "=================================================="
# 	  #ffmpeg -i "$video" "$video".mp4
# 	  #ffmpeg -f rawvideo -pix_fmt yuv420p -s:v 1920x1080 -r 25 -i "$video" -c:v libx264 -f rawvideo "$video".mp4
# 	  ffmpeg -i "$video" -vf yadif -c:v libx264 -preset slow -crf 19 -c:a aac -b:a 256k "$video".mp4
# 	echo "Done MKV !"
# done
