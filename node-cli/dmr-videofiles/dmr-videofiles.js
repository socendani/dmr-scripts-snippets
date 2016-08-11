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
 * en nodejs ... todo es asincrono.. VOY A MORRRRIIIRRRRRR  !!!
 * 
 * 
 * El objetivo es:
 * 1- mirar una carpeta y guardar todos los ficheros de la misma extensión
 * 2- Usar el primero de ellos como "master" para el anchoxaltoxcodec
 * 3- mirar cada fichero y si su "alto" o "ancho" o "codec" no coincide con el master, quitarlo del array
 * 4- con el array restante.. hacer un "JOIN" y "convertirlo a MP4" con un nuevo nombre
 * 5- guardar en una carpeta "bck_..." los ficheros usados.. (interesante preguntar si se puede borrar y tal.. o ejecutar otro proceso despues de verificar)
 * 
 * 
 */

var videoLib = require('./libs/videolib.js');
var utils = require('./libs/utils.js');
var ffmpeg = require('fluent-ffmpeg');
var async = require('async');

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
//  videos.splice(1, 1);
//  videos.splice(2, 1);
//  videos.splice(3, 1);




if (1 == 1) {

  //ENTRY POINT: EL primer VIDEO de la lista marca el FORMATO para concatentar el RESTO
  video_master = directory + "/" + videos[0];
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


    // var eliminarVideo = function (obj) {
    //   video = directory + "/" + obj;
    //   comprobar_video_con_master(video, function (midato) {
    //     if (midato) {
    //       videos.splice(1, 1); //NORRRR
    //       console.log("....eliminado del array  ..  " + obj);
    //     }

    //     // console.log("MI DATO====>" + midato);
    //   });

    //   // callback(videos);

    // }



    // videos.forEach(function (videoName) {
    //   console.log("START");
    //   eliminarVideo(videoName, function () {
    //     console.log("FIN");
    //     console.log(videos2);
    //   });
    // });



    function procesar_videos(videos) {


      //Debe ser una PROMESA para esperar que acabe
      var p = new Promise(


        function (resolve, reject) {
          // videos2 = videos.filter(eliminarVideo);
          // resolve(function () {
          var itemsProcessed = 0;
          var itemsMax = videos.length;
          console.log("resolviendo promesa =>" + itemsMax);

          async.each(videos, function (videoName, callback) {
            console.log("P=>" + videoName);
            video = directory + "/" + videoName;
            comprobar_video_con_master(video, function (midato) {
              if (midato) {
                videos.splice(1, 1);  //BADDD
                console.log("....eliminado del array  ..  111111111111111111111111111111");
              }
            }); //comprobar_video_con_master
            callback();
          }, function (err) {
            resolve();
            // });
          });

          // videos.forEach(function (videoName) {
          //   itemsProcessed++;
          //   video = directory + "/" + videoName;


          //   //ASYNCRONO !!!
          //   comprobar_video_con_master(video, function (midato) {
          //     if (midato) {
          //       videos.splice(1, 1);  //BADDD
          //       console.log("....eliminado del array  ..  111111111111111111111111111111");
          //     }
          //   }); //comprobar_video_con_master

          //    if(itemsProcessed === itemsMax) {
          //       console.log('all done');
          //       resolve();
          //    }
          // }); //foreach


          // });

        }); ///funcion anonima + promesa

      // console.log(OK);
      // videos.forEach(function (videoName) {
      //   console.log("==bbbbb:  ..  " + videoName);
      // });


      // if (resolve) {
      //   console.log("RESOLVE");
      //   console.log(videos);
      //   videos.forEach(function (videoName) {
      //     console.log("==bbbbb:  ..  " + videoName);
      //   });
      // }
      // });
      p.then(function () {
        console.log(" PROMESA CUMPLIDAAAAAA...");
        videos.forEach(function (videoName) {
          console.log("==ccccccc:  ..  " + videoName);
        });
      });
      p.catch(
        function (reason) {
          console.log('Handle rejected promise (' + reason + ') here.');
        });
      return "YEAH";
    }


    procesar_videos(videos, function (data) {
      console.log("IIIIIIIIIIIIIIIIIIIIIIIIII" + data);
      console.log(videos);
    });
    return;



    // async.forEach(Object.keys(videos), function (item, callback) {
    async.forEach(videos, function (item, callback) {
      console.log(item); // print the key
      //  eliminarVideo(item);
      callback(); // tell async that the iterator has completed

    }, function (err) {
      console.log('.............................................................iterating done');
      videos.forEach(function (videoName) {
        console.log("==>>>>>>>>>>>>>:  ..  " + videoName);
      });
    });



    // });

    var fichero_salida = "dmr_" + fecha + "_" + master_ancho + "x" + master_alto + "_";
    console.dir(fichero_salida);
    console.dir(videos);

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
// videoLib.createBCKDirectory(directory, videos);
// videoLib.unirMOV(directory, videos);
// videoLib.convertToMp4(directory, {});












function amazingLogo() {
  console.log("-------------------------------------------------------------------------------------------------------------------------------------------");
  console.log("Using FFMpeg  + NodeJS + Mocha + Chai. GitHub: https://github.com/socendani/dmr-videofiles/                                                ");
  console.log("-------------------------------------------------------------------------------------------------------------------------------------------");

}