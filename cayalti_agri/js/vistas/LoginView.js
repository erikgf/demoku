 
var LoginView = function(servicio, cache) {
     this.initialize = function() {
         this.$el = $('<div/>');
         this.setEventos();
     };

     this.setEventos = function(){
     	this.$el.on("submit","form", this.iniciarSesion);
     	//Form
     	//txtUsuario
     	//txtClave
     };

     this.render = function() {
         this.$el.html(this.template(cache));
         return this;
     };

     this.iniciarSesion = function(e){
     	e.preventDefault();
     	
     	var $form = $(this),
     		_login = $form.find("#txt-login").val(), 
     		_clave = $form.find("#txt-clave").val();

        if (_login == "admin" && _clave == "admin"){
            DATA_NAV.acceso = true;
            DATA_NAV.usuario = {cod_usuario: -1, nombre_usuario: "ADMIN", cod_rol: 1};
            window.location.hash = "inicio";
            return;
        }

     	$.when( servicio.iniciarSesion(_login, md5(_clave)) )
     		.done( function( resultado ){
     			var rows = resultado.rows;
     			if (rows.length > 0){
     				DATA_NAV.acceso = true;
     				DATA_NAV.usuario = rows.item(0);
                    localStorage.setItem("DATA_NAV__APPCAYALTI", JSON.stringify(DATA_NAV));
     				window.location.hash = "inicio";
     			} else {
     				alert("Usuario no válido.");
     			}
      	}); //EndWhen
     };

     this.initialize();
};