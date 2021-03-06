<?php

include '../datos/local_config_web.php';
include 'session.vista.php';
$TITULO_PAGINA = "Registros Realizados";
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
    .rw-subdetalle{
      background-color: #dbfdce !important;
    }
    </style>
  </head>
  <body>

  <?php include 'cabecera.vista.php' ?>

 <main role="main" class="container">
    <h3>Registros en Sistema</h3>
    <div class="row">
      <div class="col-xs-6 col-md-2">
          <div class="control-group">
            <label class="control-label">Fecha Desde: </label>
            <input id="txtfechadesde" type="date" value="<?php echo date('Y-m-d'); ?>" class="form-control" />
          </div>
      </div>
      <div class="col-xs-6 col-md-2">
          <div class="control-group">
            <label class="control-label">Fecha Hasta: </label>
            <input id="txtfechahasta" type="date" value="<?php echo date('Y-m-d'); ?>" class="form-control" />
          </div>
      </div>
      <div class="col-xs-6 col-md-3">
          <div class="control-group">
            <label class="control-label">Campos: </label>
            <select id="cbocampo" class="form-control">
                <option value="">Todos los campos</option>
            </select>
          </div>
      </div>
      <div class="col-xs-6 col-md-3">
          <div class="control-group">
            <label class="control-label">Evaluadores: </label>
            <select id="cboevaluador" class="form-control">
                <option value="">Todos los evaluadores</option>
            </select>
          </div>
      </div>
      <div class="col-xs-6  col-md-2">
         <div class="control-group">
            <label class="control-label">&nbsp;</label>
            <button class="btn btn-block btn-success btn-sm" id="btnbuscar">BUSCAR</button>
         </div>
      </div>
    </div>
    <br>
    <div class="row">
      <div class="col-xs-6 col-md-2">
          <div class="control-group">
            <label class="control-label">Tipo Evaluación: </label>
            <select id="cbotipoevaluacion" class="form-control">
                <option value="*">TODOS</option>
                <option value="1">BIOMETRÍA</option>
                <option value="2">DIATRAEA</option>
                <option value="4">CARBÓN</option>
            </select>
          </div>
      </div>
      <div class="col-xs-6 col-sm-2 col-md-1">
        <div class="control-group">
            <label class="control-label">MÓD./JIR.: </label>
            <input id="txtnumeronivel1"  type='text' class="form-control"/>
        </div>
      </div>
      <div class="col-xs-6 col-sm-2 col-md-1">
        <div class="control-group">
            <label class="control-label">TURNO: </label>
            <input  id="txtnumeronivel2" type='text' class="form-control"/>
        </div>
      </div>
      <div class="col-xs-6 col-sm-2 col-md-1">
        <div class="control-group">
            <label class="control-label">VÁLV./CUART.: </label>
            <input  id="txtnumeronivel3" type='text' class="form-control"/>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
        <div class="row">
          <div class="col-sm-6 col-xs-12 col-md-8">
            <h3 id="txtmensaje"></h3>
          </div>
          <div class="col-sm-6 col-xs-12 col-md-4">
            <label>Buscar: </label>
            <input placeholder="Buscar..." id="txtbuscar" type='text' class="form-control">
          </div>
        </div>
          <div  id="listadoCabecera">
              <table class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                 <thead>
                   <tr>
                     <th width="100px">Acción</th>
                     <th>CAMPO</th>
                     <th width="100px">MÓDULO/ JIRÓN</th>
                     <th width="100px">TURNO</th>
                     <th width="100px">VÁLVULA/ CUARTEL</th>
                     <th width="100px">FECHA EVALUACIÓN</th>
                     <th width="100px">TIPO EVALUACIÓN</th>
                     <th>EVALUADOR</th>
                   </tr>
                 </thead>
                <tbody>
                     <tr class="td-null"><td colspan="8" class="text-center"><i>Sin registros para mostrar</i></td></tr>
                </tbody>
              </table>
          </div>
      </div>
    </div>

    <hr>
  </main><!-- /.container -->

    <div id="mdlEditarCabecera" class="modal fade" tabindex="-1" style="display: none;">
            <div class="modal-dialog modal-lg">
              <form id="frmgrabarcabecera">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="smaller lighter blue no-margin">Editar Envío</h3>
                      </div>

                      <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8 col-xs-12">
                              <div class="control-group">
                                <label class="control-label">CAMPO: </label>
                                <select name="txtmdlcampo" id="txtmdlcampo"class="form-control" required>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-4 col-xs-12">
                              <div class="control-group">
                                <label class="control-label">TIPO EVALUACIÓN </label>
                                <p id="lbltipoevaluacion"></p>
                              </div>
                            </div>                  
                        </div>
                        <div class="row">
                            <div class="col-xs-4 col-sm-3">
                              <div class="control-group">
                                <label class="control-label">MÓDULO/JIRÓN: </label>
                                <select name="txtmdlnumeronivel1" id="txtmdlnumeronivel1"class="form-control" required>
                                </select>
                              </div>
                            </div>
                            <div class="col-xs-4 col-sm-3" id="blknivel2">
                              <div class="control-group">
                                <label class="control-label">TURNO: </label>
                                <select name="txtmdlnumeronivel2" id="txtmdlnumeronivel2"class="form-control" required>
                                </select>
                              </div>
                            </div>  
                            <div class="col-xs-4 col-sm-3">
                              <div class="control-group">
                                <label class="control-label">VÁLVULA/CUARTEL: </label>
                                <select name="txtmdlnumeronivel3" id="txtmdlnumeronivel3"class="form-control" required>
                                </select>
                              </div>
                            </div>                     
                        </div>

                        <div class="row">
                          <div class="col-xs-6 col-sm-3">
                              <div class="control-group">
                                <label class="control-label">FECHA EVALUACIÓN: </label>
                                <input type="date" name="txtmdlfecharegistro" id="txtmdlfecharegistro" class="form-control" required>
                              </div>
                          </div>
                          <div class="col-xs-6 col-sm-6">
                            <div class="control-group">
                                <label class="control-label">EVALUADOR: </label>
                                <select name="txtmdlevaluador" id="txtmdlevaluador"class="form-control" required>
                                </select>
                              </div>
                          </div>
                        </div>

                        <div class="row">
                         
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

    <div id="mdlEditarDetalleDiatraea" class="modal fade" tabindex="-1" style="display: none;">
            <div class="modal-dialog modal-lg">
              <form id="frmgrabardiatraea">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="smaller lighter blue no-margin">Editar Diatraea</h3>
                      </div>

                      <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-3">
                              <div class="control-group">
                                <label class="control-label">ENTRENUDOS: </label>
                                <input type="number" name="txtdia_entrenudos" id="txtdia_entrenudos" class="form-control" required>
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-3">
                              <div class="control-group">
                                <label class="control-label">ENTRENUDOS INFESTADOS: </label>
                                <input type="number" name="txtdia_entrenudosinfestados" id="txtdia_entrenudosinfestados" class="form-control" required>
                              </div>
                            </div>                      
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-3">
                              <div class="control-group">
                                <label class="control-label">TALLOS: </label>
                                <input type="number" name="txtdia_tallos" id="txtdia_tallos" class="form-control" required>
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-3">
                              <div class="control-group">
                                <label class="control-label">TALLOS INFESTADOS: </label>
                                <input type="number" name="txtdia_tallosinfestados" id="txtdia_tallosinfestados" class="form-control" required>
                              </div>
                            </div>                      
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xs-6 col-sm-4">
                              <div class="control-group">
                                <label class="control-label">LARVAS EST. 1: </label>
                                <input type="number" name="txtdia_larvas1" id="txtdia_larvas1" class="form-control" required>
                              </div>
                            </div>
                            <div class="col-xs-6 col-sm-4">
                              <div class="control-group">
                                <label class="control-label">LARVAS EST. 2: </label>
                                <input type="number" name="txtdia_larvas2" id="txtdia_larvas2" class="form-control" required>
                              </div>
                            </div>
                            <div class="col-xs-6 col-sm-4">
                              <div class="control-group">
                                <label class="control-label">LARVAS EST. 3: </label>
                                <input type="number" name="txtdia_larvas3" id="txtdia_larvas3" class="form-control" required>
                              </div>
                            </div>                     
                        </div>

                        <div class="row">
                            <div class="col-xs-6 col-sm-4">
                              <div class="control-group">
                                <label class="control-label">LARVAS EST. 4: </label>
                                <input type="number" name="txtdia_larvas4" id="txtdia_larvas4" class="form-control" required>
                              </div>
                            </div>
                            <div class="col-xs-6 col-sm-4">
                              <div class="control-group">
                                <label class="control-label">LARVAS EST. 5: </label>
                                <input type="number" name="txtdia_larvas5" id="txtdia_larvas5" class="form-control" required>
                              </div>
                            </div>
                            <div class="col-xs-6 col-sm-4">
                              <div class="control-group">
                                <label class="control-label">LARVAS EST. 6: </label>
                                <input type="number" name="txtdia_larvas6" id="txtdia_larvas6" class="form-control" required>
                              </div>
                            </div>                     
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-3">
                              <div class="control-group">
                                <label class="control-label">CRISÁLIDAS: </label>
                                <input type="number" name="txtdia_crisalidas" id="txtdia_crisalidas" class="form-control" required>
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col- md-3">
                              <div class="control-group">
                                <label class="control-label">ENTRENUDOS INFESTADOS: </label>
                                <input type="number" name="txtdia_parasitadas" id="txtdia_parasitadas" class="form-control" required>
                              </div>
                            </div>                      
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-3">
                              <div class="control-group">
                                <label class="control-label">BILLAEA LARVAS: </label>
                                <input type="number" name="txtdia_billaealarvas" id="txtdia_billaealarvas" class="form-control" required>
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-3">
                              <div class="control-group">
                                <label class="control-label">BILLAEA PUPAS: </label>
                                <input type="number" name="txtdia_billaeapupas" id="txtdia_billaeapupas" class="form-control" required>
                              </div>
                            </div>                      
                        </div>
                        <br>
                          <div class="row">
                            <div class="col-xs-12 col-md-4">
                              <div class="control-group">
                                <button class="btn btn-lg btn-danger" onclick="app.eliminarRegistroDetalle()">
                                  <i class="glyphicon glyphicon-trash"></i>
                                  ELIMINAR REGISTRO
                                </button>
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

    <div id="mdlEditarDetalleCarbon" class="modal fade" tabindex="-1" style="display: none;">
            <div class="modal-dialog modal-lg">
              <form id="frmgrabarcarbon">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="smaller lighter blue no-margin">Editar Carbón</h3>
                      </div>

                      <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-3">
                              <div class="control-group">
                                <label class="control-label">N° TALLOS: </label>
                                <input type="number" name="txtcar_tallos" id="txtcar_tallos" class="form-control" required>
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-3">
                              <div class="control-group">
                                <label class="control-label">N° LÁTIGOS: </label>
                                <input type="number" name="txtcar_talloslatigo" id="txtcar_talloslatigo" class="form-control" required>
                              </div>
                            </div>                      
                        </div>
                        <br>
                          <div class="row">
                            <div class="col-xs-12 col-md-4">
                              <div class="control-group">
                                <button class="btn btn-lg btn-danger" onclick="app.eliminarRegistroDetalle();">
                                  <i class="glyphicon glyphicon-trash"></i>
                                  ELIMINAR REGISTRO
                                </button>
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

   <script id="tpl8ListadoCabecera" type="handlebars-x">
   <table class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
     <thead>
       <tr>
         <th width="100px">Acción</th>
         <th>CAMPO</th>
         <th width="100px">MÓDULO/ JIRÓN</th>
         <th width="100px">TURNO</th>
         <th width="100px">VÁLVULA/ CUARTEL</th>
         <th width="100px">FECHA EVALUACIÓN</th>
         <th width="100px">TIPO EVALUACIÓN</th>
         <th>EVALUADOR</th>
       </tr>
     </thead>
     <tbody>
       {{#this}}
         <tr data-id="{{cod_registro}}">
           <td>
             <button class="btn btn-info" onclick ="app.detalle(this, {{cod_registro}},{{cod_formulario_evaluacion}})" title="Ver Detalle">
             <i class="glyphicon glyphicon-eye-open"></i>
             </button>
             <button class="btn btn-warning" onclick ="app.leerEditar({{cod_registro}},'{{rotulo_parcela}}')" title="Editar">
             <i class="glyphicon glyphicon-edit"></i>
             </button>
             <button class="btn btn-danger" onclick ="app.eliminar(this,{{cod_registro}},'{{rotulo_parcela}}')" title="Eliminar">
             <i class="glyphicon glyphicon-trash"></i>
             </button>
           </td>
           <td>{{nombre_campo}}</td>
           <td>{{numero_nivel_1}}</td>
           <td>{{numero_nivel_2}}</td>
           <td>{{numero_nivel_3}}</td>
           <td>{{fecha_evaluacion}}</td>
           <td>{{tipo_evaluacion}}</td>
           <td>{{evaluador}}</td>
         </tr>
       {{else}}
          <tr class="tr-null"><td colspan="8" class="text-center"><i>Sin registros para mostrar</i></td></tr>
       {{/this}}
     </tbody>
   </table>
  </script>   

  <script id="tpl8Combo" type="handlebars-x">
      <option value="*">{{rotulo}}</option>
      {{#opciones}}
        <option value='{{codigo}}'>{{descripcion}}</option>
      {{/opciones}}
  </script> 

   <script id="tpl8ComboNivel" type="handlebars-x">
      <option value="">{{rotulo}}</option>
      {{#opciones}}
        <option value='{{descripcion}}'>{{descripcion}}</option>
      {{/opciones}}
  </script> 



<?php include 'pie.vista.php'; ?>

<?php 
  include '_js/jquery.js.php'; 
  include '_js/bootstrap.js.php'; 
?>
  <script src="../plugin/handlebars/handlebars.min.js" type="text/javascript"></script>
  <script src="../plugin/bootstrap-selectpicker/js/bootstrap-select.min.js" type="text/javascript"></script>
  <script src="../util/Ajxur.js" type="text/javascript"></script>
  <script src="js/Util.js" type="text/javascript"></script>
  <script src="js/registros.vista.js" type="text/javascript"></script>
</body>

</html>