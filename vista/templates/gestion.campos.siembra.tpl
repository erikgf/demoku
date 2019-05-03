<div class="text-right">
    <button class="btn btn-success" onclick="app.nuevaSiembra();">NUEVA SIEMBRA</button>
</div> 
<div style="overflow-y:scroll;max-height:350px;"> <!--    width: auto; min-width: 100%;-->
  <table class="table responsive tabla-campos" cellspacing="0" style="font-size:.9em">
            <thead>
              <tr>
                <th>OPC.</th>
                <th>CONSUMIDOR</th>
                <th>ID SIEMBRA</th>
                <th>INICIO SIEMBRA</th>
                <th>FINAL SIEMBRA</th>
                <th>CULTIVO</th>
                <th>VARIEDAD</th>
                <th>ÁREA</th>
                <th>TIPO RIEGO</th>
                <th>ESTADO</th>
              </tr>
            </thead>
            <tbody id="tblsiembratbody">
                <tr class="tr-null">
                  <td colspan="10" class="text-center"><i>No hay registros disponibles.</i></td>
                </tr>
            </tbody>
  </table>
</div>
  <!--
         <div class="bloque-detalle">
            <div class="item">
                <label>Item: </label>
                <p>Descripción del item</p>
            </div>
            <div class="item">
                <label>Item: </label>
                <p>Descripción del item</p>
            </div>
            <div class="item">
                <label>Item: </label>
                <p>Descripción del item</p>
            </div>
        </div>
  -->

<script id="tpl8Siembras" type="handlebars-x">
  {{#.}}
    <tr>
      <td>
        <button class="btn btn-warning"  title="Editar" onclick="app.leerEditarSiembra({{cod_siembra}})">
          <i class="glyphicon glyphicon-edit"></i>
        </button>
        <button class="btn btn-danger" title="Dar Baja"  onclick="app.darBajaSiembra({{cod_siembra}})">
          <i class="glyphicon glyphicon-ban-circle"></i>
        </button>
        {{#if_ estado '==' 'ACTIVO'}}
          <button class="btn btn-black" title="Finalizar"  onclick="app.finalizarSiembra({{cod_siembra}})">
            <i class="glyphicon glyphicon-lock"></i>
          </button>
        {{/if_}}
      </td>
      <td>{{idconsumidor}}</td>
      <td>{{idsiembra}}</td>
      <td>{{inicio_siembra}}</td>
      <td>{{fin_siembra}}</td>
      <td>{{cultivo}}</td>
      <td>{{variedad}}</td>
      <td>{{area}} ha</td>
      <td>{{tipo_riego}}</td>
      <td><span class="badge badge-{{#if_ estado '==' 'ACTIVO'}}success{{else}}danger{{/if_}}">{{estado}}</span></td>
    </tr>
  {{else}}
    <tr class="tr-null">
       <td colspan="10" class="text-center"><i>No hay registros disponibles.</i></td>
    </tr>
  {{/.}}
</script> 