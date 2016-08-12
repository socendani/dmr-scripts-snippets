/***
 *
 * Dani Morte v.1.1
 * 2016.08
 *
 * usage: node dmr-videofiles.js   \\SHARED\FOLDER
 *
 *
-------------------------------------
WINDOWS-CLI   (solo hace falta instalar ffmpeg)

PROVESO MANUAL:::
(for %i in (*.MOV) do @echo file '%i') > dmr_list.txt
ffmpeg -f concat -i dmr_list.txt -c copy dmr_output.mov
ffmpeg -i dmr_output.mov -qscale 0 dmr_output.mp4

... a tener encuenta:
ffprobe -i "video.mp4" -show_entries format=duration -v quiet -of csv="p=0"
ffprobe -v quiet -print_format json -show_format -show_streams somefile.asf

-----------------------------------------
 */

var videoLib = require('./libs/videolib.js');
var utils = require('./libs/utils.js');
var ffmpeg = require('fluent-ffmpeg');
var async = require('async');
var path = require('path');
var videoDir = process.argv[2];
var fraseRepetir="";

var gEliminarOld = false;

try {
  amazingLogo();
  // node dmr-videofiles.js   \\euromedice-nas\TEMPORAL\dmr\proves3
  // node dmr-videofiles.js   "\\euromedice-nas\TEMPORAL\dmr\proves2molones que te.cagas"
  var directory = videoDir || ".";
  var parent_name = path.basename(directory).replace(/[^a-z0-9]/gi, '_').toLowerCase();;
  utils.log("Directorio: " + directory);


  //pensado para hacer un BUBLE con cada extensión y así procesar todos
  var arrayVideoExtensions = ['mov', '3gp','mp4', 'mpg', 'xxxmp4'];
  // var arrayVideoExtensions = ['3gp'];

  arrayVideoExtensions.forEach(function (item) {
    var videoExtension = item;

    utils.log("[" + videoExtension + "] - START - Procesando extensión: [" + videoExtension + "]");


    var videos = utils.getFilesFromDirWithExtension(videoDir, videoExtension);
    if (videos.length <= 0) {
      utils.log("[" + videoExtension + "] - END - No hay videos que procesar con la extensión: " + videoExtension);
      return;
    } else {
      utils.log("... [" + videoExtension + "] Se han encontrado " + videos.length + " en " + videoDir);
      console.time("main_" + videoExtension);  //TODO
    }



    var counter = 0;
    //Del primer VIDEO cogeremos las condiciones para unir Correctamente el RESTO de videos
    // \\Daol-nas\DAOL-VIDEO\2015\01 Gener 2015
    //PRE:
    // var fecha = new Date().toISOString().slice(0, 10);

    //Treiem els fitxer "DMR" per no tornar-los a processar
    videos = videos.filter(function (obj) { return (obj.indexOf("DMR") >= 1) ? false : true; });

    if (1 == 1) {

      //ENTRY POINT: EL primer VIDEO de la lista marca el FORMATO para concatentar el RESTO
      video_master = directory + "/" + videos[0];
      ffmpeg.ffprobe(video_master, function (err, metadata) {
        // var format = metadata.format.format_long_name;
        var master_ancho = metadata.streams[0].coded_width;
        var master_alto = metadata.streams[0].coded_height;
        if (metadata.format.tags.creation_time === undefined) {
          var master_fecha = new Date().toISOString().slice(0, 10);
        } else {
          var master_fecha = metadata.format.tags.creation_time.slice(0, 10);
        }

        //DMR: Ejemplo de callback en funciones ASYNC de nodejs:
        //http://stackoverflow.com/questions/6847697/how-to-return-value-from-an-asynchronous-callback-function
        function comprobar_video_con_master(video, callback) {
          ffmpeg.ffprobe(video, function (err, metadata) {
            ancho = metadata.streams[0].coded_width;
            alto = metadata.streams[0].coded_height;
            // bit_rate=metadata.format.r_frame_rate;  //mismo bit_rate ¿sure?
            fecha = metadata.format.tags.creation_time;

            // console.log(metadata);
            // utils.log("... [" + videoExtension + "] VIDEO (" + path.basename(video) + ")=> Ancho: " + ancho + "/" + master_ancho + ", Alto:" + alto + "/" + master_alto + "  ,Fecha: " + fecha);
            if (ancho !== master_ancho) {
              callback(true);
              return;
            }
            if (alto !== master_alto) {
              callback(true);
              return;
            }
            //Se puede procesar
            callback(false);
          });
        }


        function procesar_videos(videos) {
          //Debe ser una PROMESA para esperar que acabe
          var p = new Promise(
            function (resolve, reject) {
              // videos2 = videos.filter(eliminarVideo);
              // resolve(function () {
              var itemsProcessed = 0;
              var itemsMax = videos.length;
              // utils.log("resolviendo promesa =>" + itemsMax);

              //sincrono
              videos.forEach(function (videoName) {
                //asincrono !!!
                comprobar_video_con_master(directory + "/" + videoName, function (isEliminar) {
                  itemsProcessed++;
                  if (isEliminar) {
                    videos.splice(videos.indexOf(videoName), 1);  //BADDD
                    // utils.log("[" + videoExtension + "]... eliminado del array  ..  " + videoName);
                  }
                  //Verificamos que se han procesado TODOS los items del ARRAY para dar
                  //por resuelta la promesa
                  if (itemsProcessed === itemsMax) {
                    resolve(itemsMax);
                  }
                }); //comprobar_video_con_master
              }); //foreach
            }); ///funcion anonima + promesa

          p.then(function (itemsMax) {
            try {
              utils.log("... [" + videoExtension + "] Se van a unir " + videos.length + " videos");
              if (videos.length<itemsMax) {
                fraseRepetir="***************  ("+itemsMax+"/"+videos.length+") Debe repetir el proceso pues hay diferentes tamaños de pantalla *********";
              }
              var random_salida = Math.random().toString().replace(".", "9");
              var fichero_salida = "" + master_fecha + "_" + master_ancho + "x" + master_alto + "_" + parent_name + "_" + random_salida + "_DMR." + videoExtension;
              utils.log("... [" + videoExtension + "] Fichero de salida: " + fichero_salida);

              videoLib.unirMOV(directory, fichero_salida, videos, function () {
                //se han unido OK!
                videoLib.createBCKDirectory(directory, random_salida, videos, function () {
                  //Se han movido..
                  videoLib.convertToMp4(directory + "/" + fichero_salida, function (msg) {
                    utils.log(msg);
                    utils.log("[" + videoExtension + "] - END - Procesado ok en " + console.timeEnd("main_" + videoExtension));
                    utils.log(fraseRepetir);
                  });
                });
              });

            } catch (err) {
              utils.log("... ERROR:  ..  " + err);
            }
          });
          p.catch(
            function (reason) {
              utils.log('Handle rejected promise (' + reason + ') here.');
            });
        }


        procesar_videos(videos, function (data) {
          utils.log("IIIIIIIIIIIIIIIIIIIIIIIIII" + data);
          utils.log(videos);
        });

      });

    }

  }); //forEach Extensions

} catch (err) {
  console.log("************* ERRROR *************");
  console.log(err);
}



function amazingLogo() {
  utils.log("-------------------------------------------------------------------------------------------------------------------------------------------");
  utils.log("Using FFMpeg  + NodeJS + Mocha + Chai. GitHub: https://github.com/socendani/dmr-videofiles/                                                ");
  utils.log("-------------------------------------------------------------------------------------------------------------------------------------------");

}
