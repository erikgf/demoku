<div class="text-right">
    <button class="btn btn-success" onclick="app.nuevaCampaña();">NUEVA CAMPAÑA</button>
</div> 
<div style="overflow-y:scroll;max-height:350px;"> <!--    width: auto; min-width: 100%;-->
  <table class="table responsive tabla-campos" cellspacing="0" style="font-size:.9em">
            <thead>
              <tr>
                <th>OPC.</th>
                <th>CONSUMIDOR</th>
                <th>ID SIEMBRA</th>
                <th>ID CAMPAÑA</th>
                <th>AÑO</th>
                <th>DESCRIPCIÓN</th>
                <th>ÁREA</th>
                <th>INICIO CAMPAÑA</th>
                <th>FINAL CAMPAÑA</th>
                <th>ESTADO</th>
              </tr>
            </thead>
            <tbody id="tblcampanatbody">
                <tr class="tr-null">
                  <td colspan="10" class="text-center"><i>No hay registros disponibles.</i></td>
                </tr>
            </tbody>
  </table>
</div>

<script id="tpl8Campanas" type="handlebars-x">
  {{#.}}
    <tr data-id="{{cod_campaña}}" title="Doble click para ver PARCELAS.">
      <td>
        <button class="btn btn-warning"  title="Editar" onclick="app.leerEditarCampaña({{cod_campaña}})">
          <i class="glyphicon glyphicon-edit"></i>
        </button>
        <button class="btn btn-danger" title="Dar Baja"  onclick="app.darBajaCampaña({{cod_campaña}})">
          <i class="glyphicon glyphicon-ban-circle"></i>
        </button>
        {{#if_ estado '==' 'ACTIVO'}}
          <button class="btn btn-black" title="Finalizar"  onclick="app.finalizarCampaña({{cod_campaña}})">
            <i class="glyphicon glyphicon-lock"></i>
          </button>
        {{/if_}}
      </td>
      <td>{{idconsumidor}}</td>
      <td>{{idsiembra}}</td>
      <td>{{idcampaña}}</td>
      <td>{{año}}</td>
      <td>{{descripcion}}</td>
      <td>{{area}} ha</td>
      <td>{{inicio_campaña}}</td>
      <td>{{fin_campaña}}</td>
      <td><span class="badge badge-{{#if_ estado '==' 'ACTIVO'}}success{{else}}danger{{/if_}}">{{estado}}</span></td>
    </tr>
  {{else}}
    <tr class="tr-null">
       <td colspan="10" class="text-center"><i>No hay registros disponibles.</i></td>
    </tr>
  {{/.}}
</script> 