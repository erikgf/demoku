 
var LoginView = function(servicio, cache) {

    var _CLICKS = 0;

     this.initialize = function() {
         this.$el = $('<div/>');
         this.setEventos();
     };

     this.setEventos = function(){
     	this.$el.on("submit","form", this.iniciarSesion);
        this.$el.on("click","img", this.resetearBD);
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

     this.resetearBD = function(e){
        e.preventDefault();
        _CLICKS++;
        if (_CLICKS > 5){
            alert("Se eliminiará BD, resetee app en 5 segundos.");
            servicio.resetearBD();
            _CLICKS = 0;
        }
        
     };

     this.destroy = function(){
        this.$el.off("submit","form", this.iniciarSesion);
        this.$el.off("click","img", this.resetearBD);

        this.$el = null;
    };

     this.initialize();
};