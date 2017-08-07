<?php
include_once "functions.inc.php";

$program="Join videos folder v.1.08.2017 - socendani";

p($program);
p("MAIN Directory: ".$folderSource);
p("     (i). Une los videos de esta carpeta");

$dir=getcwd();

chdir($dir);

foreach (new DirectoryIterator($dir) as $file) {
    if($file->isDot()) continue;
    $filename=$file->getFilename();
    $extension=strtolower($file->getExtension());
    if (stristr($filename,".rd.")) { //si JA s'ha reduït.. fora.
        continue;
    }

    if (in_array($extension, $arrayVideos)) {
        if ($extension!="mp4") {
            $filename=convertoMp4($filename,$extension);
        }
        $meta=extractMetadata($filename);
        // print_r($meta);
        $clave=trim($meta["width"]."x".$meta["height"].".".$meta["rotation"]);
        @mkdir($clave);
        @$arrayFicheros["files"][$clave].="$file|";  //opcio A
        if (@$arrayFicheros["meta"][$clave]=="") {
            @$arrayFicheros["meta"][$clave]=print_r($meta,true);  //META del primer fichero para pasarlo al unirlos
        }
    }
}

mylog($arrayFicheros["files"]);
        

//2. Generamos el SUPERARRAY de la LISTA.txt
$arrayLista=array();
foreach ($arrayFicheros["files"] as $key=>$value) {
    $fil=explode("|",$value);
    foreach ($fil as $f) {
        if ($f=="") {
            continue;
        }
        // $newname=date("Ymdhis.").rand().".".basename($f);
        $newname=date("Ymdhis.").".".basename($f);
        rename($f,$key."/".$newname);
        @$arrayLista[$key].="file '".$newname."' \r\n";
    }

}
 

  //Tengo todos en ARRAYS clasificados por TAMAÑOS anchoxaltoxrotacion
p("     uniendo ".sizeof($arrayLista)." ficheros ");
foreach ($arrayLista as $key=>$value) {
    $salida=trim($key);
    $fichero_salida=$key.".joined.".date("Ymdhis").".mp4";
    //SOLO FUNCIONA EN EL MISMO SUBDIRETORIO !!
    chdir($folderSource."/".$key);
    $lista="lista".date("Ymdhis").".txt";
    file_put_contents($lista, $value);
    $comando="ffmpeg -f concat -i  '".$lista."' -c copy "."'../".$fichero_salida."'  -loglevel panic && rm '".$lista."'  ";
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
    rename("../".$fichero_salida2, "../".$newname);
    
}




// Sortida
p("END $program  $br");