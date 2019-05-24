<?php

include '../datos/local_config_web.php';
include 'session.vista.php';
$TITULO_PAGINA = "Gestión de Parcelas";
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

    <link rel="stylesheet" type="text/css" href="../plugin/bootstrap-selectpicker/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="css/estilos.css">

  </head>
  <body>

  <?php include 'cabecera.vista.php' ?>

 <main role="main" class="container">
    <form>
      <input id="txtparcelaccion" name="txtparcelaccion" value="" type="hidden">
      <h3>Gestión de Parcela</h3>
      <h4>Seleccionar campo / campaña</h4>
      <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-3">
          <div class="control-group">
            <label class="control-label">Campos: </label>
            <select required id="cbocampo" name="cbocampo" class="form-control"  data-live-search="true" title="Selecionar campo">
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-3">
          <div class="control-group">
            <label class="control-label">Siembra: </label>
            <select required  id="cbosiembra"  name="cbosiembra" class="form-control" title="Selecionar siembra">
            </select>
          </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
          <div class="control-group">
            <label class="control-label">Campaña: </label>
            <select required id="cbocampaña"  name="cbocampaña" class="form-control" title="Selecionar campaña">
            </select>
          </div>
        </div>
      </div>
      <hr>
      <h4>Datos base parcela</h4>
      <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-2">
          <div class="control-group">
            <label class="control-label">Fecha Inicio Campaña: </label>
            <input type="date" id="txtfechainicio"  name="txtfechainicio" class="form-control" required/>
          </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-2">
          <div class="control-group">
            <label class="control-label">Fecha Fin Campaña: </label>
            <input type="date"  id="txtfechafin" name="txtfechafin" class="form-control"/>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-2">
          <div class="control-group">
            <label class="control-label">Tipo de Riego: </label>
            <select id="cbotiporiego" name="cbotiporiego" class="form-control" required>
              <option value="1">GOTEO</option>
              <option value="0">GRAVEDAD</option>
            </select>
          </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
          <div class="control-group">
            <label class="control-label">Cultivo: </label>
            <select id="cbocultivo"  name="cbocultivo" class="form-control" required>
            </select>
          </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-2">
          <div class="control-group">
            <label class="control-label">Variedad: </label>
            <select id="cbovariedad" name="cbovariedad" class="form-control" required>
            </select>
          </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-2 col-lg-1">
          <div class="control-group">
            <label class="control-label">Área (ha): </label>
            <input type="numeric"  id="txtarea" name="txtarea" class="form-control"  value="1.00" required/>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-2">
          <div class="control-group">
            <label class="control-label">Módulo/Jirón: </label>
            <input type="text"  id="txtnn1" name="txtnn1" class="txtnn form-control" required/>
          </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-2" id="blkturno" style="display:none;">
          <div class="control-group">
            <label class="control-label">Turno: </label>
            <input type="text"  id="txtnn2" name="txtnn2" class="txtnn form-control"/>
          </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-2">
          <div class="control-group">
            <label class="control-label">Válvula/Cuartel: </label>
            <input type="text"  id="txtnn3" name="txtnn3" class="txtnn form-control" required/>
          </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-offset-2 col-lg-2">
          <div class="control-group">
            <label class="control-label">Rótulo: </label>
            <input type="text" readonly id="txtrotulo" name="txtrotulo" class="form-control"/>
          </div>
        </div>
      </div>
      <hr>
      <h4>Coordenadas / Ubicación</h4>
      <div class="row">
          <div class="col-xs-12">
            <button class="btn btn-danger" id="btnreseteardodibujo">RESETEAR POLÍGONO</button>
          </div>
      </div>
      <p></p>
      <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-9">
          <div id="mapa" style="height:550px; width:100%"></div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
          <div class="well">
            <small>Para dibujar una parcela, se debe dar click en los puntos deseados consecutivamente. Para finalizar/cerrarla se debe seleccionar el primer punto marcado.</small>
            <h5><b>Estado polígono:</b> <span id="txtestadopoligono">Dibujando</span></h5>
            <h5><b>Área aprox.:</b> <span id="txtareaproxima">-</span> ha</h5>
            <h5><b>Coordenadas:</b></h5>
             <ul id="txtcoordenadas"></ul>
          </div>
        </div>
      </div>

      <div class="control-group pull-right" style="margin: 20px 0;">
        <button class="btn btn-lg btn-default" id="btncancelar">CANCELAR</button>
        <button type="submit" class="btn btn-lg btn-success">GUARDAR</button>
      </div>
    </form>
  </main><!-- /.container -->
  <?php include 'pie.vista.php'; ?>
  <script id="tpl8Combo" type="handlebars-x">
      <option value="">Seleccionar opción</option>
       {{#.}}
          <option value="{{codigo}}">{{descripcion}}</option>
       {{/.}}
  </script>  

<?php 
  include '_js/jquery.js.php'; 
  include '_js/bootstrap.js.php'; 
?>

  <script src="../plugin/bootstrap-selectpicker/js/bootstrap-select.min.js" type="text/javascript"></script>
  <script src="../plugin/handlebars/handlebars.min.js" type="text/javascript"></script>
  <script src="../util/Ajxur.js" type="text/javascript"></script>
  <script src="js/Util.js" type="text/javascript"></script>
  <script src="js/gestion.parcelas.vista.js" type="text/javascript"></script>
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAxMsuT_-ZvPMuG69IfvROqFOUJIu6Wl5o&callback=gmapsLoaded&libraries=geometry,drawing" async defer></script>    
  <script src="js/_mapa.js" type="text/javascript"></script>

  <script type="text/javascript">
    var codP = <?php echo isset($_GET["cp"]) ? "'".$_GET["cp"]."'" : 'null'; ?>;
  </script>

  <!-- <script src="js/reporteador.vista.js" type="text/javascript"></script> -->
</body>

</html>
