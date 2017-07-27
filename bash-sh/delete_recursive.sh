#!/bin/bash
#Dani Morte (socendani@gmail.com)
#2017.07.09

#mascara="(-name *.ini -o  -name *.url -o -name .DS_Store)"

mask[0]="*.ini"
mask[1]="*.url"
mask[2]="*.DS_Store"
mask[3]="*.db"


mascara1=""
mascara2=""

for item in ${mask[*]}
do
	#echo -e "\n  $item \n";
	mascara1="$mascara1 -name $item -o ";
	mascara2+="-name $item -delete -o ";
    
done
mascara1=${mascara1::-4}
mascara2=${mascara2::-4}

echo -e "\nDrag a folder here and press the Enter or Return keys to delete $mascara subfolders (. = actual folder OR nothing):\n"
read -p "" FOLDER
if [$FOLDER -eq ""]
then 
   FOLDER=".";
fi

echo -e "The following files will be deleted:"
echo -e "find $FOLDER $mascara2 "

FOLDER=".";

find $FOLDER $mascara1

echo -e "\nDelete these files? (y/n): "
read -p "" DECISION
while true
do
    case $DECISION in
        [yY]* ) find $FOLDER $mascara2
	echo -e "\nfind $FOLDER $mascara2 \n"
        echo -e "The files were deleted.\n"
        break;;
        [nN]* ) echo -e "Aborting without file deletion.\n"
        exit;;
        * ) echo -e "Aborting without file deletion.\n"
        exit;;
    esac
done
