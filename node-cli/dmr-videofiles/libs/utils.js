var fs = require("fs");
var spawn = require('child_process').spawn;

exports = module.exports = {};

var log= function(msg) {
	console.log(msg);
}
exports.log=log;


var getFilesFromDirWithExtension = function (directory, extension) {
	//Ordenar por fecha UTC !!!
	//http://stackoverflow.com/questions/10559685/using-node-js-how-do-you-get-a-list-of-files-in-chronological-order
	dir = directory+"/";
	var files = fs.readdirSync(dir)
		.map(function (v) {
			return {
				name: v,
				time: fs.statSync(dir + v).mtime.getTime()
			};
		})
		.sort(function (a, b) { return a.time - b.time; })
		.map(function (v) { return v.name; });


	// return fs.readdirSync(directory).filter(function (element) {
	return files.filter(function (element) {
		return element.substr(element.length - 3, element.length).toUpperCase() == extension.toUpperCase();
	});
};

exports.getFilesFromDirWithExtension = getFilesFromDirWithExtension;


var getMaxDbFromText = function (text) {
	var textMaxVolume = "max_volume";
	var ret = text.substr(text.indexOf(textMaxVolume) + textMaxVolume.length + 2, 10);
	return ret.split("dB")[0].trim();
}

exports.getMaxDbFromText = getMaxDbFromText;

var reverseSign = function (text) {
	return (text.charAt(0) === '-' ? text.split('-')[1] : '-' + text);
}

exports.reverseSign = reverseSign;


var getVideoNameForNormalizado = function (pathTofile) {
	return pathTofile.substr(0, pathTofile.lastIndexOf(".")) + "_NORMALIZED" + pathTofile.substr(pathTofile.lastIndexOf("."), pathTofile.length);
}

exports.getVideoNameForNormalizado = getVideoNameForNormalizado;

var deleteFileIfexists = function (pathToFile) {
	if (fs.existsSync(pathToFile)) {
		fs.unlinkSync(pathToFile);
	}
}

exports.deleteFileIfexists = deleteFileIfexists;

String.prototype.replaceAll = function (str1, str2, ignore) {
	return this.replace(new RegExp(str1.replace(/([\/\,\!\\\^\$\{\}\[\]\(\)\.\*\+\?\|\<\>\-\&])/g, "\\$&"), (ignore ? "gi" : "g")), (typeof (str2) == "string") ? str2.replace(/\$/g, "$$$$") : str2);
}; exports.replaceAll = String.prototype.replaceAll;