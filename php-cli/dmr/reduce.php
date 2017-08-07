<?php
include_once "functions.inc.php";

$program="Reduce v.1.08.2017 - socendani";

p($program);
p("MAIN Directory: ".$folderSource);
p("     (i). Convierte a MP4 y REDUCE el tamaño");

$dir=getcwd();

chdir($dir);



foreach (new DirectoryIterator($dir) as $file) {
    if($file->isDot()) continue;
    $filename=$file->getFilename();
    $extension=strtolower($file->getExtension());
    if (stristr($filename,".rd.")) { //si JA s'ha reduït.. fora.
        continue;
    }
    if (stristr($filename,".rdno.")) { //si JA s'ha reduït.. fora.
        continue;
    }

    if (in_array($extension, $arrayVideos)) {
        //SOLO convertiremos a MP$ y reduciremos si el tamaño del height es mayor de 400 en otro
        //en tamaños pequeños no ganamos NADA
        $meta=extractMetadata($filename);
        // print_r($meta);die();

        if ( ($meta["height"]>400)&&($meta["size"]>989000000) ) {
        // if ( 1==1 ) {
            if ($extension!="mp4") {
                $filename=convertoMp4($filename,$extension);
            }
            p("     ..reduciendo tamaño : " .$filename); 
            // ffmpeg -i input.mp4 -c:v libx264 -crf 24 -b:v 1M -c:a aac output.mp4   
            // $comando="ffmpeg -i '$filename' -s $hd -c:v libx264 -crf 23 -c:a aac -strict -2 '$filename.reduced.mp4' -loglevel panic	 ";
            $filename2=$filename.".rd.".$extension;
            $comando="ffmpeg -i '$filename' -c:v libx264 -crf 23 -c:a aac -strict -2 '$filename2' -loglevel panic	 ";
            exec ($comando);
            unlink($filename);
             //mejoramos el NOMBRE
             $newfilename=niceFilename($filename2);
            rename($filename2,$newfilename);
            
        }else{
            $newfilename=$filename.".rdno.".$extension;
            $newfilename=niceFilename($newfilename);
            rename($filename,$newfilename);
        }

       
    }
        
}




// Sortida
p("END $program  $br");