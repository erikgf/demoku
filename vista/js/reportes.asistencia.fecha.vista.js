var app = {},
   DT = null;

app.init = function(){
  //this.setDOM();
  this.setTemplates();
  this.setEventos();

  $("#tbodyresultados").html(this.tpl8.listado([]));
};

app.setEventos  = function(){
  var self = this,
      btnBuscar = $("#btn-buscar"),
      btnGenerar = $("#btn-generar");

  btnBuscar.on("click", function(e){
    e.preventDefault();
    self.listar();
  });

  btnGenerar.on("click", function(e){
    e.preventDefault();
    self.generarExcel();
  });

  btnGenerar = null;
};

app.setTemplates = function(){
  var tpl8 = {};
  tpl8.listado = Handlebars.compile($("#tpl8Listado").html());
  tpl8.detalle = Handlebars.compile($("#tpl8Detalle").html());

  this.tpl8 = tpl8;
};


app.generarExcel = function(){
  var $fi = $("#txtfechainicio").val(),
      $ff = $("#txtfechafin").val(),
      strUrl;


    if ($fi == ""){
      alert("Fecha de desde no válida.");
      return;
    }  

    if ($ff == ""){
      alert("Fecha de desde no válida.");
      return;
    }     

    strUrl = "../controlador/reportes.xls.formularios.evaluacion.php?"+
                    "p_fi="+$fi+"&"+
                    "p_ff="+$ff; 

    window.open(strUrl,'_blank'); 
};

app.listar = function () {
  var self = this;
  var fn  =function(xhr){
    console.log(xhr);
    var datos = xhr.data;
    $("#tbodyresultados").html(self.tpl8.listado(datos));
  };

  new Ajxur.Api({
    metodo: "listarFechas",
    modelo: "Asistencia",
    data_out : [$("#txtfechainicio").val(), $("#txtfechafin").val()]
  }, fn);
};


app.verDetalle = function(fecha, fecha_raw){
  var self = this;
  var fn  =function(xhr){
    var datos = xhr.data,
        $blkDetalle = $("#blk-detalle");

      if (xhr.rpt) {
        if (DT) { DT.fnDestroy(); DT = null; }
        $blkDetalle.html(self.tpl8.detalle({registros: datos, fecha: fecha}));
        DT = $blkDetalle.find("table").dataTable({
          "aaSorting": [[2, "asc"]]
        });
      }else{
        console.error(datos.msj);
      }
  };

  new Ajxur.Api({
    metodo: "listarFechasDetalle",
    modelo: "Asistencia",
    data_out : [fecha_raw]
  }, fn);
};  

$(document).ready(function(){
  app.init();
});

