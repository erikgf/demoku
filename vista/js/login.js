var app = {}; 

app.init = function(){
  this.setDOM();
  this.setEventos();
};

app.setDOM = function(){
  var DOM = {};

  DOM.frmIniciar = $("#frminiciar");
  DOM.txtDni = $("#txtdni");
  DOM.txtClave = $("#txtclave");
  DOM.blkAlert = $("#blkalert");
  DOM.chkRecordar = $("#chkrecordar");

  this.DOM = DOM;
};

app.setEventos  = function(){
  var self = this;

  self.DOM.frmIniciar.on("submit", function(e){
    e.preventDefault();
    self.iniciarSesion();
  });
};

app.limpiar = function(){
  this.DOM.frmIniciar[0].reset();
};

app.iniciarSesion = function(){
  var DOM = this.DOM,
      fn = function(xhr){
        console.log(xhr);
        var datos = xhr.datos;
        if (datos.rpt){
          Util.alert(DOM.blkAlert, {tipo: "s", "mensaje": datos.msj});
          window.location.href = "principal.vista.php";
        } else {
          Util.alert(DOM.blkAlert, {tipo: "e", "mensaje": datos.msj});
        }
      };

  new Ajxur.Api({
    modelo: "Sesion",
    metodo: "iniciarSesion",
    data_in :  {
      p_dni : DOM.txtDni.val(),
      p_clave : DOM.txtClave.val(),
      p_recordar : DOM.chkRecordar[0].checked
    }
  },fn);
};


$(document).ready(function(){
  app.init();
});

