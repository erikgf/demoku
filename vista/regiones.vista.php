<?php

include '../datos/local_config_web.php';
include 'session.vista.php';
$TITULO_PAGINA = "Gestión Regiones";
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

    <style type="text/css">

      .tabla-campos tbody tr{
        cursor: pointer;
      }
      .tabla-campos  tbody .tr-seleccionado {
        background: #d9edf7;
      }
      .tabla-campos  tbody tr:hover{
        background: #ececec;
      }
      .tabla-campos  tbody .tr-seleccionado:hover{
        background: #c6d7d8;
      }
      .bloque-detalle{
      }
      .bloque-detalle p{
        display: inline;
      }

      .btn-black{
        color: white;
        background: #565454;
      }

      .badge-success{
        background-color: #5cb85c;
      }

      .badge-danger{
        background-color: #d9534f;
      }
    </style>
  </head>
  <body>

  <?php include 'cabecera.vista.php' ?>

 <main role="main" class="container">
    <h3>Gestión de Regiones</h3>

    <div class="row">
      <div class="col-xs-6 col-md-3">
          <div class="control-group">
              <div class="input-group"> 
                  <span class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>
                  <input type="search" class="form-control" name="txtbuscar" id="txtbuscar" placeholder="Buscar...">
              </div>
          </div>
       </div>
       <div class="col-xs-6 col-md-offset-7 col-md-2">
          <div class="control-group">
              <label class="control-label">&nbsp; </label>
              <button class="btn btn-success btn-block" onclick="app.nuevaRegion()">NUEVA REGION</button>
           </div>
       </div>
    </div>
    <div class="row">
        <div class="col-xs-12">     
            <div style="overflow-y:scroll;max-height:700px;"> <!--    width: auto; min-width: 100%;-->
              <table class="table responsive tabla-campos" cellspacing="0" style="font-size:.9em">
                  <thead>
                        <tr>
                          <th style="width:100px">OPC.</th>
                          <th>DESCRIPCIÓN</th>
                        </tr>
                  </thead>
                  <tbody id="tblbody">
                      <tr class="tr-null">
                        <td colspan="2" class="text-center"><i>No hay registros disponibles.</i></td>
                      </tr>
                  </tbody>
              </table>
            </div>

            <script id="tpl8Regiones" type="handlebars-x">
              {{#.}}
                <tr>
                  <td>
                    <button class="btn btn-warning"  title="Editar" onclick="app.leerEditarRegion({{cod_region}})">
                      <i class="glyphicon glyphicon-edit"></i>
                    </button>
                    <button class="btn btn-danger" title="Dar Baja"  onclick="app.darBajaRegion({{cod_region}})">
                      <i class="glyphicon glyphicon-ban-circle"></i>
                    </button>
                  </td>
                  <td class="text-left">{{descripcion}}</td>
                </tr>
              {{/.}}
            </script> 
        </div>
    </div>
    <hr>
  </main><!-- /.container -->
  <?php include 'pie.vista.php'; ?>
  

  <div id="mdlRegion" class="modal fade" tabindex="-1" style="display: none;">
      <div class="modal-dialog">
          <form>
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="smaller lighter blue no-margin">Nueva Región</h3>
                      </div>

                      <div class="modal-body">
                        <input type="hidden" id="txtregionaccion" value="">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-8">
                              <div class="control-group">
                                <label class="control-label">Descripción: </label>
                                <input type="text" name="txtregiondescripcion" id="txtregiondescripcion" class="form-control" required>
                              </div>
                            </div>                      
                        </div>
                      </div>

                      <div class="modal-footer">
                        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
                          <i class="ace-icon fa fa-times"></i>
                          Cancelar
                        </button>
                        <button type="submit"  class="btn btn-sm btn-success pull-right">
                          <i class="ace-icon fa fa-save"></i>
                          Guardar
                        </button>
                      </div>
                    </div><!-- /.modal-content -->
          </form>
      </div>
  </div> 

<?php 
  include '_js/jquery.js.php'; 
  include '_js/bootstrap.js.php'; 
?>
  <script src="../plugin/bootstrap-selectpicker/js/bootstrap-select.min.js" type="text/javascript"></script>
  <script src="../plugin/handlebars/handlebars.min.js" type="text/javascript"></script>
  <script src="../util/Ajxur.js" type="text/javascript"></script>
  <script src="js/Util.js" type="text/javascript"></script>
  <script src="js/regiones.vista.js" type="text/javascript"></script>

  <!-- <script src="js/reporteador.vista.js" type="text/javascript"></script> -->
</body>

</html>