<?php
setlocale(LC_ALL, 'es_ES.utf8');
//////////////// UTILS for EVERYTHING
$br="\r\n";
$folderSource = getcwd();
$ffprobe="/usr/bin/ffprobe";
$arrayVideos=array('mov','3gp','avi','mp4','mkv','wmv','m4v','mpg','divx');
$arrayPhotos=array('jpg', 'png','jpeg','bmp');

$bLog=true;
$logFile="log_".date("Y.m.d_h.i.s").".txt";

$tempDir=$folderSource."/../dmr_temp/";






function p($texto){
    global $br;
    $time=date("h:i:s");
    if (is_array($texto)) {
        print_r($texto,true);
    }else{
        $texto=$time." - ".$texto;
       echo $br.$texto;
    }
     mylog($texto);

}

function mylog($texto){
    global $br,$folderSource,$logFile,$bLog, $tempDir;
    if (!$bLog) {
        return false;
    }
    @mkdir($tempDir);
    $l=$tempDir.$logFile;
    if (is_array($texto)) {
        file_put_contents($l, print_r($texto,true), FILE_APPEND);
    }else{
        file_put_contents($l, $texto."\n", FILE_APPEND);
    }
}


function addMetasDani($nombre_fichero, $metaArray=array()) {
    //AÑADIR METADATA al fichero de salida, porque SEGURO que se ha perdido. Estoy en el mismo directorio
    //key="aaaa.mm.dd.anchoxaltoxrotacion";
    // $metaFichero=extractMetadata($nombre_fichero);
    $fichero_salida2=$nombre_fichero.".meta.mp4";

    $anno=@substr($metaArray["fecha"],0,4);
    $fecha=@$metaArray["fecha"];
    

    $meta="  -map_metadata -1 ";
    $meta.=" -metadata title='Video privat familia Morte Maya' ";
    $meta.=" -metadata comments='$fichero_salida2' ";
    $meta.=" -metadata author='socendani' ";
    $meta.=" -metadata album='socendani family' ";
    $meta.=" -metadata artist='socendani' ";
    $meta.=" -metadata year='".$anno."' ";
    $meta.=" -metadata date='".$fecha."' ";
    $meta.=" -metadata copyright='socendani.".date("Y")."' " ;

    $comando2="ffmpeg -i '../".$nombre_fichero."' $meta -codec copy '../".$fichero_salida2."' -loglevel panic  ";
    exec($comando2);
    unlink("../".$nombre_fichero);
    return $fichero_salida2;
}

function extractMetadata($rutaFichero) {
    global $ffprobe;
    $cmd = shell_exec($ffprobe ." -v quiet -print_format json -show_format -show_streams '".$rutaFichero."' ");
    $parsed = json_decode($cmd, true);
    // print_r($parsed);
   
    $duration=round_duration(@$parsed['format']['duration']);
    $tamany=@$parsed['format']['size'];
    $ancho=@$parsed['streams'][0]['width'];
    $alto=@$parsed['streams'][0]['height'];
    $rotacion=@$parsed['streams'][0]['rotation'];
    if ($rotacion=="") {
         $rotacion=@$parsed['streams'][0]['tags']['rotate'];
    }
    $fechaCompleta=@$parsed['format']['tags']['creation_time'];
    if ($fechaCompleta=="") {
        // $fechaCompleta=$object->getMTime();  //fecha modificació del fitxer
        // if (date("Y.m.d",strtotime($fechaCompleta) == "1970.01.01") ) {  //si no tiene fecha.. por copy anterior..
        //     $fechaCompleta=$object->getCTime(); 
        // }
    }
    $fecha=date("Y.m.d",strtotime($fechaCompleta));
    $fechaAnnoMes=date("Y.m",strtotime($fechaCompleta));
    if($ancho=="") $ancho="0";
    if($alto=="") $alto="0";
    if($rotacion=="") $rotacion="0";
    if($duration=="") $duration="0";
    if($fechaCompleta=="") $fechaCompleta="0";
    $arrayMetadata=[
        "width"=>$ancho,
        "height"=>$alto,
        "rotation"=>$rotacion,
        "duration"=>$duration,
        "size"=>$tamany,
        "fechaUnix"=>$fechaCompleta,
        "fecha"=>$fecha,
        "fechaAnnoMes"=>$fechaAnnoMes
    ];
    return $arrayMetadata;
}


function convertoMp4($filename, $extension) {
    global $folderSource, $tempDir;
    // $backup=$folderSource."/dmr_temp_originals";
    if ($extension!="mp4") {
        echo p("      [".$filename." ] => convirtiendo a MP4 ...");
        $filename2=basename($filename).".mp4";
        $comando="ffmpeg -i '$filename' -vf yadif -c:v libx264 -loglevel panic -preset slow -crf 19 -c:a aac -b:a 256k -map_metadata 0 '$filename2' ";
        $cmd = exec($comando . "2>&1", $salida, $result);
        @mkdir($backup);
        if ($result==0) {
            rename($filename,$tempDir.$filename2."_".date("Ymdhis").".done");
            echo "  (OK) ";
        }else{
            rename($filename,$filename2.".error");
            echo "  (error) ";
        }

        return $filename2;
    }
    return false;

}



function niceFilename($original) {
    $original=trim(strtolower(basename($original)));
    // $original = strtr($original, 'áàâäãåçéèêëíìîïñóòôöõúùûüýÿñ', 'aaaaaaceeeeiiiinooooouuuuyyn'); //bad
    $original =iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $original);
    // p($original);
    $bonito=(preg_replace('@[^0-9a-z()\[\]\.]+@i', '.', $original));
    //Quitamos cosas clásicas de internet..
    $bonito=str_replace("divxtotal.com","",$bonito);
    $bonito=str_replace("divxatope1.com","",$bonito);
    $bonito=str_replace("newpct1.com","",$bonito);
    $bonito=str_replace("www","",$bonito);
    $bonito=str_replace("dvdrip","",$bonito);
    $bonito=str_replace("dvd","",$bonito);
    $bonito=str_replace("xvid","",$bonito);
    $bonito=str_replace("spanish","",$bonito);
    $bonito=str_replace("rip","",$bonito);
    $bonito=str_replace("bluray","",$bonito);
    $bonito=str_replace("blurayac3","",$bonito);
    $bonito=str_replace("castellano","",$bonito);
    $bonito=str_replace("ac3.5.1","",$bonito);
    $bonito=str_replace("espanol","",$bonito);
    $bonito=str_replace("hdtv","",$bonito);
    // $bonito=str_replace("dvb","",$bonito);
    $bonito=str_replace("[.]","",$bonito);
    $bonito=str_replace("[]","",$bonito);
    $bonito=str_replace("()","",$bonito);
    // $bonito=str_replace(".mp4.rd.",".rd.",$bonito);
    // $bonito=str_replace(".avi.rd.",".rd.",$bonito);
    // $bonito=str_replace(".mkv.rd.",".rd.",$bonito);
    // $bonito=str_replace(".3gp.rd.",".rd.",$bonito);
    // $bonito=str_replace(".mov.rd.",".rd.",$bonito);
    $bonito=str_replace("...",".",$bonito);
    $bonito=str_replace("..",".",$bonito);

    return $bonito;
}


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
         $dia=substr($temp,6,2);
    }else{
        $temp=preg_replace('@[^0-9a-z]+@i', '', $fechaFichero);
        $anno=substr($temp,0,4);
        $mes=substr($temp,4,2);
         $dia=substr($temp,6,2);
    }
    $destino=$folderTarget_videos.$anno."/".$anno.".".$mes."/".$anno.".".$mes.".".$dia."/";
    if ($procesarREAL) {
        @mkdir($folderTarget_videos.$anno);
        @mkdir($folderTarget_videos.$anno."/".$anno.".".$mes);
        
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



////////////////// Auxiliary
function round_duration($seconds) {
    if ($seconds==""){
        return "0";
    }
  $t = round($seconds);
  return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
}
function convertExifToTimestamp($exifString, $dateFormat="Y m d h i s"){
  $exifPieces = explode(" ", $exifString);
  return date($dateFormat,strtotime(str_replace(":","-",$exifPieces[0])." ".$exifPieces[1]));
}
