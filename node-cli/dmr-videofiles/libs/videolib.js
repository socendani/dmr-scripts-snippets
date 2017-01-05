
var fs = require("fs");
var ffmpeg = require('fluent-ffmpeg');
var spawn = require('child_process').spawn;
var utils = require('./utils.js');

//exports = module.exports = {};

// var mergedVideo = fluent_ffmpeg();


var createBCKDirectory = function (directory, random_salida, videos, callback) {
  var subDirectory = directory + "/dmr_temp";

  //create subdir temp
  if (!fs.existsSync(subDirectory)) {
    fs.mkdirSync(subDirectory, 0766, function (err) {
      if (err) {
        console.log(err);
        response.send("ERROR! Can't make the +" + subDirectory + " !!! \n");    // echo the result back
      } else {
        console.log("... Creando directorio temporal en: " + subDirectory);
      }
    });
  }

  //Copy videos backup mode
  // videos.forEach(function (videoName) {
  //   fs.writeFileSync(subDirectory + "/bck_" + videoName, fs.readFileSync(directory + "/" + videoName));
  // });
  // console.log("Copiados " + videos.length + " videos al temporal en " + subDirectory);

  //MOVE videos backup mode
  videos.forEach(function (videoName) {
    fs.rename(directory + "/" + videoName, subDirectory + "/" + videoName + "_bck_" + random_salida + "_" + videoName);
  });
  utils.log("... movidos " + videos.length + " videos al temporal en " + subDirectory);
  callback();

}
exports.createBCKDirectory = createBCKDirectory;





var unirMOV = function (directory, salida_name, videos, callback) {
  var mergedVideo = ffmpeg();

  try {
    videos.forEach(function (videoName) {
      mergedVideo = mergedVideo.addInput(directory + "/" + videoName);
    });
    // ffmpeg -f concat -i dmr_list.txt -c copy dmr_output.mov
    utils.log("... Uniendo " + videos.length + " videos en: [" + salida_name + "] ....... be patience, please :-)");

    mergedVideo.mergeToFile(directory + '/' + salida_name, './tmp/')
      .on('error', function (err, stdout, stderr) {
        utils.log('Error (1)!!!!!! ' + err.message);
        console.log("stdout:\n" + stdout);
	console.log("stderr:\n" + stderr);
        if (err.message.indexOf("Error configuring complex filters") >= 1) {
          console.log("stderr:\n" + stderr); //this will contain more detailed debugging info
          utils.log("****** DANI:::: Si surt un missatge COM aquest:  Input link in5:v0 parameters (size 1080x1920, SAR 1:1) do not match the corresponding output link in0:v0 parameters (1920x1080, SAR 1:1) ===> vol dir que el video 6 (in5:v0) te un framerate diferent  *****");
        }
      })
      .on('end', function () {
        // utils.log('Finished!');
        callback();
        // return true;
      });

  } catch (err) {
    utils.log('Error (2)!!!!!!!!!!! ' + err.message);
  }
}
exports.unirMOV = unirMOV;






var convertToMp4 = function (origen, callback) {
  try {
    // if (origen) {
    //   callback(".. fichero '" + fichero + "' existente...");
    //   return;
    // }
    // utils.deleteFileIfexists(outputFile);
    // var comando = "ffmpeg -i dmr_output.mov -qscale 0 dmr_output.mp4";
    var salida = origen + ".mp4"; //mantenemos la extensión anterior como referencia

    var command = spawn('ffmpeg', ['-i', origen, '-q:v', '1', '-q:a', '2', salida]);
    command.stdout.on('data', function (data) {
      // console.log('stdout: ' + data);
    });

    command.stderr.on('data', function (data) {
      // console.log('stderr: ' + data);
    });

    command.on('close', function (code) {
      //eliminamos el fichero de original;
      fs.unlink(origen);
      //informamos que hemos acabado llamando a callback
      callback("... Convertido " + origen + " en .MP4");
    });
  } catch (err) {
    utils.log('Error (convertToMp4) ' + err.message);
  }

};

exports.convertToMp4 = convertToMp4;
