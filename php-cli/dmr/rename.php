<?php
include_once "functions.inc.php";

$program="Rename v.1.08.2017 - socendani";
$bLog=false;

p($program);
p("     (i). Renombra Videos y Fotos");

$dir=getcwd();
chdir($dir);

// if (1==1) {
// $annoying_filenames = array(
//         '.DS_Store', // mac specific
//         '.localized', // mac specific
//         'Thumbs.db' // windows specific
//     );

$cmd="find . \( -name .DS_Store -or -name ._.DS_Store -or -name Thumbs.db -or -name ._Thumbs.db \) -print -delete";
exec($cmd);



$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir,RecursiveDirectoryIterator::SKIP_DOTS));
// $objects = new RecursiveDirectoryIterator($dir,RecursiveDirectoryIterator::SKIP_DOTS);
foreach($objects as $file=>$object) {
    $filename2=$object->getFilename();
    $path=$objects->getPath()."/";
    $filename=$file;
    // die($br.$subpath.$br);
    $extension=strtolower($object->getExtension());
    if (in_array($extension, $arrayVideos)) {
        $newfilename=niceFilename($filename);
        p(" OLD:".$filename." -->  $newfilename");
        rename($filename,$path.$newfilename);
    }
    if (in_array($extension, $arrayPhotos)) {
        $newfilename=niceFilename($filename);
        p(" OLD:".$filename." -->  $newfilename");
        rename($filename,$path.$newfilename);
    }
}


    //     foreach (new DirectoryIterator($dir) as $file) {
    //         if($file->isDot()) continue;
    //         $filename=$file->getFilename();
    //         $extension=strtolower($file->getExtension());
    //         if (in_array($extension, $arrayVideos)) {
    //             $newfilename=niceFilename($filename);
    //             p("    $newfilename");
    //             rename($filename,$newfilename);
    //         }
    //         if (in_array($extension, $arrayPhotos)) {
    //             $newfilename=niceFilename($filename);
    //             p("    $newfilename");
    //             rename($filename,$newfilename);
    //         }
    //     }
    // }




// Sortida
p("END $program  $br");