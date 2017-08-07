<?php
include_once "functions.inc.php";

$program="Ordenar v.1.08.2017 - socendani";

p($program);
p("MAIN Directory: ".$folderSource);
p("     (i). Ordena Recursivamente las fotos y videos");

$dir=getcwd();
chdir($dir);

$procesarREAL=true;

$procesarFotos=true;
$procesarVideos=true;

$action="copy";  // copy | rename

$destination_fotos="fotos_processades_".date("Ymd-hi");
$destination_videos="videos_processats_".date("Ymd-hi");


$folderTarget_fotos= $folderSource."/../".basename($folderSource)."_".$destination_fotos."/";
$folderTarget_videos=  $folderSource."/../".basename($folderSource)."_".$destination_videos."/";
p("TARGET Folder PHOTOS: ".$folderTarget_fotos);
p("TARGET Folder VIDEOS: ".$folderTarget_videos);


// The power of PHP5,7
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir,RecursiveDirectoryIterator::SKIP_DOTS));
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

$total_fotos=0;$total_videos=0;
$number_fotos=0;$number_videos=0;
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

        if (in_array($extension, $arrayPhotos)) {
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

// Sortida
p("END $program  $br");



