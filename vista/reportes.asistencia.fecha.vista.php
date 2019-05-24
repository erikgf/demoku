<?php

include '../datos/local_config_web.php';
include 'session.vista.php';
$TITULO_PAGINA = "Reporte Asistencias Fecha";
$fechaHoy = date('Y-m-d');

?>
<html lang="en"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="CompuCodex, Cayalti">
    <meta name="generator" content="CompuCodex">
    <title>AgriCayaltí</title>

    <!-- Bootstrap core CSS -->
    <?php 
      include '_css/bootstrap.css.php';
    ?>
    <link rel="stylesheet" type="text/css" href="css/estilos.css">
  </head>
  <body>

  <?php include 'cabecera.vista.php' ?>

 <main role="main" class="container">
    <h3>Reporte Asistencia por Fecha</h3>
    <div class="row">
      <div class="col-xs-12 col-sm-3 col-md-2">
          <div class="control-group">
            <label class="control-label">Desde: </label>
            <input name="txtfechainicio" id="txtfechainicio" class="form-control" type="date" value="<?php echo $fechaHoy; ?>"/>
          </div>
      </div>
      <div class="col-xs-12 col-sm-3 col-md-2">
          <div class="control-group">
            <label class="control-label">Hasta: </label>
            <input name="txtfechafin" id="txtfechafin" class="form-control" type="date" value="<?php echo $fechaHoy; ?>"/>
          </div>
      </div>
      <div class="col-xs-12 col-sm-3 col-md-2">
          <br>
          <button class="btn btn-block btn-success" id="btn-buscar">BUSCAR</button>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-5 col-xs-12">
        <h5>Resultados: </h5>
        <table class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
          <thead>
            <tr>
              <th width="150px">Fecha</th>
              <th width="120px">Opc.</th>
            </tr>
          </thead>
          <tbody id="tbodyresultados">
            <script id="tpl8Listado" type="handlebars-x">
            {{#this}}
              <tr>
                <td>{{fecha}}</td>
                <td>
                   <button class="btn btn-info"  title="Ver Detalle" onclick="app.verDetalle('{{fecha}}','{{fecha_raw}}')">
                      <i class="glyphicon glyphicon-file"></i>
                    </button>
                    <button class="btn btn-success" title="Exportar"  onclick="app.exportar('{{fecha}}','{{fecha_raw}}')">
                      <i class="glyphicon glyphicon-download"></i>
                    </button>
                </td>
              </tr>
            {{else}}
              <tr>
                <td class="td-null" colspan="2">
                   <i>Sin registros para mostrar</i>
                </td>
              </tr>
            {{/this}}
            </script>
          </tbody>
        </table>
      </div>
    </div>
    <!-- 
    <div class="row">
      <h3>Ver Detalle: </h3>
      <div class="col-xs-6">
        <div class="group-control">
          <label>Punto Acceso</label>
          <p>001 - NOMBRE DEL PUNTO ACCESO</p>
        </div>
      </div>
      <div class="col-xs-3">
        <div class="group-control">
          <label>Fecha</label>
          <p>10/05/2019</p>
        </div>
      </div>
    </div>
    -->
    <div id="blk-detalle">
        <script id="tpl8Detalle" type="handlebars-x">
          <div class="row">
            <h3>Ver Detalle: </h3>
            <div class="col-xs-offset-9 col-xs-3">
              <div class="group-control">
                <label>Fecha</label>
                <p>{{fecha}}</p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-12 col-md-12">
                <table style="font-size:13px" class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                    <thead>
                      <tr>
                        <th width="100px">DNI</th>
                        <th>Apellidos y Nombres</th>
                        <th width="200px">>Planilla</th>
                        <th width="100px">Turno</th>
                        <th width="100px">Ingreso</th>
                        <th width="100px">Salida</th>
                        <th width="75px">ID Pto. Asistencia</th>
                        <th >Pto. Asistencia</th>
                        <th width="100px">IdResponsable</th>
                        <th>Responsable</th>
                      </tr>
                    </thead>
                    <tbody>
                    {{#registros}}
                      <tr>
                        <td>{{dni_asistencia}}</td>
                        <td>{{apellidos_nombres}}</td>
                        <td>{{planilla}}</td>
                        <td>{{turno}}</td>
                        <td>{{ingreso}}</td>
                        <td>{{salida}}</td>
                        <td>{{idpuntoacceso}}</td>
                        <td>{{puntoacceso}}</td>
                        <td>{{idresponsable}}</td>
                        <td>{{responsable}}</td>
                      </tr>
                    {{else}}
                      <tr>
                        <td class="td-null" colspan="9">
                           <i>Sin registros para mostrar</i>
                        </td>
                      </tr>
                    {{/registros}}
                    </tbody>
                </table>
            </div>
          </div>
        </script>
    </div>
  
    <hr>
  </main><!-- /.container -->

<?php include 'pie.vista.php'; ?>

<?php 
  include '_js/jquery.js.php'; 
  include '_js/bootstrap.js.php'; 
  include '_js/dataTable.js.php';
?>
  <script src="../util/Ajxur.js" type="text/javascript"></script>
  <script src="../assets/js/handlebars.min.js"></script>
  <script src="js/reportes.asistencia.fecha.vista.js" type="text/javascript"></script>
</body>

</html>