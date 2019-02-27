<?php

include '../datos/local_config_web.php';
include 'session.vista.php';
$TITULO_PAGINA = "Gestión AHP (Analysis Hierarchy Process)";
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


                  <script id="tpl8Criterios" type="handlebars-x">
                    {{#this}}
                    <tr>
                      <td>{{nombre_criterio}}</td>
                      <td>
                        <label>
                            <input data-id="{{cod_ahp_criterio}}" name="switch-field-1" class="ace ace-switch" type="checkbox" {{#if_ estado_activado '==' 'A'}}checked{{/if_}}>
                            <span class="lbl"></span>
                        </label>
                      </td>
                    </tr>
                    {{/this}}
                  </script> 

                   <script id="tpl8Matriz" type="handlebars-x">
                              <table class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                       <th>CRITERIOS</th>
                                      {{#criterios}}
                                        <th class="text-center" style="witdh:150px">{{nombre_criterio}}</th>
                                       {{/criterios}}
                                     </tr>
                                  </thead>
                                  <tbody>
                                      {{#filas_matriz}}
                                        <tr>
                                            <td >{{nombre_criterio}}</td>
                                            <td style="witdh:150px"><input class="form-control text-center ahp-valor" data-d="{{cod_ahp_criterio}}-1" data-default="{{vs_criterio_1}}" value="{{vs_criterio_1}}"{{#if_ cod_ahp_criterio '==' 1}}disabled{{/if_}}></td>
                                            <td style="witdh:150px"><input class="form-control text-center ahp-valor" data-d="{{cod_ahp_criterio}}-2" data-default="{{vs_criterio_2}}" value="{{vs_criterio_2}}" {{#if_ cod_ahp_criterio '==' 2}}disabled{{/if_}}></td>
                                            <td style="witdh:150px"><input class="form-control text-center ahp-valor" data-d="{{cod_ahp_criterio}}-3" data-default="{{vs_criterio_3}}" value="{{vs_criterio_3}}" {{#if_ cod_ahp_criterio '==' 3}}disabled{{/if_}}></td>
                                            <td style="witdh:150px"><input class="form-control text-center ahp-valor" data-d="{{cod_ahp_criterio}}-4" data-default="{{vs_criterio_4}}" value="{{vs_criterio_4}}" {{#if_ cod_ahp_criterio '==' 4}}disabled{{/if_}}></td>
                                        </tr>
                                      {{/filas_matriz}}
                                  </tbody>
                              </table>
                  </script>   

                  <div class="row">
                    <div class="col-sm-4">
                      <div class="widget-box">
                          <div class="widget-header">
                            <h4>Criterios</h4>
                          </div>
                          <div class="widget-body">
                            <div class="widget-main">
                              <div class="well well-sm"> Al desactivar un criterio lo excluye de la aplicación del algoritmo AHP. </div>
                              <table class="table table-striped table-bordered table-hove"  cellspacing="0" width="100%">
                                    <thead>
                                      <tr>
                                        <th>Nombre de Criterio</th>
                                        <th width="100px">Estado</th>
                                      </tr>
                                    </thead>
                                    <tbody id="tbl-criterios"> </tbody>
                              </table>
                              <div id="blkalertcriterio"></div>
                              <div class="space-6"></div>
                            </div>
                          </div>
                      </div>
                    </div>
                    <div class="col-sm-8">
                      <div class="widget-box">
                          <div class="widget-header">
                            <h4>Matriz de Pesos</h4>
                          </div>
                          <div class="widget-body">
                            <div class="widget-main">
                              <div class="well well-sm"> Se permiten valores de 1 a 9. Se completará el valor inverso de la matriz automáticamente. </div>
                              <table class="table table-striped table-bordered table-hove" id="tbl-matriz"  cellspacing="0" width="100%">
                              </table>
                              <div id="blkalert"></div>
                              <div class="space-6"></div>
                              <div class="row"> 
                                <div class="col-sm-2">
                                  <button id="btn-restaurar-matriz" class="btn btn-lighter btn-block">RESTAURAR</button>
                                </div>
                                <div class="col-sm-offset-8 col-sm-2">
                                  <button id="btn-guardar-matriz" class="btn btn-primary btn-block">GUARDAR</button>
                                </div>
                              </div>
                            </div>
                          </div>
                      </div>
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
        <?php  include '_js/main.js.php';?>
        <script src="js/gestion.ahp.vista.js" type="text/javascript"></script>
    </body>

</html>



