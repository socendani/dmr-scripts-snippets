# VideoFiles Process
## Description
This program uses FFMpeg to process video files.

  usage: node dmr-videofiles.js   \\SHARED\FOLDER   true                                                         --------");
  utils.log("------               > true|false => Convertir a Mp4 


WINDOWS-CLI   (solo hace falta instalar ffmpeg)

PROVESO MANUAL:::
(for %i in (*.MOV) do @echo file '%i') > dmr_list.txt

##UNIR: 
ffmpeg -f concat -i dmr_list.txt -c copy dmr_output.mov

##CONVERTIR: 
ffmpeg -i dmr_output.mov -qscale 0 dmr_output.mp4

##ejemplos:
ffprobe -i "video.mp4" -show_entries format=duration -v quiet -of csv="p=0"
ffprobe -v quiet -print_format json -show_format -show_streams somefile.asf
