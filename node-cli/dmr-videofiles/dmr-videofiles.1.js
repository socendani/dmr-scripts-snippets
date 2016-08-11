/***
 * 
 * Dani Morte
 * 
 * usage: node dmr-videofiles.js   videoDIR   videoEXTENSION
 * 
 * 
 * 
 * 
 * -------------------------------------
WINDOWS   (solo hace falta instalar ffmpeg)

RUTA: \\euromedice-nas\TEMPORAL\dmr

(for %i in (*.MOV) do @echo file '%i') > dmr_list.txt
ffmpeg -f concat -i dmr_list.txt -c copy dmr_output.mov
ffmpeg -i dmr_output.mov -qscale 0 dmr_output.mp4

-----------------------------------------
 * 
 * 
 */

var videoLib = require('./libs/videolib.js');
var utils = require('./libs/utils.js');
var ffmpeg = require('fluent-ffmpeg');
var videoDir = process.argv[2];
// var videoExtension = process.argv[3];

var gEliminarOld = false;


amazingLogo();
// \\euromedice-nas\TEMPORAL\dmr\proves
// var directory = videoDir || "\\\\euromedice-nas\\TEMPORAL\\dmr\\proves";
var directory = videoDir || ".";
console.log("Directorio: " + directory);



var arrayVideoExtensions = ['mov', 'mp4', '3gp', 'mpg'];
var videoExtension = "mov";




var videos = utils.getFilesFromDirWithExtension(videoDir, videoExtension);
if (videos.length <= 0) {
  console.log("No hay videos que procesar.");
  return;
}
console.log("Se han encontrado " + videos.length + " videos con extensión " + videoExtension + " encontrados en " + videoDir);
console.time("Main");
var counter = 0;

//Del primer VIDEO cogeremos las condiciones para unir Correctamente el RESTO de videos

// \\Daol-nas\DAOL-VIDEO\2015\01 Gener 2015

//PRE:
var fecha = new Date().toISOString().slice(0, 10);



//Treiem els fitxer "DMR" per no tornar-los a processar
videos = videos.filter(function (obj) { return (obj.indexOf("dmr") >= 1) ? false : true; });





if (1 == 1) {
  video_master = directory + "/" + videos[0];
  //ENTRY POINT: EL primer VIDEO de la lista marca el FORMATO para concatentar el RESTO
  ffmpeg.ffprobe(video_master, function (err, metadata) {
    // var format = metadata.format.format_long_name;
    var master_ancho = metadata.streams[0].coded_width;
    var master_alto = metadata.streams[0].coded_height;
    var fecha = metadata.format.tags.creation_time.slice(0, 10) || new Date().toISOString().slice(0, 10);

    //DMR: Ejemplo de callback en funciones ASYNC de nodejs: 
    //http://stackoverflow.com/questions/6847697/how-to-return-value-from-an-asynchronous-callback-function
    function comprobar_video_con_master(video, callback) {
      ffmpeg.ffprobe(video, function (err, metadata) {
        console.log("VIDEO (" + video + ")=" + metadata.streams[0].coded_width + "=" + master_ancho + " --> " + metadata.format.tags.creation_time);
        if (metadata.streams[0].coded_width !== master_ancho) {
          callback(true);
          return;
        }
        callback(false);
      });
    }


    var eliminarVideo = function (obj, callback) {
      video = directory + "/" + obj;
      comprobar_video_con_master(video, function (midato) {
        if (midato) {
          videos.splice(obj, 1);
          console.log("....eliminado del array  ..  " + obj);
        }

        // console.log("MI DATO====>" + midato);
      });

    }

    function procesar_videos(videos) {
      //Debe ser una PROMESA para esperar que acabe
      var p = new Promise(function (resolve, reject) {

        // videos2 = videos.filter(eliminarVideo);
        var OK = videos.forEach(function (videoName) {
          video = directory + "/" + videoName;
          comprobar_video_con_master(video, function (midato) {
            if (midato) {
              videos.splice(videoName, 1);
              console.log("....eliminado del array  ..  " + videoName);
            }
            // eliminarVideo(videoName, function() {
            // console.Clog("==aaaaa:  ..  " + videoName);
            // });
          });
        });

        console.log(OK);
        videos.forEach(function (videoName) {
          console.log("==bbbbb:  ..  " + videoName);
        });


        if (resolve) {
          console.log("RESOLVE");
          console.log(videos2);
          videos2.forEach(function (videoName) {
            console.log("==bbbbb:  ..  " + videoName);
          });
        }
      });

      p.then(function () {
        console.log(" PROMESA ...");
        videos2.forEach(function (videoName) {
          console.log("==ccccccc:  ..  " + videoName);
        });
      });
    }


    procesar_videos(videos);


    // });

    var fichero_salida = "dmr_" + fecha + "_" + master_ancho + "x" + master_alto + "_";
    console.dir(fichero_salida);

    // videos.forEach(function (videoName) {
    //   console.log("==>>>>>>>>>>>>>:  ..  " + videoName);
    // });


  });

}




return;

// console.log(obj);
var fichero_salida = "dmr_" + fecha + "_" + ancho + "x" + alto + "_";
console.dir(fichero_salida);
return;



//PROVA
videoLib.createBCKDirectory(directory, videos);
// videoLib.unirMOV(directory, videos);
// videoLib.convertToMp4(directory, {});












function amazingLogo() {
  console.log("                                                                                                                                           ");
  console.log("  _______  .___  ___. .______                 ____    ____  __   _______   _______   ______    _______  __   __       _______      _______.");
  console.log(" |       \ |   \/   | |   _  \                \   \  /   / |  | |       \ |   ____| /  __  \  |   ____||  | |  |     |   ____|    /       |");
  console.log(" |  .--.  ||  \  /  | |  |_)  |     ______     \   \/   /  |  | |  .--.  ||  |__   |  |  |  | |  |__   |  | |  |     |  |__      |   (----`");
  console.log(" |  |  |  ||  |\/|  | |      /     |______|     \      /   |  | |  |  |  ||   __|  |  |  |  | |   __|  |  | |  |     |   __|      \   \    ");
  console.log(" |  '--'  ||  |  |  | |  |\  \----.              \    /    |  | |  '--'  ||  |____ |  `--'  | |  |     |  | |  `----.|  |____ .----)   |   ");
  console.log(" |_______/ |__|  |__| | _| `._____|               \__/     |__| |_______/ |_______| \______/  |__|     |__| |_______||_______||_______/    ");
  console.log("-------------------------------------------------------------------------------------------------------------------------------------------");
  console.log("Using FFMpeg  + NodeJS + Mocha + Chai. GitHub: https://github.com/socendani/dmr-videofiles/                                                ");
  console.log("-------------------------------------------------------------------------------------------------------------------------------------------");

}