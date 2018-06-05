app.service('UsersService', function($http){


    this.changeStatus = function(user_id, status, successCallback, errorCallback){
        $http({
            method: 'PATCH',
            url: "http://asesoriaspar.ronintopics.com/index.php/users/"+user_id+"/status/"+status
        }).then(function(success){
            successCallback(success);
        }, function(error){
            errorCallback(error);
        });
    }

    
    this.getUsers = function(successCallback, errorCallback){
        $http({
            method: 'GET',
            url: "http://asesoriaspar.ronintopics.com/index.php/users/staff"
        }).then(function(success){
            successCallback(success);
        }, function(error){
            errorCallback(error);
        });
    }

    this.searchUsers = function(data,successCallback, errorCallback){
        $http({
            method: 'GET',
            url: "http://asesoriaspar.ronintopics.com/index.php/users/search/"+data+"/staff"
        }).then(function(success){
            successCallback(success);
        }, function(error){
            errorCallback(error);
        });
    }

    

    this.addUser = function(user, successCallback, errorCallback){
        $http({
            method: 'POST',
            url: "http://asesoriaspar.ronintopics.com/index.php/users",
            data: {
                email: user.email,
                password: user.pass,
                role: user.role
            }
        }).then(function(success){
            successCallback(success) 
        }, function(error){
            errorCallback(error)
        });
    }

    this.updateUser = function(user, successCallback, errorCallback){
        $http({
            method: 'PUT',
            url: "http://asesoriaspar.ronintopics.com/index.php/users/"+user.id,
            data: {
                email: user.email,
                password: user.pass,
                role: user.role
            }
        }).then(function(success){
            successCallback(success) 
        }, function(error){
            errorCallback(error)
        });
    }
    
    this.deleteUser = function(user_id, successCallback, errorCallback){
        $http({
            method: 'DELETE',
            url: "http://asesoriaspar.ronintopics.com/index.php/users/"+user_id
        }).then(function (success){
            successCallback(success);
        },function (error){
            errorCallback(error);
        });
    }
    
    

});