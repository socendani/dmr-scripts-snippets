#!/usr/bin/env bash


if [ ! $1 ]; then
    echo "Usage: ./pictures.sh jpg"
    exit 1
fi

for f in *."$1"; do
    FILENAME="$f"

    YEAR=`date -j -f "%s" $(stat -f "%m" "$FILENAME") +"%Y"`

echo $YEAR;

    MONTH=`date -j -f "%s" $(stat -f "%m" "$FILENAME") +"%m_%B"`
    DEST="$YEAR/$MONTH"

    if [ ! -d "$DEST" ]; then
        mkdir -p "$DEST"
    fi

    echo "Moving $FILENAME to $DEST/$FILENAME ..."
    mv "$FILENAME" "$DEST/$FILENAME"
done

