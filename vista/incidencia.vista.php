<?php

include '../datos/local_config_web.php';
include 'session.vista.php';
$TITULO_PAGINA = "Incidencias";

?>

<!DOCTYPE html>
<html leng="es">
    <head>
          <meta charset="utf-8" />
          <title><?php echo $TITULO_PAGINA ?></title>
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <meta name="viewport" content="width=device-width, initial-scale=1.0" />

          <?php  include '_css/main.css.php'; ?>
          <link rel="stylesheet" href="../assets/css/chosen.min.css" />
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

                <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                  <ul class="breadcrumb">
                    <li>
                      <i class="ace-icon fa fa-book"></i> <?php  echo $TITULO_PAGINA; ?>
                    </li>
                  </ul><!-- /.breadcrumb -->
                </div>

                <div class="page-content">
               
                  <?php include 'ace.settings.php' ?>

                  <div class="page-header">
                    <h1>
                      <?php echo $TITULO_PAGINA; ?>
                    </h1>
                  </div><!-- /.page-header -->

                  <div class="row">
                    <div class="col-md-3 col-xs-6">
                      <div class="control-group">
                          <label class="control-label">Filtrar por estado: </label>
                          <select class="chosen-select form-control" id="cboestado" data-placeholder="Seleccionar Estado">
                            <option value="P" selected>PENDIENTE</option>
                            <option value="R">REVISADO</option>
                            <option value="E">ESPERA</option>
                            <option value="C">ATENDIDO PARCIAL</option>
                            <option value="A">ATENDIDO</option>
                          </select>
                      </div>
                    </div>
                  </div>
                  <style type="text/css">
                    table{
                      table-layout: fixed;
                      word-wrap:break-word; 
                    }
                  </style>
                  <div class="space-6"></div>

                  <div class="row">
                    <div class="col-xs-12">
                       <div id="listado">                            
                          <script id="tpl8Listado" type="handlebars-x">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                      <tr>
                                        <th width="35px">#</th>
                                        <th width="100px"><small>Estado</small></th>
                                        <th width="250px">Personal</th>
                                        <th width="150px">Cargo</th>
                                        <th width="150px">Tipo Equipo</th>
                                        <th width="150px">Tipo Problema</th>
                                        <th width="100px">Fecha Registro</th>
                                        <th width="100px">Fecha Revisado</th>
                                        <th width="100px">Fecha Espera</th>
                                        <th width="200px">Observaciones Espera</th>
                                        <th width="100px">Fecha Atendido Parcial</th>
                                        <th width="200px">Observaciones Atendido Parcial</th>
                                        <th width="100px">Fecha Atendido</th>
                                        <th width="200px">Observaciones Atendido</th>
                                      </tr>
                                    </thead>
                                    <tbody >
                                        {{#.}}
                                          <tr data-codigo="{{codigo}}">
                                              <td class="td-ordenador">{{numero}}</td>
                                              <td class="text-center">
                                                <span class="badge badge-{{estado_color}} label-lg">{{estado_rotulo}}</span>
                                                {{#if_ estado '==' 'P'}}
                                                {{else}}
                                                  {{#if_ estado '!=' 'A'}}
                                                    <select onchange="app.modalCambiarEstado($(this), '{{codigo}}');" class="input-sm" style="margin-top: 5px;">
                                                    <option value="" selected>Cambiar...</option>
                                                    {{#if_ estado '==' 'P'}}
                                                      <option value="R">REVISADO</option>
                                                    {{/if_}}
                                                    {{#if_ estado '==' 'R'}}
                                                      <option value="E">ESPERA</option>
                                                      <option value="C">ATENDIDO PARCIAL</option>
                                                    {{/if_}}
                                                    {{#if_ estado '==' 'C'}}
                                                      <option value="E">ESPERA</option>
                                                      <option value="A">ATENDIDO</option>
                                                    {{/if_}}
                                                    {{#if_ estado '==' 'E'}}
                                                      <option value="C">ATENDIDO PARCIAL</option>
                                                    {{/if_}}
                                                    </select>
                                                  {{/if_}}
                                                {{/if_}}                                                
                                              </td>
                                              <td>{{personal}}</td>
                                              <td>{{cargo}}</td>
                                              <td>{{tipo_equipo}}</td>
                                              <td>{{tipo_problema}}</td>
                                              <td>{{fecha_hora_registro}}</td>
                                              <td>{{fecha_hora_revisado}}</td>
                                              <td>{{fecha_hora_espera}}</td>
                                              <td>{{observacion_espera}}</td>
                                              <td>{{fecha_hora_atendido_parcial}}</td>
                                              <td>{{observacion_atendido_parcial}}</td>
                                              <td>{{fecha_hora_atendido}}</td>
                                              <td>{{observacion_atendido}}</td>
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

            <div id="mdlObservaciones" class="modal fade" tabindex="-1" style="display: none;">
                <div class="modal-dialog">
                  <form id="frmgrabar">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h3 class="smaller lighter blue no-margin">Cambiar Estado a: <span class="bolder" id="lblmodalheader"></span></h3>
                          </div>
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-xs-12">
                                <div class="control-group">
                                  <label class="control-label">Observaciones: </label>
                                  <textarea name="txtobservaciones" id="txtobservaciones" class="form-control" placeholder="Observaciones..."></textarea>
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
                              Cambiar
                            </button>
                          </div>
                        </div><!-- /.modal-content -->
                  </form>
                </div>
            </div>
            
            <?php include 'footer.php'; ?>
           
        </div><!-- /.main-container -->

        <?php  include '_js/main.js.php';?>
        <script src="../assets/js/chosen.jquery.min.js"></script>
        <script src="js/incidencia.vista.js"></script>
    </body>

</html>



