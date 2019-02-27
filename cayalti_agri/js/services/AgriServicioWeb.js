var AgriServicioWeb = function() {
    var url,
        IP = "192.168.8.7";

    this.initialize = function(serviceURL) {
        //url = serviceURL ? serviceURL : "http://localhost/cayalti_agri_web/controlador/";
        url = serviceURL ? serviceURL : "http://"+IP+"/cayalti_agri_web/controlador/";
        var deferred = $.Deferred();
        deferred.resolve();
        return deferred.promise();
    };

    this.actualizarDatos = function() {
       return $.ajax({
                url: url,
                data: {modelo: "ActualizadorApp", "metodo": "actualizarDatos"},
                type: "post"
              });
    };

    this.masterKey = function(){
       return $.ajax({
                url: url,
                data: {modelo: "ActualizadorApp", "metodo": "masterKey"},
                type: "post"
         });
    };

    this.sincronizarDatos = function(JSONData) {
       return $.ajax({
                url: url,
                data: {modelo: "ActualizadorApp", "metodo": "sincronizarDatos", data_out:[JSONData]},
                type: "post"
              });
    };

}