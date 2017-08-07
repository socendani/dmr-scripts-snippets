<?php

include_once "functions.inc.php";

$program="Join MP4 Recursive v.1.08.2017 - socendani";

p($program);
p("MAIN Directory: ".$folderSource);
p("     (i). Join MP4 en base al: ancho xalto x rotacion x fecha");

$dir=getcwd();

chdir($dir);
p("     Directory: ".$dir);
// 1. Convertimos a MP4
$n=0;
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir,RecursiveDirectoryIterator::SKIP_DOTS));
$files=iterator_to_array($objects);
ksort($files);
// $objects = new RecursiveDirectoryIterator($dir,RecursiveDirectoryIterator::SKIP_DOTS);
foreach($files as $file=>$object) {
    $extension=strtolower($object->getExtension());
    if (in_array($extension, $arrayVideos)) {
        if ($extension!="mp4") {
            $filename=convertoMp4($file,$extension);
        }
    }
}

// echo "$br ....... Conversión previa a MP4: $n ".$br;
//2. Generamos el SUPERARRAY
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir,RecursiveDirectoryIterator::SKIP_DOTS));
$files=iterator_to_array($objects);
ksort($files);
// $objects = new RecursiveDirectoryIterator($dir,RecursiveDirectoryIterator::SKIP_DOTS);
$arrayFicheros=array();
foreach($files as $file=>$object) {
    $extension=strtolower($object->getExtension());
    $filename=$object->getFilename();
    if (in_array($extension, $arrayVideos)) {
        if ($extension=="mp4") {
            $meta=extractMetadata($file);
            $clave=trim($meta["fecha"].".".$meta["width"]."x".$meta["height"].".".$meta["rotation"]);
            @mkdir($clave);
            @$arrayFicheros["files"][$clave].="$file|";  //opcio A
            if (@$arrayFicheros["meta"][$clave]=="") {
                 @$arrayFicheros["meta"][$clave]=print_r($meta,true);  //META del primer fichero para pasarlo al unirlos
            }
        }
    }
}
// print_r($arrayFicheros);
mylog($arrayFicheros["files"]);
// die();
//2. Generamos el SUPERARRAY de la LISTA.txt
$arrayLista=array();
foreach ($arrayFicheros["files"] as $key=>$value) {
    $fil=explode("|",$value);
    foreach ($fil as $f) {
        // echo $br.$f;
        if ($f=="") {
            continue;
        }
        // $newname=date("Ymdhis.").rand().".".basename($f);
        $newname=date("Ymdhis.").".".basename($f);
        rename($f,$key."/".$newname);
        @$arrayLista[$key].="file '".$newname."' \r\n";
    }

}

if (1==1) {
         //Tengo todos en ARRAYS clasificados por TAMAÑOS anchoxaltoxrotacion
        p("     uniendo ".sizeof($arrayLista)." ficheros ");
        foreach ($arrayLista as $key=>$value) {
            $salida=trim($key);
        
            $fichero_salida=$key.".joined.".date("Ymdhis").".mp4";

            // NOTA dani: Opcions A i B donen aquest error a vegades: => FFMPEG Concat protocol Error: Found duplicated MOOV Atom. Skipped it

            //opcio A
            // $comando="ffmpeg -i 'concat:$value' -c copy "."'./".$salida."_joined.mp4' -loglevel panic  ";  
            //opcio B
            // $comando="ffmpeg  $value -c copy "."'./".$salida."_joined.mp4'  " ;  
            //opcio C:
            //SOLO FUNCIONA EN EL MISMO SUBDIRETORIO !!
            chdir($folderSource."/".$key);

            $lista="lista".date("Ymdhis").".txt";
            file_put_contents($lista, $value);
            // $comando="ffmpeg -f concat -i  '".$lista."' -c copy "."'../".$fichero_salida."'  -loglevel panic && rm '".$lista."'  ";
        
            // $comando="ffmpeg -f concat -i  '".$lista."' -c copy "."'../".$fichero_salida."'  ";
            $comando="ffmpeg -f concat -i  '".$lista."' -c copy "."'../".$fichero_salida."'  -loglevel panic && rm '".$lista."'  ";
            // echo($br.$br.$comando.$br.$br);die();

            //Ejecutamos JOIN
            $cmd1 = exec($comando . "2>&1", $salidacmd, $result);
            if ($result==0) {
                 p("        [".$key."] => unido en $fichero_salida ...(OK)");
            }else{
                 p("        [".$key."] => uniendo en $fichero_salida ...(error)");
            }
            $meta=$arrayFicheros["meta"][$key];
            $fichero_salida2=addMetasDani("../".$fichero_salida, $meta);

            //añadimos duración
            $meta2=extractMetadata($fichero_salida2);
            $newname=str_replace("joined","duration.".$meta2["duration"],$fichero_salida2);
            $newname=str_replace(".mp4.meta","",$newname);    
            rename($fichero_salida2, $newname);
            
        }

}




p("END $program  $br");