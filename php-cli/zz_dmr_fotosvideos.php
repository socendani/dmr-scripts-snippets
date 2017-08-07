<?php

// $extension = "ffmpeg";
// $extension_soname = $extension . "." . PHP_SHLIB_SUFFIX;
// $extension_fullname = PHP_EXTENSION_DIR . "/" . $extension_soname;

// // load extension
// if(!extension_loaded($extension)) {
//     dl($extension_soname) or die("\nCan't load extension $extension_fullname \nCheck: http://ffmpeg-php.sourceforge.net/index.php\n");
// }

$version="1.0.43 - July 2017";
$author="Dani Morte <socendani AT gmail DOT com>";

$procesarREAL=true;

$procesarFotos=true;
$procesarVideos=true;

$gVerbose=true;
$gLogFile="log_".date("Ymdhis").".log";

$action="copy";  // copy | rename

$destination_fotos="fotos_processades_".date("Ymd-hi");
$destination_videos="videos_processats_".date("Ymd-hi");
$br="\r\n";
$ffprobe="/usr/bin/ffprobe";
$arrayImages=array('jpg', 'png','jpeg','png');
$arrayVideos=array('mov','3gp','avi','mp4','mkv','wmv');
$number_fotos=0;
$number_videos=0;
system('clear');

echo "$br========================================".$br;
echo $br."AUTHOR: $author ";
echo $br."VERSION: $version ";

$folderSource = getcwd();


$folderTarget_fotos= $folderSource."/../".basename($folderSource)."_".$destination_fotos."/";
$folderTarget_videos=  $folderSource."/../".basename($folderSource)."_".$destination_videos."/";
echo "$br SOURCE Folder: ".$folderSource;
echo "$br TARGET Folder PHOTOS: ".$folderTarget_fotos;
echo "$br TARGET Folder VIDEOS: ".$folderTarget_videos;
echo "$br";

// The power of PHP5,7
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folderSource));

// $files = iterator_to_array(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS), true);
$files=iterator_to_array($objects);
ksort($files);
// print_r($files);die();
if ($procesarREAL){
    if ($procesarFotos) {
        @mkdir($folderTarget_fotos);
    }
    if ($procesarVideos) {
        @mkdir($folderTarget_videos);
    }
}
if ($gVerbose){
     if ($procesarFotos) {
        echo " .. making dir: ($folderTarget_fotos) ";
    }
    if ($procesarVideos) {
        echo " .. making dir: ($folderTarget_videos) ";
    }
}
$total_fotos=0;$total_videos=0;
foreach($files as $name => $object){
    // print_r($object);
    $filename=$object->getFilename();
    $extension=strtolower($object->getExtension());
    if ($filename== '.' || $filename == '..') {
        $number_fotos=1;
        $number_videos=1;
        continue;
    }

    //por algun motivo.. hay ficheros que no TIENEN extensión !! (antiguamente.. un fallo)
    if ($extension=="") {
        $extension="jpg";
    }
    if (is_dir($name)) {
        $number_fotos=1;
        $number_videos=1;
    }else{
        $padreCompleto=$object->getPathname();
        $output = explode("/",$padreCompleto);
        $padreFolder=trim($output[count($output)-2]);

        if (in_array($extension, $arrayImages)) {
            if ($procesarFotos) {
                procesarImagenes($name, $object, $number_fotos, $padreFolder);
                ++$number_fotos;
                ++$total_fotos;
            }
        }elseif (in_array($extension, $arrayVideos)) {
            if ($procesarVideos) {
                procesarVideos($name, $object, $number_videos, $padreFolder);
                ++$number_videos;
                ++$total_videos;
            }
        }
    }

}
//$ok=procesarDirectorio($folder);


echo "$br";
echo "$br TARGET Folder PHOTOS: ".$folderTarget_fotos;
echo "$br TARGET Folder VIDEOS: ".$folderTarget_videos;
echo "$br Total Fotos: ".$total_fotos. " $br Total Videos: ".$total_videos;
echo $br."AUTHOR: $author ";
echo $br."VERSION: $version ";
echo "$br========================================".$br;


///////////////////////// AUXILIARY FUNCTIONS //////////////////////

function procesarImagenes ($name, $object, $number, $padreFolder){
            global $br, $gVerbose, $procesarREAL,$gLogFile,$action;
            $ancho="";
            $alto="";
            $ruta="";
            $exif=@exif_read_data($name);
            $nombreOriginal=$object->getFilename();
            $extension=strtolower($object->getExtension());
            if ($extension=="") {
                $extension="jpg";
            }

            // $fechaCompleta=@$exif["FileDateTime"];  //FECHA DE ULTIMA MODIFICACION !!!.. NO NOS S
            $fechaCompleta=@$exif["DateTimeOriginal"];  //Formato: 2011:01:01 21:18:51

            //CASO ESPECIAL. Nuestra cámara CANON perdió la memoria y tiene fotos con fechas: 2001:01:01 xxxxxx y NO SON VALIDAS
            if ((substr($fechaCompleta,0,7)=="2005:01") && (@$exif["Model"]=="EX-P505"))  {
                $fechaCompleta="";
            }
            // print_r($exif);
            if ($fechaCompleta=="") { //1. miramos en EXIF ORIGINAL
                 $fechaCompleta=sacarFechaDeString($padreFolder);
                 if ($fechaCompleta=="0000.00.00") { //Miramos en el FOLDER PADRE (quizás esté ya ordenado)
                    $fechaCompleta=sacarFechaDeString($nombreOriginal);//miramos SI tenemos la FECHA PROPIA en la PARTE INICIAL DEL FICHERO (procesos antiguos)
                    if ($fechaCompleta=="0000.00.00") { //Miramos en el FOLDER PADRE (quizás esté ya ordenado)
                        $fechaCompleta=@$exif["FileDateTime"];  //Formato: UNIXTimestamp - FECHA DE ULTIMA MODIFICACION !!!.. NO NOS S
                        if ($fechaCompleta=="") {
                            $fecha="0000.00.00";
                            $hora=tiempoDelFichero($object);
                        }else{
                            $fecha=date("Y.m.d",$fechaCompleta);
                            $hora=date("h.i.s",$fechaCompleta);
                        }
                    }else{
                        $fecha=$fechaCompleta;
                        $hora=tiempoDelFichero($object);
                    }
                 }else{
                    $fecha=$fechaCompleta;
                    $hora=tiempoDelFichero($object);
                 }
            }else{
                // $fechaCompleta=convertExifToTimestamp($fechaCompleta);
                $fecha=date("Y.m.d",strtotime($fechaCompleta));
                $hora=date("h.i.s",strtotime($fechaCompleta));
            }


            $ancho=@$exif["COMPUTED"]["Width"];
            $alto="x".@$exif["COMPUTED"]["Height"];

            $ruta=$fecha;

            $destino=createSubFoldersFotos($ruta,$padreFolder); //2011.01.01
            $newfile=$fecha.".".$hora.".".$ancho.$alto.".".str_pad($number, 5, '0', STR_PAD_LEFT).".".$extension;

            $newfile=str_replace("...",".",$newfile);
            $newfile=str_replace("..",".",$newfile);

            if ($gVerbose) {
                //echo "$name\n";
                //echo "$br [".$object."] => ".$newfile. " ($ruta)" ;
                echo "$br [F:".$nombreOriginal."] => ".$destino.$newfile ;

            }
            if ($gLogFile!="") {
                file_put_contents($gLogFile,"$br [F:".$nombreOriginal."] => ".$destino.$newfile,FILE_APPEND);
            }
           if ($procesarREAL) {
                if ($action=="rename") {
                    rename($name, $destino.$newfile);
                }else{
                    copy($name, $destino.$newfile);
                }
            }
            // die("KK");

}

function procesarVideos ($name, $object, $number, $padreFolder){
            global $br,$ffprobe, $gVerbose,$procesarREAL,$gLogFile,$action;
            $ancho="";
              $alto="";
            $ruta="";
            $cmd = shell_exec($ffprobe .' -v quiet -print_format json -show_format -show_streams "'.$name.'"');
            $parsed = json_decode($cmd, true);
            // print_r($parsed);
            $nombreOriginal=$object->getFilename();
            $extension=strtolower($object->getExtension());

            $fechaCompleta=@$parsed['format']['tags']['creation_time'];

            if ($fechaCompleta=="") {
                $fecha=sacarFechaDeString($padreFolder);
                if ($fecha=="0000.00.00") {  //el padre no nos da información.. lo probamos con el mismo FICHERO
                    $fecha=sacarFechaDeString($nombreOriginal);
                }
            }else{
                if (stristr("T",$fechaCompleta)>=0) {  //Formato === 2011-01-01T01:01:52.000000Z
                    $anno=substr($fechaCompleta,0,4);
                    $mes=substr($fechaCompleta,5,2);
                    $dia=substr($fechaCompleta,8,2);
                    $fecha=$anno.".".$mes.".".$dia;
                }else{
                    $fecha="0000.00.00";
                }

            }


            $ancho=@$parsed['streams'][0]['width'];
            $alto="x".@$parsed['streams'][0]['height'];


            $ruta=$fecha;
            $destino=createSubFoldersVideos($ruta,$nombreOriginal); //2011.01.01
            //Los videos, es posible que YA tengan NOMBRES y no QUEREMOS que se pierdan!!
            $temp=preg_replace('@[^0-9a-z]+@i', '', $nombreOriginal);
            // if ((strlen($temp)>=8) && (is_numeric( substr($temp,0,8) ) ) ) { //20202020
            if ((  (  (  (strlen($temp)>=8)  &&  (is_numeric( substr($temp,0,8) ) ) ) &&   ((substr($temp,0,2)=="19")||(substr($temp,0,2)=="20"))  )) ) { //20202020
                $nombreLimpiado=str_ireplace($fecha.".".$ancho.$alto,"",trim($nombreOriginal));
                $newfile=$fecha.".".$ancho.$alto.".".nombreEnBonito($nombreLimpiado);
            }else{
                if ($fecha=="0000.00.00") {
                    $fecha=date("Y").".".date("m").".[TODO]";  //Para procesarlo manualmente y que visualmente lo veamos
                }
                $newfile=$fecha.".".$ancho.$alto.".".str_pad($number, 5, '0', STR_PAD_LEFT).".".$extension;
            }

            $newfile=str_replace("...",".",$newfile);
            $newfile=str_replace("..",".",$newfile);

            if ($gVerbose) {
                //echo "$name\n";
                //echo "$br [".$object."] => ".$newfile. " ($ruta)" ;
               echo "$br [V:".$nombreOriginal."] => ".$destino.$newfile;
            }
            if ($gLogFile!="") {
                file_put_contents($gLogFile,"$br [V:".$nombreOriginal."] => ".$destino.$newfile,FILE_APPEND);
            }
            if ($procesarREAL) {
                if ($action=="rename") {

                    rename($name, $destino.$newfile);
                }else{
                    copy($name, $destino.$newfile);
                }
            }


}

function nombreEnBonito($original) {
    $original=trim($original);
    $bonito=strtolower(preg_replace('@[^0-9a-z\.]+@i', '.', $original));
    return $bonito;
}


function sacarFechaDeString($string) {
    $temp=preg_replace('@[^0-9a-z]+@i', '', $string);

    // if (strlen($string)>=8) {
    if ((  (  (  (strlen($temp)>=8)  &&  (is_numeric( substr($temp,0,8) ) ) ) &&   ((substr($temp,0,2)=="19")||(substr($temp,0,2)=="20"))  )) ) { //20202020
        $anno=substr($temp,0,4);
        $mes=substr($temp,4,2);
        $dia=substr($temp,6,2);
        return $anno.".".$mes.".".$dia;
    }else{
        return "0000.00.00";
    }
}

function convertExifToTimestamp($exifString, $dateFormat="Y m d h i s"){
  $exifPieces = explode(" ", $exifString);
  return date($dateFormat,strtotime(str_replace(":","-",$exifPieces[0])." ".$exifPieces[1]));
}


function tiempoDelFichero($object){
    $h=date ("h", $object->getMTime());
    $m=date ("i", $object->getMTime());
    $s=date ("s", $object->getMTime());
    return $h.".".$m.".".$s;
}
function createSubFoldersVideos($fechaFichero,$nombreOriginal) {
    global $gVerbose,$folderTarget_videos, $procesarREAL;
//chequear que la ruta ORIGINAL comience por un concepto de FECHAS normal + una descripcion
    $temp=preg_replace('@[^0-9a-z]+@i', '', $nombreOriginal);
    // if ((strlen($temp)>=8)&&(is_numeric( substr($temp,0,8) ) ) ) { //20202020
    if ((  (  (  (strlen($temp)>=8)  &&  (is_numeric( substr($temp,0,8) ) ) ) &&   ((substr($temp,0,2)=="19")||(substr($temp,0,2)=="20"))  )) ) { //20202020
        $anno=substr($temp,0,4);
        $mes=substr($temp,4,2);
    }else{
        $temp=preg_replace('@[^0-9a-z]+@i', '', $fechaFichero);
        $anno=substr($temp,0,4);
        $mes=substr($temp,4,2);
    }
    $destino=$folderTarget_videos.$anno."/".$anno.".".$mes."/";
    if ($procesarREAL) {
        @mkdir($folderTarget_videos.$anno);
        @mkdir($destino);
    }
    // echo "[".$fechaFichero."]***********".$temp."---------- $destino-------\n";
    return $destino;
}

function createSubFoldersFotos($fechaFichero,$padreFolder) {
    global $gVerbose, $folderTarget_fotos,$procesarREAL;
    $temp=preg_replace('@[^0-9a-z]+@i', '', $fechaFichero);
    $anno=substr($temp,0,4);
    $mes=substr($temp,4,2);
    $dia=substr($temp,6,2);
    // $anno=substr($fechaFichero,0,4);
    // $mes=substr($fechaFichero,5,2);
    // $dia=substr($fechaFichero,8,2);
//chequear que la ruta ORIGINAL comience por un concepto de FECHAS normal + una descripcion y han de ser de 19xx or 20xx
    $temp=preg_replace('@[^0-9a-z]+@i', '', $padreFolder);
    if ((  (  (  (strlen($temp)>=8)  &&  (is_numeric( substr($temp,0,8) ) ) ) &&   ((substr($temp,0,2)=="19")||(substr($temp,0,2)=="20"))  )) ) { //20202020
    // if ((strlen($temp)>=8) && (is_numeric( substr($temp,0,8) ) ) ) { //20202020
        $destino=$folderTarget_fotos.$anno."/".$anno.".".$mes."/".nombreEnBonito($padreFolder)."/";
    }else{
        $destino=$folderTarget_fotos.$anno."/".$anno.".".$mes."/".$anno.".".$mes.".".$dia."/";
    }

    if ($procesarREAL) {
        @mkdir($folderTarget_fotos.$anno);
        @mkdir($folderTarget_fotos.$anno."/".$anno.".".$mes);
        @mkdir($destino);
    }
// echo "[".$fechaFichero."]***********".$temp."---------- $destino-------\n";
    return $destino;
}



?>
