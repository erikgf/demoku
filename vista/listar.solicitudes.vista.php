<?php

include '../datos/local_config_web.php';
include 'session.vista.php';
$TITULO_PAGINA = "Listado de Solicitudes";
$CLASE  = "SOLICITUD";
?>

<!DOCTYPE html>
<html leng="es">
    <head>
          <meta charset="utf-8" />
          <title><?php echo $TITULO_PAGINA ?></title>
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <meta name="viewport" content="width=device-width, initial-scale=1.0" />

          <?php  include '_css/main.css.php'; ?>
    </head>
    <body class="no-skin">
        <?php include 'navbar.php'; ?>

        <div class="main-container ace-save-state" id="main-container">
             <script type="text/javascript">
                try{ace.settings.loadState('main-container')}catch(e){}
             </script>

             <?php include 'menu.php'; ?>

            <div class="main-content">
              <div class="main-content-inner">

                <?php include 'breadcrumb.mantenimiento.php' ?>

                <div class="page-content">
               
                  <?php include 'ace.settings.php' ?>

                  <div class="page-header">
                    <h1>
                      <?php echo $TITULO_PAGINA; ?>
                    </h1>
                  </div><!-- /.page-header -->

                  <div class="row">
                    <div class="col-xs-12">
                       <div  id="listado" class="table-responsive">    
                            <script id="tpl8Listado" type="handlebars-x">
                              <table class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                      <th width="55px">#</th>
                                      <th >Personal</th>
                                      <th width="100px">Núm. Eventos</th>
                                      <th width="150px">App Origen</th>
                                      <th width="100px">Veces Devuelto</th>
                                      <th width="130px">Estado</th>
                                      <th width="120px">Fecha Registro</th>
                                      <th width="120px">Acción</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                      {{#.}}
                                        <tr>
                                            <td>{{numero_orden}}</td>
                                            <td>{{personal}}</td>
                                            <td>{{numero_eventos}}</td>
                                            <td>{{origen}}</td>
                                            <td>{{veces_devuelto}}</td>
                                            <td> <span class="badge badge-{{estado_color}} label-lg">{{estado}}</span></td>
                                            <td>{{fecha}}<br>{{hora}}</td>
                                            <td>
                                              <button class="btn btn-xs btn-warning" onclick ="app.editar({{cod_solicitud}})">
                                                <i class="fa fa-edit bigger-130"></i>
                                              </button>
                                              <button class="btn btn-xs btn-danger" onclick ="app.eliminar({{cod_solicitud}})">
                                                <i class="fa fa-trash bigger-130"></i>
                                              </button>
                                            </td>
                                         </tr>
                                      {{/.}}
                                  </tbody>
                              </table>
                           </script>                                    
                        </div>  <!-- table-responsive --> 
                      <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                  </div><!-- /.row -->
                </div><!-- /.page-content -->
              </div>
            </div><!-- /.main-content -->


            <?php include 'footer.php'; ?>
           
        </div><!-- /.main-container -->

        <?php  include '_js/main.js.php';?>
        <script src="js/listar.solicitudes.vista.js"></script>
    </body>

</html>



