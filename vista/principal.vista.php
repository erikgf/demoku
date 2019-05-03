<?php

include '../datos/local_config_web.php';
include 'session.vista.php';
$TITULO_PAGINA = "Página Principal";

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
    <div class="block-superior">
        <div class="Header-jpeg login" style="height:100px"></div>
        <div class="PageBackgroundGlare login" style="margin-top:100px">
          <div class="PageBackgroundGlareImage"></div>
        </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <img src="../imagenes/cayalti.png" class="img-responsive">
      </div>
    </div><!-- /.row -->

    <hr>
  </main><!-- /.container -->

<?php include 'pie.vista.php'; ?>

<?php 
  include '_js/jquery.js.php'; 
  include '_js/bootstrap.js.php'; 
?>
  <script src="../util/Ajxur.js" type="text/javascript"></script>
  <script src="js/Util.js" type="text/javascript"></script>
</body>

</html>
