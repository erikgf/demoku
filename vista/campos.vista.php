<?php

include '../datos/local_config_web.php';
include 'session.vista.php';
$TITULO_PAGINA = "Campos";

?>
<html lang="en"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="CompuCodex, Cayalti">
    <meta name="generator" content="CompuCodex">
    <title>Campos</title>

    <!-- Bootstrap core CSS -->
    <?php 
      include '_css/bootstrap.css.php';
      include '_css/dataTable.css.php';
    ?>

    <link rel="stylesheet" type="text/css" href="css/estilos.css">
    <!-- Custom styles for this template -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700,900" rel="stylesheet">-->
    <!-- Custom styles for this template -->
    <!-- <link href="blog.css" rel="stylesheet"> -->
  </head>
  <body>

  <?php include 'cabecera.vista.php' ?>

 <main role="main" class="container">
    <div class="row">
      
    </div>
    <div class="row">
      <div class="col-sm-12">
        <table id="table_id" class="tabla-nueva display table-bordered">
          <thead>
              <tr>
                  <th>Column 2</th>
              </tr>
          </thead>
          <tbody>
              <tr>
                  <td>Row 1 Data 1</td>
                  <td>Row 1 Data 2</td>
              </tr>
              <tr>
                  <td>Row 2 Data 1</td>
                  <td>Row 2 Data 2</td>
              </tr>
          </tbody>
      </table>
      </div>
    </div><!-- /.row -->

    <hr>
  </main><!-- /.container -->

<?php include 'pie.vista.php'; ?>

<?php 
  include '_js/jquery.js.php'; 
  include '_js/bootstrap.js.php'; 
  include '_js/dataTable.js.php';
?>

<script type="text/javascript">
  $(document).ready( function () {
  //  $('#table_id').DataTable();
  });
  
</script>
</body>

</html>
