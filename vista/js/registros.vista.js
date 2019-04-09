var app = {},
    vars = {
      TIPO_RIEGO_TEMP : null,
      COD_REGISTRO_TEMP : null,
      BTN_DETALLE_TEMP : null
    };

app.init = function(){
  //this.setDOM();
  //this.setEventos();
  this.setTemplates();
  this.setEventos();
  this.obtenerDataFiltro();
};

app.setTemplates = function(){
  var tpl8 ={};
  tpl8.listadoCabecera = Handlebars.compile($("#tpl8ListadoCabecera").html());
  tpl8.combo = Handlebars.compile($("#tpl8Combo").html());
  tpl8.comboNivel = Handlebars.compile($("#tpl8ComboNivel").html());

  this.tpl8 = tpl8;
};

app.setEventos  = function(){
  var btnBuscar = $("#btnbuscar"),
      txtBuscar = $("#txtbuscar"),
      txtMdlCampo = $("#txtmdlcampo"),
      txtMdlNN1 = $("#txtmdlnumeronivel1"),
      txtMdlNN2 = $("#txtmdlnumeronivel2"),
      frmCabecera = $("#frmgrabarcabecera");

  btnBuscar.on("click", function(e){
    app.buscar();
  });

  txtBuscar.on("keyup", function(e){
    var v = this.value.toLowerCase();
    $("table tbody tr:not(.tr-null)").each(function() {
        var dis = $(this);
        console.log(dis);
        if(dis.text().toLowerCase().indexOf(v) > -1){
             dis.show();                        
         }
         else{
             dis.hide();
         }
    });
  });

  txtMdlCampo.on("change", function(e){
    app.cargarNivelUno(this.value);
  });

  txtMdlNN1.on("change", function(e){
    app.cargarNivelDos(this.value);
  });

  txtMdlNN2.on("change", function(e){
    app.cargarNivelTres(this.value);
  });

  frmCabecera.on("submit", function(e){
    e.preventDefault();
    app.grabarCabecera();
  });


  btnBuscar = null;
  txtBuscar = null;
  txtMdlCampo = null;
  txtMdlNN1 = null;
  txtMdlNN2 = null;
  frmCabecera =null;
};

app.cargarNivelUno =function(){
  var tpl8Combo = this.tpl8.comboNivel,
      codCampo = $("#txtmdlcampo").val(),
      $nn1 = $("#txtmdlnumeronivel1"),
      $nn2 = $("#txtmdlnumeronivel2"),
      $nn3 = $("#txtmdlnumeronivel3"),
      blkNivel2 = $("#blknivel2"),
      fn = function(xhr){
        var datos = xhr.data,
            rotulo = "Seleccionar módulo";

        if (datos.tipo_riego == "0"){
           rotulo = "Seleccionar jirón";
           blkNivel2.hide();
           $nn2.attr("required", false);
        } else {
           blkNivel2.show();
           $nn2.attr("required", true);
        }
        $nn1.html(tpl8Combo({opciones: datos.niveles, rotulo: rotulo}));
        $nn2.empty();
        $nn3.empty();
      };

  if (codCampo == "*" || codCampo == ""){
    $nn1.empty();
    $nn2.empty();
    $nn3.empty();
    return;
  }

  new Ajxur.Api({
    modelo: "RegistroEvaluacion",
    metodo: "cargarNivelUno",
    data_in : {
      p_codCampo : codCampo
    }
  },fn);
};

app.cargarNivelDos =function(){
  var tpl8Combo = this.tpl8.comboNivel,
      numeroNivel1 =  $("#txtmdlnumeronivel1").val(),
      $nn2 = $("#txtmdlnumeronivel2"),
      $nn3 = $("#txtmdlnumeronivel3"),
      fn = function(xhr){
        var datos = xhr.data,
            rotulo;

        if (datos.tipo_riego == "1"){
          $nn2.html(tpl8Combo({opciones: datos.niveles, rotulo: "Seleccionar turno"}));
          $nn3.empty();
        } else {
          $nn2.empty();
          $nn3.html(tpl8Combo({opciones: datos.niveles, rotulo: "Seleccionar cuartel"}));
        }

      };

  if (numeroNivel1 == "*" || numeroNivel1 == ""){
    $nn2.empty();
    $nn3.empty();
    return;
  }

  new Ajxur.Api({
    modelo: "RegistroEvaluacion",
    metodo: "cargarNivelDos",
    data_in : {
      p_codCampo : $("#txtmdlcampo").val(),
      p_numeroNivel1 : numeroNivel1
    }
  },fn);
};

app.cargarNivelTres =function(numeroNivel2){
  var tpl8Combo = this.tpl8.comboNivel,
      fn = function(xhr){
        var datos = xhr.data;
        $("#txtmdlnumeronivel3").html(tpl8Combo({opciones: datos.niveles, rotulo: "Seleccionar válvula"}));
      };

  if (numeroNivel2 == "*" || numeroNivel2 == ""){
    $nn3.empty();
    return;
  }


  new Ajxur.Api({
    modelo: "RegistroEvaluacion",
    metodo: "cargarNivelTres",
    data_in : {
      p_codCampo : $("#txtmdlcampo").val(),
      p_numeroNivel1 : $("#txtmdlnumeronivel1").val(),
      p_numeroNivel2 : numeroNivel2
    }
  },fn);
};

app.buscar = function(){

  var tpl8ListadoCabecera = this.tpl8.listadoCabecera,
      codCampo = $("#cbocampo").val(),
      fechaRegistro = $("#txtfecha").val(),
      codEvaluador = $("#cboevaluador").val(),
      tipoEvaluacion = $("#cbotipoevaluacion").val(),
      numeroNivel1 = $("#txtnumeronivel1").val(),
      numeroNivel2 = $("#txtnumeronivel2").val(),
      numeroNivel3 = $("#txtnumeronivel3").val(),
      $mensaje = $("#txtmensaje"),
      fn = function(xhr){
          if (xhr.rpt) {
            var _data = xhr.data,
                listadoCabecera = $("#listadoCabecera");

            listadoCabecera.html(tpl8ListadoCabecera(_data));
            listadoCabecera = null;
          }else{
            console.error(xhr.msj);
          }

        $mensaje.html("");
      },
      fnError = function(error){
        $mensaje.html("");
      };
 
   $mensaje.html("Buscando...");

   new Ajxur.Api({
    modelo: "RegistroEvaluacion",
    metodo: "obtenerCabeceras",
    data_in: {
      p_codCampo : codCampo,
      p_fechaRegistro : fechaRegistro,
      p_codEvaluador : codEvaluador,
      p_tipoEvaluacion : tipoEvaluacion,
      p_numeroNivel1: numeroNivel1,
      p_numeroNivel2: numeroNivel2,
      p_numeroNivel3: numeroNivel3
    }
  },fn, fnError);
};

app.obtenerDataFiltro =function(){
  var tpl8Combo = this.tpl8.combo;
  var fn = function (xhr){
      if (xhr.rpt) {
        var datos = xhr.data,
            cboCampo = $("#cbocampo"),
            cboCampoEditar = $("#txtmdlcampo"),
            cboEvaluador = $("#cboevaluador"),
            cboEvaluadorEditar = $("#txtmdlevaluador");

        cboCampo.html(tpl8Combo({opciones: datos.campos, rotulo: "Todos los campos"}));
        cboEvaluador.html(tpl8Combo({opciones: datos.evaluadores, rotulo: "Todos los evaluadores"}));

        cboCampoEditar.html(tpl8Combo({opciones: datos.campos, rotulo: "Seleccionar campo"}));
        cboEvaluadorEditar.html(tpl8Combo({opciones: datos.evaluadores, rotulo: "Seleccionar evaluador"}));

        cboEvaluadores = null;
        cboCampos = null;
      }else{
        console.error(xhr.msj);
      }
  };

  new Ajxur.Api({
    modelo: "RegistroEvaluacion",
    metodo: "obtenerDataFiltro"
  },fn);
};

app.eliminar = function($this, cod_registro, rotuloParcela){
  var fn = function (xhr){
      if (xhr.rpt) {
        var datos = xhr.data;
        $this.parentElement.parentElement.remove();
        alert("Registro "+rotuloParcela+" eliminado.");
      }else{
        console.error(xhr.msj);
      }
  };

  if (cod_registro.length <= 0 || cod_registro == ""){
    return;
  }

  if (!confirm("¿Desea eliminar el registro seleccionado: "+rotuloParcela+"?")){
    return;
  }

  new Ajxur.Api({
    modelo: "RegistroEvaluacion",
    metodo: "eliminarCabecera",
    data_in : {
      p_codRegistro : cod_registro
    }
  },fn);
};

app.leerEditar = function(cod_registro, rotuloParcela){
  var tpl8Combo = this.tpl8.comboNivel,
      fn = function (xhr){
      if (xhr.rpt) {
        var datos = xhr.data,
           registro = datos.registro,
           $modal = $("#mdlEditarCabecera"),
           blkNivel2 = $("#blknivel2"),
           $nn1 = $("#txtmdlnumeronivel1"),
           $nn2 = $("#txtmdlnumeronivel2"),
           $nn3 = $("#txtmdlnumeronivel3");

        $modal.find(".modal-header h3").html("EDITAR ENVÍO: "+registro.nombre_campo+" "+rotuloParcela);

        $("#txtmdlcampo").val(registro.cod_campo);

        if (registro.tipo_riego == "0"){
          $nn1.html(tpl8Combo({opciones: datos.data_1, rotulo: "Seleccionar jirón"}));
          $nn2.empty();
          blkNivel2.hide();
          $nn2.attr("required", false);
          $nn3.html(tpl8Combo({opciones: datos.data_3, rotulo: "Seleccionar cuartel"}));
        } else {
          $nn1.html(tpl8Combo({opciones: datos.data_1, rotulo: "Seleccionar módulo"}));
          $nn2.html(tpl8Combo({opciones: datos.data_2, rotulo: "Seleccionar turno"}));
          blkNivel2.show();
          $nn2.attr("required", true);
          $nn3.html(tpl8Combo({opciones: datos.data_3, rotulo: "Seleccionar válvula"}));
        }

        $nn1.val(registro.numero_nivel_1);
        $nn2.val(registro.numero_nivel_2);
        $nn3.val(registro.numero_nivel_3);

        $("#txtmdlfecharegistro").val(registro.fecha_evaluacion);
        $("#txtmdlevaluador").val(registro.cod_evaluador);
        $("#lbltipoevaluacion").html(registro.formulario);

        $modal.modal("show");        
        
        vars.COD_REGISTRO_TEMP = registro.cod_registro;
      }else{
        console.error(xhr.msj);
      }
  };

  if (cod_registro.length <= 0 || cod_registro == ""){
    return;
  }

  new Ajxur.Api({
    modelo: "RegistroEvaluacion",
    metodo: "leerEditarCabecera",
    data_in : {
      p_codRegistro : cod_registro
    }
  },fn);
};

app.grabarCabecera = function(){
  var self = this,
      fn = function (xhr){
          if (xhr.rpt) {
            var datos = xhr.data;
            self.buscar();
            $("#mdlEditarCabecera").modal("hide");
            alert("Registro guardado.");

          }else{
            console.error(xhr.msj);
          }
      };

  /*Are you sure 'bout this?*/
  if (!confirm("Los cambios realizados son permanentes. ¿Confirmar?")){
    return;
  }

  if (vars.COD_REGISTRO_TEMP == null){
    alert("No se ha guardado el código de registro, vuelva abrir la ventana de edición.");
    return;
  }

  /*Get da data*/
  var codCampo = $("#txtmdlcampo").val(),
      numeroNivel1 = $("#txtmdlnumeronivel1").val(),
      numeroNivel2 = $("#txtmdlnumeronivel2").val(),
      numeroNivel3 = $("#txtmdlnumeronivel3").val(),
      fechaRegistro = $("#txtmdlfecharegistro").val(),
      evaluador = $("#txtmdlevaluador").val();

  new Ajxur.Api({
    modelo: "RegistroEvaluacion",
    metodo: "editarCabecera",
    data_in : {
      p_codRegistro : vars.COD_REGISTRO_TEMP,
      p_codCampo : codCampo,
      p_numeroNivel1 : numeroNivel1,
      p_numeroNivel2 : numeroNivel2,
      p_numeroNivel3 : numeroNivel3,
      p_fechaRegistro : fechaRegistro,
      p_codEvaluador : evaluador
    }
  },fn);    
};

app.detalle = function($this, cod_registro, cod_formulario_evaluacion){
  /*Crear varias sub TR debajo
  1.- consultar detalles (by item number, y obtener cod_registro_detalle)
      []: cod_registro_detalle, item iorder by item
  2.- listarlos
  3.- btn open (guardar item global)
  */
  var fn = function (xhr){
      if (xhr.rpt) {
        var datos = xhr.data,
            $tr = $($this.parentElement.parentElement);

        $tr.after(app.renderDetalle(datos,cod_formulario_evaluacion));
        vars.BTN_DETALLE_TEMP = $this;
      } else {
        console.error(xhr.msj);
      }
  };

  $(".rw-subdetalle").remove();

  if (vars.BTN_DETALLE_TEMP != null && vars.BTN_DETALLE_TEMP == $this){    
    vars.BTN_DETALLE_TEMP = null;
    return;
  }

  new Ajxur.Api({
    modelo: "RegistroEvaluacion",
    metodo: "verDetalles",
    data_in : {
      p_codRegistro : cod_registro
    }
  },fn);
};

app.renderDetalle = function(arregloDetalle,cod_formulario_evaluacion){
  var html = "";

  for (var i = 0; i < arregloDetalle.length; i++) {
    html+= '<tr class="rw-subdetalle">';
      html+= '<td></td>';
      html+= '<td colspan="7"><button class="btn btn-info btn-xs" onclick="app.cargarDetalle('+cod_formulario_evaluacion+','+arregloDetalle[i].cod_registro_detalle+')">VER REGISTRO '+arregloDetalle[i].item+'</a></td>';
    html+= '</tr>';
  };

  return html;
};

$(document).ready(function(){
  app.init();
});

