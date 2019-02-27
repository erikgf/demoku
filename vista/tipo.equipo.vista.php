<?php

include '../datos/local_config_web.php';
include 'session.vista.php';
$TITULO_PAGINA = "Gestión de Tipo de Equipos";
$CLASE  = "TIPO DE EQUIPO";
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
                      <!--
                      <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Gestión y mantenimiento
                      </small>
                      -->
                    </h1>
                  </div><!-- /.page-header -->

                  <div class="row">
                    <div class="col-xs-8">
                      <!-- PAGE CONTENT BEGINS -->
                      <!-- USARE ESTE ALERT PARA DESPUES
                      <div class="alert alert-block alert-success">
                        <button type="button" class="close" data-dismiss="alert">
                          <i class="ace-icon fa fa-times"></i>
                        </button>

                        <i class="ace-icon fa fa-check green"></i>

                        Welcome to
                        <strong class="green">
                          Ace
                          <small>(v1.4)</small>
                        </strong>, лёгкий, многофункциональный и простой в использовании шаблон для админки на bootstrap 3.3.6. Загрузить исходники с <a href="https://github.com/bopoda/ace">github</a> (with minified ace js/css files).
                      </div>
                     -->

                      <div class="row">
                        <div class="col-xs-offset-9 col-xs-3">
                          <a type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#mdlRegistro" onclick="app.agregar()">
                          <i class="fa fa-plus bigger-120 white"></i> NUEVO REGISTRO</a>
                        </div>
                      </div><!-- /.row -->

                      <div class="space-6"></div>

                      <div class="row">
                        <div class="col-xs-12">
                          <div  id="listado" class="table-responsive">           
                            <script id="tpl8Listado" type="handlebars-x">
                              <table class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                      <th>Descripción</th>
                                      <th width="250px">Peso</th>
                                      <th width="125px">Acción</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                      {{#this}}
                                        <tr>
                                            <td>{{descripcion}}</td>
                                            <td>{{peso}}</td>
                                            <td>
                                                <button class="btn btn-xs btn-warning" onclick ="app.editar({{cod_tipo_equipo}})" data-toggle="modal" data-target="#mdlRegistro">
                                                <i class="fa fa-edit bigger-130"></i>
                                                </button>
                                                <button class="btn btn-xs btn-danger" onclick ="app.eliminar({{cod_tipo_equipo}})">
                                                <i class="fa fa-trash bigger-130"></i>
                                                </button>
                                            </td>
                                         </tr>
                                      {{/this}}
                                  </tbody>
                              </table>
                           </script>                                    
                          </div>  <!-- table-responsive --> 
                        </div> 
                      </div>

                      <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                  </div><!-- /.row -->
                </div><!-- /.page-content -->
              </div>
            </div><!-- /.main-content -->

           <?php include 'footer.php'; ?>
           
        </div><!-- /.main-container -->


        <div id="mdlRegistro" class="modal fade" tabindex="-1" style="display: none;">
            <div class="modal-dialog">
              <form id="frmgrabar">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="smaller lighter blue no-margin">Mantenimiento</h3>
                      </div>

                      <div class="modal-body">
                        <div class="row">
                              <div class="col-xs-7">
                                <div class="control-group">
                                  <label class="control-label">Descripción: </label>
                                  <input type="text" name="txtdescripcion" id="txtdescripcion" class="form-control" placeholder="Descripción..." required="">
                                </div>
                              </div>
                              <div class="col-xs-5">
                                <div class="control-group">
                                  <label class="control-label">Peso: </label>
                                  <select name="cbopeso" id="cbopeso" class="form-control" required="">    
                                  <script id="tpl8Combo" type="handlebars-x">
                                      <option value="">Seleccionar {{rotulo}}</option>
                                      {{#opciones}}
                                      <option value='{{codigo}}'>{{descripcion}}</option>
                                      {{/opciones}}
                                    </script>                                
                                  </select>
                                </div>
                              </div>
                        </div>
                      </div>

                      <div class="modal-footer">
                        <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                          <i class="ace-icon fa fa-times"></i>
                          Cancelar
                        </button>
                        <button type="submit"  class="btn btn-sm btn-primary pull-right">
                          <i class="ace-icon fa fa-save"></i>
                          Guardar
                        </button>
                      </div>
                    </div><!-- /.modal-content -->
              </form>
            </div>
        </div>

        <?php  include '_js/main.js.php';?>
        <script src="js/tipo.equipo.vista.js"></script>
    </body>

</html>



