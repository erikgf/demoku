var app = {},
  _TEMPID = -1,
  _ACCION = "agregar",
  _CLASE = "Personal",
  DT = null;

app.init = function(){
  this.setDOM();
  this.setEventos();
  this.setTemplate();

  app.llenarCargos();
  app.listar();
};

app.setDOM = function(){
  var DOM = {};

  DOM.listado = $("#listado");
  DOM.modal = $("#mdlRegistro");
  DOM.modalCambiarClave = $("#mdlCambiarClave");
  DOM.frmGrabar = $("#frmgrabar");
  DOM.mdlHeader = DOM.modal.find(".modal-header h3");

  DOM.txtDni = DOM.frmGrabar.find("#txtdni");
  DOM.txtNombres = DOM.frmGrabar.find("#txtnombres");
  DOM.txtApellidoPaterno = DOM.frmGrabar.find("#txtapellidopaterno");
  DOM.txtApellidoMaterno = DOM.frmGrabar.find("#txtapellidomaterno");
  DOM.txtCelular = DOM.frmGrabar.find("#txtcelular");
  DOM.txtEmail = DOM.frmGrabar.find("#txtemail");
  DOM.cboCargo = DOM.frmGrabar.find("#cbocargo");
  DOM.cboEstado = DOM.frmGrabar.find("#cboestado");
  DOM.txtFechaIngreso = DOM.frmGrabar.find("#txtfechaingreso");

  DOM.frmGrabarClave = $("#frmgrabarclave");
  DOM.txtDniClave = DOM.frmGrabarClave.find("#txtdniclave");
  DOM.txtPersonalClave = DOM.frmGrabarClave.find("#txtpersonalclave");
  DOM.txtNuevaClave = DOM.frmGrabarClave.find("#txtnuevaclave");

  this.DOM = DOM;
};

app.setEventos  = function(){
  var self = this,
      DOM  = self.DOM;

  DOM.modal.on("hidden.bs.modal",function(e){
    self.limpiar();
  });

  DOM.modalCambiarClave.on("hidden.bs.modal",function(e){
    self.limpiarClave();
  });

  DOM.modalCambiarClave.on("shown.bs.modal",function(e){
    self.DOM.txtDniClave.chosen("destroy").chosen({allow_single_deselect:true});
  });

  DOM.frmGrabar.on("submit", function(e){
    e.preventDefault();
    swal({
          title: "Confirme",
          text: "¿Esta seguro de grabar los datos ingresados?",
          showCancelButton: true,
          confirmButtonColor: '#3d9205',
          confirmButtonText: 'Si',
          cancelButtonText: "No",
          closeOnConfirm: false,
          closeOnCancel: true,
          imageUrl: "../images/pregunta.png"
        },
        function(isConfirm){ 
          if (isConfirm){
            self.grabar();
          }
      });
  });

  DOM.frmGrabarClave.on("submit", function(e){
    e.preventDefault();
    swal({
          title: "Confirme",
          text: "¿Esta seguro de cambiar clave al usuario?",
          showCancelButton: true,
          confirmButtonColor: '#3d9205',
          confirmButtonText: 'Si',
          cancelButtonText: "No",
          closeOnConfirm: false,
          closeOnCancel: true,
          imageUrl: "../images/pregunta.png"
        },
        function(isConfirm){ 
          if (isConfirm){
            self.grabarCambioClave();
          }
      });
  });

  var fSoloNumeros = function(e){ return Util.soloNumeros(e);};
  DOM.txtDni.on("keypress", fSoloNumeros);
  DOM.txtCelular.on("keypress", fSoloNumeros);
  DOM.txtDniClave.on("keypress", fSoloNumeros);

  var fSoloLetras = function(e){ return Util.soloLetras(e);};

  DOM.txtNombres.on("keypress", fSoloLetras);
  DOM.txtApellidoPaterno.on("keypress", fSoloLetras);
  DOM.txtApellidoMaterno.on("keypress", fSoloLetras);
};

app.setTemplate = function(){
  var tpl8 = {};
  tpl8.listado = Handlebars.compile($("#tpl8Listado").html());
  tpl8.combo = Handlebars.compile($("#tpl8Combo").html());
  tpl8.dni_clave = Handlebars.compile($("#tpl8DniClave").html());

  this.tpl8 = tpl8;
};

app.limpiar = function(){
  var DOM = this.DOM;
  DOM.frmGrabar[0].reset();

  _ACCION = "agregar";
  _TEMPID = -1;
};

app.limpiarClave = function(){
  var DOM = this.DOM;
  DOM.frmGrabarClave[0].reset();
};

app.agregar = function(){
  _ACCION = "agregar";
  _TEMPID = -1;
  this.DOM.mdlHeader.html((_ACCION+" "+_CLASE).toUpperCase());
};

app.editar = function(cod){
  _ACCION = "editar";
  _TEMPID = cod;
  var DOM = this.DOM,
    fn = function (xhr){
      var datos = xhr.datos;
      if (datos.rpt) {      
        var data = datos.data;
        DOM.txtDni.val(data.dni);
        DOM.txtNombres.val(data.nombres);
        DOM.txtApellidoPaterno.val(data.apellido_paterno);
        DOM.txtApellidoMaterno.val(data.apellido_materno);
        DOM.txtCelular.val(data.celular);
        DOM.txtEmail.val(data.email);
        DOM.cboCargo.val(data.cod_cargo);
        DOM.txtFechaIngreso.val(data.fecha_ingreso);
        DOM.cboEstado.val(data.estado);
      }else{
        console.error(datos.msj);
      }
  };

  DOM.mdlHeader.html((_ACCION+" "+_CLASE).toUpperCase());
  new Ajxur.Api({
    modelo: _CLASE,
    metodo: "leerDatos",
    data_in : {
      p_codPersonal: cod
    }
  },fn);
};


app.eliminar = function(cod){
  var DOM = this.DOM,
    fn = function (xhr){
      var datos = xhr.datos;
      if (datos.rpt) {      
         swal("Exito", datos.msj, "success");
         app.listar();
      }else{
        console.error(datos.msj);
      }
  };

  swal({
          title: "Confirme",
          text: "¿Esta seguro que desea eliminar el registro?",
          showCancelButton: true,
          confirmButtonColor: '#3d9205',
          confirmButtonText: 'Si',
          cancelButtonText: "No",
          closeOnConfirm: true,
          closeOnCancel: true,
          imageUrl: "../images/pregunta.png"
        },
        function(isConfirm){ 
          if (isConfirm){
              new Ajxur.Api({
                modelo: _CLASE,
                metodo: "eliminar",
                data_in : {
                  p_codPersonal: cod
                }
              },fn);
          }
      });

};

app.grabar = function(){
  var DOM = this.DOM,
      fn = function(xhr){
        console.log(xhr);
        var datos = xhr.datos;
        if (datos.rpt){
          swal("Exito", datos.msj, "success");
          DOM.modal.modal("hide");
          app.listar();
        } else {
          swal("Error", datos.msj, "error");
        } 
      };

  new Ajxur.Api({
    modelo: _CLASE,
    metodo: _ACCION,
    data_in :  {
      p_dni : DOM.txtDni.val(),
      p_nombres : DOM.txtNombres.val(),
      p_apellidoPaterno : DOM.txtApellidoPaterno.val(),
      p_apellidoMaterno : DOM.txtApellidoMaterno.val(),
      p_celular : DOM.txtCelular.val(),
      p_email : DOM.txtEmail.val(),
      p_codCargo : DOM.cboCargo.val(),
      p_fechaIngreso: DOM.txtFechaIngreso.val(),
      p_estado : DOM.cboEstado.val(),
      p_codPersonal : _TEMPID
    }

  },fn);
};


app.grabarCambioClave = function(){
  var DOM = this.DOM,
      fn = function(xhr){
        var datos = xhr.datos;
        if (datos.rpt){
          swal("Exito", datos.msj, "success");
          DOM.modalCambiarClave.modal("hide");
        } else {
          swal("Error", datos.msj, "error");
        } 
      };

  new Ajxur.Api({
    modelo: _CLASE,
    metodo: "cambiarClave",
    data_in :  {
      p_codPersonal : DOM.txtDniClave.val(),
      p_clave : DOM.txtNuevaClave.val()
    }
  },fn);
};

app.listar = function(){
  var DOM = this.DOM,
      tpl8Listado = this.tpl8.listado,
      tpl8DniClave = this.tpl8.dni_clave;

  var fn = function (xhr){
    var datos = xhr.datos;
      if (datos.rpt) {
        if (DT) { DT.fnDestroy(); DT = null; }
        DOM.txtDniClave.html(tpl8DniClave(datos.data));
        DOM.listado.html(tpl8Listado(datos.data));
        DT = DOM.listado.find("table").dataTable({
          "aaSorting": [[0, "asc"]]
        });
      }else{
        swal("Error", datos.msj, "error");
      }
  };

  new Ajxur.Api({
    modelo: _CLASE,
    metodo: "listar"
  },fn);
};

app.llenarCargos = function(){
  var DOM = this.DOM,
      tpl8 = this.tpl8.combo;
  var fn = function (xhr){
    var datos = xhr.datos;
      if (datos.rpt) {
        DOM.cboCargo.html(tpl8({opciones: datos.r, rotulo: "cargo"}));
      }else{
        console.error(datos.msj);
      }
  };

  new Ajxur.Api({
    modelo: "Cargo",
    metodo: "obtenerCargos"
  },fn);
};

app.verClave = function(){
  this.DOM.txtNuevaClave[0].type = "text";
};

app.esconderClave = function(){
  this.DOM.txtNuevaClave[0].type = "password";
};

app.cambiarClave = function(){
  this.DOM.modalCambiarClave.modal("show");
}

$(document).ready(function(){
  app.init();
});

