
var fs = require("fs");
var ffmpeg = require('fluent-ffmpeg');
var spawn = require('child_process').spawn;
var utils = require('./utils.js');

//exports = module.exports = {};

// var mergedVideo = fluent_ffmpeg();


var createBCKDirectory = function (directory, videos) {
  var subDirectory = directory + "/dmr_temp";

  //create subdir temp
  if (!fs.existsSync(subDirectory)) {
    fs.mkdirSync(subDirectory, 0766, function (err) {
      if (err) {
        console.log(err);
        response.send("ERROR! Can't make the +" + subDirectory + " !!! \n");    // echo the result back
      } else {
        console.log("Creando directorio temporal en " + subDirectory);
      }
    });
  }

  //CHECK good videos
  //EL PRIMER VIDEO condicionará el JOIN de los videos



 




  //Copy videos backup mode
  videos.forEach(function (videoName) {
    fs.writeFileSync(subDirectory + "/bck_" + videoName, fs.readFileSync(directory + "/" + videoName));
  });
  console.log("Copiados " + videos.length + " videos al temporal en " + subDirectory);

}
exports.createBCKDirectory = createBCKDirectory;





var unirMOV = function (directory, videos) {
  var mergedVideo = ffmpeg();
  videos.forEach(function (videoName) {
    // console.log(videoName);
    mergedVideo = mergedVideo.addInput(directory + "/" + videoName);
  });

  // ffmpeg -f concat -i dmr_list.txt -c copy dmr_output.mov
  var salida = "dmr_video_unido.mp4";
  console.log("Uniendo ficheros MOV en " + salida);


  mergedVideo.mergeToFile(directory + '/' + salida, './tmp/')
    .on('error', function (err) {
      console.log('Error ' + err.message);
      
    })
    .on('end', function () {
      console.log('Finished!');
      return true;
    });


}
exports.unirMOV = unirMOV;




var convertToMp4 = function (directory, callback) {
  // var outputFile = utils.getVideoNameForNormalizado(pathTofile);

  // utils.deleteFileIfexists(outputFile);

  // var comando = "ffmpeg -i dmr_output.mov -qscale 0 dmr_output.mp4";

  var origen = directory + "/dmr_output.mov";
  var salida = directory + "/dmr_output.mp4";

  console.log("Convirtiendo  " + origen + " a " + salida);
  var command = spawn('ffmpeg', ['-i', origen, '-q:v', '1', '-q:a', '2', salida]);
  command.stdout.on('data', function (data) {
    // console.log('stdout: ' + data);
  });

  command.stderr.on('data', function (data) {
    // console.log('stderr: ' + data);
  });

  command.on('close', function (code) {
    console.log('FFMpeg process exited with code ' + code);
    // callback();
  });

};

exports.convertToMp4 = convertToMp4;



