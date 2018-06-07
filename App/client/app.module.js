var app = angular.module("LoginApp", ['ngRoute', 'ui-notification', 'LocalStorageModule']);

app.run(function($rootScope, $window, $timeout, localStorageService){

    $rootScope.url = "http://api.asesoriaspar.com";
    
    //Verifica la sesion
    (function(){
        if( localStorageService.get('user') ){
            var data = localStorageService.get('user');
            data = JSON.parse( data );
            //Se verifica rol y se redirecciona
            if( data.user.role === 'basic' )
                $window.location.href = "desktop";
            else
                $window.location.href = "dashboard";    
        }
    })();
    
    //Guarda la session
    $rootScope.saveSession = function(data){
        //Se guarda session
        localStorageService.set('user', JSON.stringify(data));
        //Se verifica rol y se redirecciona
        if( data.user.role === 'basic' )
            $window.location.href = "desktop";
        else
            $window.location.href = "dashboard";
    }


    //TODO: metodo para verificar si esta logeado

    //-----------VARIABLES GLOBALES
    $rootScope.page = {
        title: "PAGE TITLE"
    };

    //User
    $rootScope.user = {},

    //-STATUS VARIABLES
    $rootScope.alert = {
        type: "",
        status: false,
        message: "",
    };
    $rootScope.loading = {
        status: false,
        message: "",
    };
    // $rootScope.success = {
    //     status: false,
    //     message: "",
    // };
    // $rootScope.error = {
    //     status: false,
    //     message: "",
    // };
    // $rootScope.warning = {
    //     status: false,
    //     message: "",
    // };
    
    $rootScope.showUpdateForm = false;
    $rootScope.showCreateForm = false;
    $rootScope.showModalForm = false;

    
});


app.factory("RequestFactory", function() {
    // var url = "http://api.ronintopics.com/index.php";
    var url = "http://api.asesoriaspar.com/index.php";

    return {
        getURL: function() {
            return url;
        }
    };
});