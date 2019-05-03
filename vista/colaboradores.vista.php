<?php

include '../datos/local_config_web.php';
include 'session.vista.php';
$TITULO_PAGINA = "Gestión Colaboradores";
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
    <h3>Gestión de Colaboradores</h3>

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
              <button class="btn btn-success btn-block" onclick="app.nuevoColaborador()">NUEVO COLABORADOR</button>
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
                          <th>DNI</th>
                          <th>NOMBRES Y APELLIDOS</th>
                          <th>CELULAR</th>
                          <th>CORREO</th>
                          <th>USUARIO</th>
                          <th>PERFIL</th>
                          <th>ESTADO</th>
                        </tr>
                  </thead>
                  <tbody id="tblbody">
                      <tr class="tr-null">
                        <td colspan="8" class="text-center"><i>No hay registros disponibles.</i></td>
                      </tr>
                  </tbody>
              </table>
            </div>

            <script id="tpl8Colaboradores" type="handlebars-x">
              {{#.}}
                <tr>
                  <td>
                    <button class="btn btn-primary" title="Cambiar Clave"  onclick="app.cambiarClave({{cod_colaborador}})">
                      <i class="glyphicon glyphicon-asterisk"></i>
                    </button>
                    <button class="btn btn-warning"  title="Editar" onclick="app.leerEditarColaborador({{cod_colaborador}})">
                      <i class="glyphicon glyphicon-edit"></i>
                    </button>
                    <button class="btn btn-danger" title="Dar Baja"  onclick="app.darBajaColaborador({{cod_colaborador}})">
                      <i class="glyphicon glyphicon-ban-circle"></i>
                    </button>
                  </td>
                  <td class="text-left">{{dni}}</td>
                  <td class="text-left">{{apellidos}}, {{nombres}}</td>
                  <td class="text-left">{{celular}}</td>
                  <td class="text-left">{{correo}}</td>
                  <td class="text-left">{{usuario}}</td>
                  <td class="text-left">{{perfil}}</td>
                  <td class="text-left"><span class="badge badge-{{#if_ estado_baja '==' 'ACTIVO'}}success{{else}}danger{{/if_}}">{{estado_baja}}</span></td>
                </tr>
              {{else}}
                <tr class="tr-null">
                    <td colspan="8" class="text-center"><i>No hay registros disponibles.</i></td>
                </tr>  
              {{/.}}
            </script> 
        </div>
    </div>
    <hr>
  </main><!-- /.container -->
  <?php include 'pie.vista.php'; ?>
  

  <script id="tpl8Combo" type="handlebars-x">
      <option value="">Seleccionar opción</option>
       {{#.}}
          <option value="{{codigo}}">{{descripcion}}</option>
       {{/.}}
  </script>
  
  <div id="mdlColaborador" class="modal fade" tabindex="-1" style="display: none;">
      <div class="modal-dialog">
          <form>
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="smaller lighter blue no-margin">Nuevo Colaborador</h3>
                      </div>

                      <div class="modal-body">
                        <input type="hidden" id="txtcolaboradoraccion" value="">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">DNI: </label>
                                <input type="text" name="txtdni" id="txtdni" class="form-control">
                              </div>
                            </div>               
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                              <div class="control-group">
                                <label class="control-label">Nombres: </label>
                                <input type="text" name="txtnombres" id="txtnombres" class="form-control" required>
                              </div>
                            </div> 
                            <div class="col-xs-12 col-sm-6">
                              <div class="control-group">
                                <label class="control-label">Apellidos: </label>
                                <input type="text" name="txtapellidos" id="txtapellidos" class="form-control" required>
                              </div>
                            </div>                   
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">Celular: </label>
                                <input type="text" name="txtcelular" id="txtcelular" maxlength="9" class="form-control" >
                              </div>
                            </div> 
                            <div class="col-xs-12 col-sm-6 col-md-8">
                              <div class="control-group">
                                <label class="control-label">Correo: </label>
                                <input type="email" name="txtcorreo" id="txtcorreo" maxlength="100" class="form-control" >
                              </div>
                            </div>                   
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">Usuario (*)(**): </label>
                                <input type="text" name="txtusuario" id="txtusuario" maxlength="32" class="form-control" >
                              </div>
                            </div> 
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">Perfil: </label>
                                <select name="cboperfil" id="cboperfil" class="form-control" required>
                                </select>
                              </div>
                            </div>  
                            <div class="col-xs-12 col-sm-6 col-md-4">
                              <div class="control-group">
                                <label class="control-label">Estado: </label>
                                <select name="cboestado" id="cboestado" class="form-control" required>
                                  <option value="A">ACTIVO</option>
                                  <option value="I">INACTIVO</option>
                                </select>
                              </div>
                            </div>                
                        </div>
                        <br>
                        <div class="row">
                          <div class="col-xs-12">
                            <div><small><b>(*)</b>Los nuevos usuarios toman por defecto la clave general. </small></div>
                            <div><small><b>(**)</b> El nombre de usuario NO toma en cuenta mayúsculas o minúsculas. </small></div>
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

  <div id="mdlClaveColaborador" class="modal fade" tabindex="-1" style="display: none;">
      <div class="modal-dialog">
          <form>
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 class="smaller lighter blue no-margin">Cambiar Clave</h3>
              </div>

              <div class="modal-body">
                <div class="row">
                  <div class="col-xs-12">
                    <div class="control-group">
                      <label class="control-label">Colaborador: </label>
                      <input type="text" readonly name="txtclavecolaborador" id="txtclavecolaborador" class="form-control" >
                      <input type="hidden"  name="txtclavecodcolaborador" id="txtclavecodcolaborador">
                    </div>
                  </div>               
                </div>

                <div class="row">
                  <div class="col-xs-12 col-sm-6">
                    <div class="control-group">
                      <label class="control-label">Nueva Clave: </label>
                      <input type="password" name="txtnuevaclave" id="txtnuevaclave" class="form-control" required>
                      <small><a href="javascript:;" class="verclave" data-id="txtnuevaclave">Ver</a></small>
                    </div>
                  </div> 
                  <div class="col-xs-12 col-sm-6">
                    <div class="control-group">
                      <label class="control-label">Confirmar Clave: </label>
                      <input type="password" name="txtconfirmarclave" id="txtconfirmarclave" class="form-control" required>
                      <small><a href="javascript:;" class="verclave" data-id="txtconfirmarclave">Ver</a></small>
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
  <script src="js/colaboradores.vista.js" type="text/javascript"></script>

  <!-- <script src="js/reporteador.vista.js" type="text/javascript"></script> -->
</body>

</html>