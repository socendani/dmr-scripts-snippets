
var fs = require("fs");
var ffmpeg = require('fluent-ffmpeg');
var spawn = require('child_process').spawn;
var utils = require('./utils.js');

//exports = module.exports = {};

// var mergedVideo = fluent_ffmpeg();



module.exports = {
  version: "1.0",
  logo: function () {
    utils.log("-------------------------------------------------------------------------------------------------------------------------------------------");
    utils.log("------                                                                                                                             --------");
    utils.log("------                                                                                                                             --------");
    utils.log("------              Dani Morte     v. " + VERSION + "                                                                               ");
    utils.log("------              usage: node dmr-video2.js   \\SHARED\FOLDER   true                                                         --------");
    utils.log("------               > true|false => Convertir a Mp4                                                                               --------");
    utils.log("------                                                                                                                             --------");
    utils.log("------                                                                                                                             --------");
    utils.log("-------------------------------------------------------------------------------------------------------------------------------------------");
    utils.log("Using FFMpeg  + NodeJS + Mocha + Chai. GitHub: https://github.com/socendani/dmr-videofiles/                                                ");
    utils.log("-------------------------------------------------------------------------------------------------------------------------------------------");

  }

};


// exports.dmrvideolib = dmrvideolib;

