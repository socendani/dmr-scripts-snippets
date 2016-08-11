#!/bin/bash
# DMR: este me ha funcionado PERFECTO y recursivamente COMODO !!!!
# for k in $(ls *.jpg); do convert -resize 800 -quality 80 $k r800-$k; done
# find all directories, copy structure
#find . -type d | cpio -pvdm ./resized
# creates the new image, move to new dir
#for f in *.jpg;
#for f in $(ls -R *.jpg);
DIR=${1:-.}
find $DIR -type f -iname "*.jpg" | while read file; 
do
    echo "Processing $file"
    convert -resize "50%" -quality 70 "$file" "${file%.*}.${file##*.}"
    # convert -resize "${size}!" "$file" "${file%.*}_${size}.${file##*.}"
    #convert -resize "50%"  $f ./resized/$f
done
