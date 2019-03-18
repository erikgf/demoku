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
    <h3>Consultas SQL</h3>
    <div class="row">
      <div class="col-xs-12">
          <div class="control-group">
            <label class="control-label">SQL: </label>
            <textarea id="txtsql" class="form-control" rows="6"></textarea>
          </div>
      </div>
    </div>
    <br>
    <div class="row">
      <div class="col-xs-12 col-sm-3 col-md-2">
          <button class="btn btn-block btn-primary btn-lg" id="btn-ejecutar">EJECUTAR SQL</button>
      </div>
      <div class="col-xs-12 col-sm-3 col-md-2">
          <a href="docs/documentacion_apoyo_sql.docx">Ver documento de apoyo</a>
      </div>
    </div>
    <br>
    <div class="row">
      <div class="col-xs-12">
          <div class="control-group">
            <label class="control-label">Resultados: </label>
            <p id="txtresultados"></p>
          </div>
      </div>
    </div>

    <hr>
  </main><!-- /.container -->

<?php include 'pie.vista.php'; ?>

<?php 
  include '_js/jquery.js.php'; 
  include '_js/bootstrap.js.php'; 
?>
  <script src="../util/Ajxur.js" type="text/javascript"></script>
  <script src="js/sql.vista.js" type="text/javascript"></script>
</body>

</html>
