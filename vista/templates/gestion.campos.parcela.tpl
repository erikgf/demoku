 <div class="text-right">
    <button class="btn btn-success" onclick="app.nuevaParcela()">NUEVA PARCELA</button>
</div> 
<div style="overflow-y:scroll;max-height:350px;"> <!--    width: auto; min-width: 100%;-->
  <table class="table responsive tabla-campos" cellspacing="0" style="font-size:.9em">
      <thead>
            <tr>
              <th>OPC.</th>
              <th>CONSUMIDOR</th>
              <th>ID SIEMBRA</th>
              <th>ID CAMPAÑA</th>
              <th>PARCELA</th>
              <th>ÁREA</th>
              <th>CULTIVO</th>
              <th>VARIEDAD</th>
              <th>INICIO CAMPAÑA</th>
              <th>FINAL CAMPAÑA</th>
              <th>ESTADO</th>
            </tr>
      </thead>
      <tbody id="tblparcelatbody">
          <tr class="tr-null">
            <td colspan="11" class="text-center"><i>No hay registros disponibles.</i></td>
          </tr>
      </tbody>
  </table>
</div>

<script id="tpl8Parcelas" type="handlebars-x">
  {{#.}}
    <tr>
      <td>
        <button class="btn btn-warning"  title="Editar" onclick="app.leerEditarParcela({{cod_parcela}})">
          <i class="glyphicon glyphicon-edit"></i>
        </button>
        <button class="btn btn-danger" title="Dar Baja"  onclick="app.darBajaParcela({{cod_parcela}})">
          <i class="glyphicon glyphicon-ban-circle"></i>
        </button>
        <button class="btn btn-black" title="Finalizar"  onclick="app.finalizarParcela({{cod_parcela}})">
          <i class="glyphicon glyphicon-lock"></i>
        </button>
      </td>
      <td>{{idconsumidor}}</td>
      <td>{{idsiembra}}</td>
      <td>{{idcampaña}}</td>
      <td>{{rotulo_parcela}}</td>
      <td>{{area}} ha</td>
      <td>{{cultivo}}</td>
      <td>{{variedad}}</td>
      <td>{{inicio_campaña}}</td>
      <td>{{fin_campaña}}</td>
      <td><span class="badge badge-{{#if_ estado '==' 'ACTIVO'}}success{{else}}danger{{/if_}}">{{estado}}</span></td>
    </tr>
  {{else}}
    <tr class="tr-null">
       <td colspan="11" class="text-center"><i>No hay registros disponibles.</i></td>
    </tr>
  {{/.}}
</script> 