<?php

include '../datos/local_config_web.php';
include 'session.vista.php';
$TITULO_PAGINA = "Registrar Solicitud";
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

                <?php include 'breadcrumb.solicitudes.php'; ?>

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

                  <script id="tpl8Personal" type="handlebars-x">
                        <option value=""> </option>
                        {{#.}}
                        <option value='{{cod_personal}}'>[{{dni}}] - {{nombres_apellidos}}</option>
                        {{/.}}
                  </script>

                  <script id="tpl8TipoEquipo" type="handlebars-x">
                        <option value=""> </option>
                        {{#.}}
                        <option value='{{cod_tipo_equipo}}'>{{descripcion}}</option>
                        {{/.}}
                  </script>

                  <script id="tpl8TipoProblema" type="handlebars-x">
                        <option value=""> </option>
                        {{#.}}
                        <option value='{{cod_tipo_problema}}' data-evento='{{tipo_evento}}'>{{descripcion}}</option>
                        {{/.}}
                  </script>

                  <script id="tpl8Evento" type="handlebars-x">
                        {{#.}}
                          <tr>
                            <td width="40px">{{numero}}</td>
                            {{#tipo_equipo}}<td width="175px" data-codigo="{{codigo}}">{{texto}}</td>{{/tipo_equipo}}
                            {{#tipo_problema}}<td width="215px" data-codigo="{{codigo}}">{{texto}}</td>{{/tipo_problema}}
                            <td width="210px">{{tipo_evento}}</td>
                            <td>{{descripcion}}</td>
                            <td width="75px">
                              <button class="btn btn-xs btn-warning editar-evento">
                              <i class="fa fa-edit bigger-80"></i>
                              </button>
                              <button class="btn btn-xs btn-danger eliminar-evento">
                              <i class="fa fa-close bigger-80"></i>
                              </button>
                            </td>
                          </tr>
                        {{/.}}
                        {{^.}}
                          <tr class="td-null">
                            <td colspan="6" class="text-center"><i>No hay eventos agregados.</i></td>
                           </tr>
                        {{/.}}
                  </script>

                  <div class="row">
                    <div class="col-xs-offset-1 col-xs-10">
                      <div id="blkalertsolicitud"></div>
                      <div class="row">
                        <div class="col-md-2 col-sm-4 col-xs-6">
                          <div class="control-group">
                            <label class="control-label">Código Solicitud: </label>
                            <input type="text" style="font-size:24px;" name="txtcodigo" id="txtcodigo" class="form-control text-center" readonly>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 col-sm-8 col-xs-12">
                          <div class="control-group">
                            <label class="control-label">Personal solicitante: </label>
                            <select class="chosen-select form-control" id="cbopersonal" data-placeholder="Elegir personal">
                                <option value=""></option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-offset-4 col-md-2 col-sm-offset-1 col-sm-3 col-xs-6">
                          <div class="control-group" id="blkestado" style="display:none;">
                            
                          </div>
                        </div>
                      </div>
                      <div class="hr hr-16"></div>

                      <h4 id="lblaccionevento">Nuevo Evento</h4>
                      <div class="row" id="blkregistrarevento">                      
                          <div class="col-xs-9">
                            <div class="row">
                              <div class="col-xs-7">
                                   <div class="control-group">
                                    <label class="control-label">Tipo Equipo: </label>
                                    <select class="chosen-select form-control" name="cbotipoequipo" id="cbotipoequipo" required data-placeholder="Elegir tipo de equipo">
                                    </select>
                                  </div>
                                   <div class="control-group">
                                    <label class="control-label">Tipo Problema: </label>
                                    <select class="chosen-select form-control" name="cbotipoproblema" id="cbotipoproblema" required data-placeholder="Elegir tipo de problema">
                                    </select>
                                  </div>
                              </div>
                              <div class="col-xs-5">
                                  <div class="control-group">
                                    <label class="control-label">Descripción de evento: </label>
                                    <textarea class="form-control" id="txtdescripcionevento"  rows="4" name="txtdescripcionevento" required></textarea>
                                  </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-3">
                            <button type="submit" id="btnagregarevento" class="btn btn-primary btn-block btn-sm">
                              <i class="ace-icon fa fa-plus white"></i> AGREGAR EVENTO
                            </button>
                            <button type="submit" id="btneditaredicion" class="btn btn-warning btn-block btn-sm" style="display:none;">
                              <i class="ace-icon fa fa-floppy-o white"></i> GUARDAR EDICIÓN
                            </button>
                            <button type="submit" id="btncancelaredicion" class="btn btn-danger btn-block btn-sm" style="display:none;">
                              <i class="ace-icon fa fa-close white"></i> CANCELAR EDICIÓN
                            </button>
                          </div>
                      </div>
                      <div id="blkalertevento"></div>

                      <div class="row">
                        <div class="col-xs-12">
                          <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                               <thead>
                                    <tr>
                                      <th width="40px">#</th>
                                      <th width="175px">Tipo Equipo</th>
                                      <th width="215px">Tipo de Problema</th>
                                      <th width="200px">Tipo Evento</th>
                                      <th>Descripción</th>
                                      <th width="75px"><small>Acción</small></th>
                                    </tr>
                                </thead>
                                <tbody id="tbl">
                                  <tr class="td-null">
                                    <td colspan="6" class="text-center"><i>No hay eventos agregados.</i></td>
                                  </tr>
                                </tbody>
                            </table>
                          </div>
                        </div>
                      </div>                    

                      <div class="row">
                        <div class="col-xs-2">
                          <button class="btn btn-info btn-block btn-lg" id="btnrevisarsolicitud"  style="display: none;">
                            <i class="ace-icon fa fa-check white"></i>REVISAR
                          </button>
                        </div>
                         <div class="col-xs-3">
                          <h3 id="lbldevuelto" style="display:none;text-align: center;line-height: 5px;color: #ff3600;font-weight: bold;font-size: 25px;">DEVUELTO</h3>
                          <button class="btn btn-danger btn-block btn-lg" id="btndevolversolicitud"  style="display: none;">
                            <i class="ace-icon fa fa-exchange white"></i>DEVOLVER
                          </button>
                        </div>
                        <div class="col-xs-offset-3 col-xs-4">
                          <button class="btn btn-success btn-block btn-lg" id="btnguardarsolicitud">
                            <i class="ace-icon fa fa-floppy-o white"></i>GUARDAR
                          </button>
                        </div>
                      </div>
                      <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                  </div><!-- /.row -->
                </div><!-- /.page-content -->
              </div>
            </div><!-- /.main-content -->

             <div id="mdlDevolucion" class="modal fade" tabindex="-1" style="display: none;">
                <div class="modal-dialog">
                  <form id="frmgrabar">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h3 class="smaller lighter blue no-margin">Devolución de Solicitud</h3>
                          </div>
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-xs-12">
                                <div class="control-group">
                                  <label class="control-label">Motivos de devolución: </label>
                                  <textarea name="txtmotivodevolucion" required id="txtmotivodevolucion" class="form-control"></textarea>
                                </div>
                              </div>
                            </div>
                            <div id="blkalertmodal"></div>
                          </div>

                          <div class="modal-footer">
                            <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                              <i class="ace-icon fa fa-times"></i>
                              Cancelar
                            </button>
                            <button type="submit"  class="btn btn-sm btn-primary pull-right">
                              <i class="ace-icon fa fa-save"></i>
                              Devolver
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
        <script type="text/javascript">
            var _TEMPID =  '<?php echo isset($_GET["cs"]) ? $_GET["cs"] : "-1" ?>';
        </script>
        <script src="js/registrar.solicitud.vista.js"></script>
    </body>

</html>



