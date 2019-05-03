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

    <link rel="stylesheet" type="text/css" href="../plugin/bootstrap-selectpicker/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="css/estilos.css">
  </head>
  <body>

  <?php include 'cabecera.vista.php' ?>

 <main role="main" class="container">
    <h3>Gestión de Campos</h3>
    <div class="row">
      <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="control-group">
          <label class="control-label">Regiones: </label>
          <select id="cboregion" class="form-control"  data-live-search="true" title="Selecionar región">
          </select>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="control-group">
          <label class="control-label">Campos: </label>
          <select id="cbocampo" class="form-control" data-live-search="true" title="Selecionar campo">
          </select>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-2">
        <br>
        <button class="btn btn-success" onclick="app.nuevoCampo();">NUEVO CAMPO</button>
      </div>
    </div>
    <hr>
    <div id="blkcamposeleccionado">
       <script id="tpl8CabeceraCampo" type="handlebars-x">
         <h4 id="lblcamposeleccionado">{{nombre_campo}}
          <div class="pull-right">
            <button class="btn btn-xs btn-danger" onclick="app.darBajaCampo()">DAR BAJA</button>
            <button class="btn btn-xs btn-warning" onclick="app.leerEditarCampo()">EDITAR</button>
          </div>
        </h4>
        <div class="row">
          <div class="col-md-4 control-group">
            <label class="control-label">CONSUMIDOR</label>
            <p>{{idconsumidor}}</p>
          </div>
          <div class="col-md-4 control-group">
            <label class="control-label">DESCRIPCIÓN</label>
            <p>{{nombre_campo}}</p>
          </div>
          <div class="col-md-4 control-group">
            <label class="control-label">ÁREA</label>
            <p>{{area}} ha</p>
          </div>
        </div>
     </script> 
    </div> 
    <ul class="nav nav-tabs ">
      <li class="active"><a data-toggle="tab" href="#tabsiembras">Siembras</a></li>
      <li><a  data-toggle="tab" href="#tabcampañas">Campañas</a></li>
      <li class="disabled"><a  class="disabled" data-toggle="tab" href="#tabcosechas">Cosechas</a></li>
      <li><a  data-toggle="tab" href="#tabparcelas">Parcelas</a></li>
      <!-- disabled, aplica a li/ y a/ -->
    </ul>

    <div class="tab-content">
      <div id="tabsiembras" class="tab-pane fade in active">
        <?php 
          include 'templates/gestion.campos.siembra.tpl';
         ?> 
      </div>
      <div id="tabcampañas" class="tab-pane fade">
        <?php 
          include 'templates/gestion.campos.campana.tpl';
         ?> 
      </div>
      <div id="tabcosechas" class="tab-pane fade">
         <?php 
          include 'templates/gestion.campos.cosecha.tpl';
         ?> 
      </div>
      <div id="tabparcelas" class="tab-pane fade">
         <?php 
          include 'templates/gestion.campos.parcela.tpl';
         ?> 
      </div>
    </div>
    <hr>
  </main><!-- /.container -->
  <?php include 'pie.vista.php'; ?>
  

  <div id="mdlCampo" class="modal fade" tabindex="-1" style="display: none;">
      <div class="modal-dialog">
          <form>
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="smaller lighter blue no-margin">Nuevo Campo</h3>
                      </div>

                      <div class="modal-body">
                        <input type="hidden" id="txtcampoaccion" value="">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">ID Consumidor: </label>
                                <input type="text" name="txtcampoconsumidor" id="txtcampoconsumidor" class="form-control">
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-8">
                              <div class="control-group">
                                <label class="control-label">Descripción: </label>
                                <input type="text" name="txtcampodescripcion" id="txtcampodescripcion" class="form-control" required>
                              </div>
                            </div>                      
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                              <div class="control-group">
                                <label class="control-label">Región: </label>
                                <select id="cbocamporegion" name="cbocamporegion" class="form-control" required></select>
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                              <div class="control-group">
                                <label class="control-label">Área: </label>
                                <input type="number" name="txtcampoarea" step="0.001" id="txtcampoarea" class="form-control" required>
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

  <div id="mdlSiembra" class="modal fade" tabindex="-1" style="display: none;">
      <div class="modal-dialog">
          <form>
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="smaller lighter blue no-margin">Nueva Siembra</h3>
                      </div>

                      <div class="modal-body">
                        <input type="hidden" id="txtsiembraaccion" value="">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">ID Siembra: </label>
                                <input type="text" name="txtsiembraid" id="txtsiembraid" class="form-control">
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">Área: </label>
                                <input type="number" name="txtsiembraarea" id="txtsiembraarea" class="form-control" required>
                              </div>
                            </div>    
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">Fecha Inicio: </label>
                                <input type="date" name="txtsiembrafechainicio" id="txtsiembrafechainicio" class="form-control" required>
                              </div>
                            </div>   
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">Fecha Final: </label>
                                <input type="date" name="txtsiembrafechafin" id="txtsiembrafechafin" class="form-control" >
                              </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">Tipo Riego: </label>
                                <select id="cbosiembratiporiego" name="cbosiembratiporiego" class="form-control" required>
                                  <option value="">Seleccionar</option>
                                  <option value="0">GOTEO</option>
                                  <option value="1">GRAVEDAD</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">Cultivo: </label>
                                <select id="cbosiembracultivo" name="cbosiembracultivo" class="form-control" required>
                                </select>
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">Variedad: </label>
                                <select id="cbosiembravariedad" name="cbosiembravariedad" class="form-control" required>
                                </select>
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

  <div id="mdlCampaña" class="modal fade" tabindex="-1" style="display: none;">
      <div class="modal-dialog">
          <form>
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="smaller lighter blue no-margin">Nueva Campaña</h3>
                      </div>

                      <div class="modal-body">
                        <input type="hidden" id="txtcampañaaccion" value="">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">ID Campaña: </label>
                                <input type="text" name="txtcampañaconsumidor" id="txtcampañaconsumidor" class="form-control">
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">ID Siembra: </label>
                                <select id="cbocampañasiembra" name="cbocampañasiembra" class="form-control" required></select>
                              </div>
                            </div>
                        </div>

                         <div class="row">                              
                            <div class="col-xs-12 col-sm-6 col-md-3">
                              <div class="control-group">
                                <label class="control-label">Año: </label>
                                <input type="text" name="txtcampañaaño" id="txtcampañaaño" class="form-control" required>
                              </div>
                            </div>   
                            <div class="col-xs-12 col-sm-6 col-md-9">
                              <div class="control-group">
                                <label class="control-label">Descripción: </label>
                                <input type="text" name="txtcampañadescripcion" id="txtcampañadescripcion" class="form-control">
                              </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">Área: </label>
                                <input type="number" name="txtcampañaarea" id="txtcampañaarea" class="form-control" required>
                              </div>
                            </div>     
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">Fecha Inicio: </label>
                                <input type="date" name="txtcampañafechainicio" id="txtcampañafechainicio" class="form-control" required>
                              </div>
                            </div>   
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">Fecha Final: </label>
                                <input type="date" name="txtcampañafechafin" id="txtcampañafechafin" class="form-control" required>
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
  <script id="tpl8Combo" type="handlebars-x">
      <option value="">Seleccionar opción</option>
       {{#.}}
          <option value="{{codigo}}">{{descripcion}}</option>
       {{/.}}
  </script>  

<?php 
  include '_js/jquery.js.php'; 
  include '_js/bootstrap.js.php'; 
?>
  <script src="../plugin/bootstrap-selectpicker/js/bootstrap-select.min.js" type="text/javascript"></script>
  <script src="../plugin/handlebars/handlebars.min.js" type="text/javascript"></script>
  <script src="../util/Ajxur.js" type="text/javascript"></script>
  <script src="js/Util.js" type="text/javascript"></script>
  <script src="js/gestion.campos.vista.js" type="text/javascript"></script>

  <!-- <script src="js/reporteador.vista.js" type="text/javascript"></script> -->
</body>

</html>