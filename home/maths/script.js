// Returns a random integer between min(inclusive) and max(inclusive)
function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function cuenta_atras(duration) {
    if (isNaN(duration)) {
        return false;
    }
    display = document.querySelector('#time');;
    duration = duration * 60;
    var timer = duration,
        minutes, seconds;

    setInterval(function() {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (--timer < 0) {
            timer = duration;
            $('#myModal').modal();
        }
    }, 1000);
}

var app = angular.module('app', ['comun', 'restas']);
app.factory('usuario', function() {
    var obj = {};
    // var user = {};
    obj.getUser = function() {
        return user
    };
    obj.setUser = function(nombre, pwd, puntos) {
        user.nombre = nombre;
        user.pwd = pwd;
        user.puntos = puntos;
    };
    console.log("aaa=" + user.nombre);
    return obj;

});


app.factory('helloFactory', function() {
    return function(name) {
        console.log(name);
        this.name = name;
        this.hello = function() {
            return "Hello " + this.name;
        };

    };
});

var comun = angular
    .module('comun', [])
    .value('tiempo', 3)
    .controller('comunController', ['$scope', '$rootScope', 'tiempo', 'usuario', function($scope, $rootScope, tiempo, usuario) {
        $scope.tiempo = tiempo;
        // usuario.setUser("aaa", "bbb", 99);
        // $scope.$emit('activar_resta', {});
        // console.log("2222");

        $scope.ready = function() {
            // usuario.setUser($scope.yourName, $scope.yourPwd, 0);
            usuario.setUser("aaa", "bbb", 99);
        }
        $scope.start = function() {
            $scope.isDisabled = true;
            // cuenta_atras($scope.tiempo);
            // $scope.parentmethod = function() {
            // $scope.$emit('activar_resta', {});
            u = usuario.getUser();
            console.log("111=>" + u.nombre);
            // };
            $rootScope.$emit("activar_resta", {});
            // restas.crear_resta();
        }
    }]);

var restas = angular
    .module('restas', ['ngSanitize'])
    .controller('restasController', ['$scope', '$rootScope', 'usuario', function($scope, $rootScope, usuario) {
        // $scope.tiempo = 2;


        $scope.errors = 0;
        $scope.corrects = 0;
        $scope.contador = 0;
        $scope.anteriores = [];


        $rootScope.$on("activar_resta", function() {
            u = usuario.getUser();
            console.log("2222=>" + u.nombre);
            $scope.crear_resta();
        });

        $scope.siguiente = function(e) {
            if (e.keyCode == 13) {
                $scope.calcular();
            }
        }
        $scope.calcular = function() {
            resta_real = $scope.a - $scope.b;
            resta_usuari = $scope.resposta;
            $scope.contador++;
            if (resta_usuari == resta_real) {
                $scope.corrects++;
                $scope.acumular($scope.a, $scope.b,
                    "<span class='glyphicon glyphicon-ok green'></span>");
            } else {
                $scope.errors++;
                $scope.acumular($scope.a, $scope.b,
                    "<span class='glyphicon glyphicon-remove red'></span>"
                );
            }
            $scope.crear_resta();

        };


        $scope.crear_resta = function() {
            console.log("creando restaaaa");
            $scope.resposta = "";
            $scope.a = getRandomInt(9, 19);
            $scope.b = getRandomInt(1, 9);
        }

        $scope.acumular = function(x, y, z) {
            item = {
                "a": x,
                "b": y,
                "c": z
            };
            $scope.anteriores.push(item);
        }



        $("#id_all").show();




    }]);