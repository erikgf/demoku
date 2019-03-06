<?php

include '../datos/local_config_web.php';
include 'session.vista.php';
$TITULO_PAGINA = "Página Principal";
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
    <h3>Reporteador DEMO</h3>
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
          <button class="btn btn-block btn-success btn-lg" id="btn-generar">GENERAR EXCEL</button>
      </div>
    </div>

    <hr>
  </main><!-- /.container -->

<?php include 'pie.vista.php'; ?>

<?php 
  include '_js/jquery.js.php'; 
  include '_js/bootstrap.js.php'; 
?>
  <script src="js/reporteador.vista.js" type="text/javascript"></script>
</body>

</html>