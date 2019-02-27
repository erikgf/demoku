var InicioView = function (data_usuario, servicio_web, servicio) {

	var self = this,
		$label_enviar,
		progressBar,
		modalMensaje,
		TOTAL_REGISTOS_ENVIO = 0,
		TOTAL_REGISTROS_PENDIENTES = 0,
		TOTAL_REGISTROS_PENDIENTES_PROPIOS = 0,
		rs2Array = resultSetToArray;

	this.initialize = function () {
        this.$el = $('<div/>');       
        this.setEventos(); 
        if (ACTUAL_PAGE != null && (typeof ACTUAL_PAGE.destroy == "function")){
            ACTUAL_PAGE.destroy();
        }
        ACTUAL_PAGE = this;
    };

    this.setEventos = function(){
     	this.$el.on("click","#btn-actualizar", this.actualizarDatos);
     	this.$el.on("click","#btn-enviar", this.sincronizarDatos);
     	this.$el.on("click","#btn-mapa", this.irMapas);
     	this.$el.on("click","#btn-listas", this.irListaCampo);
     };

    this.render = function() {
	    this.$el.html(this.template(data_usuario));
	    $label_enviar = this.$el.find(".lbl-enviar");
	    this.consultarRegistrosPendientes();
	    return this;
	};

	this.actualizarDatos = function(){
		/*Conectando....*/

		if (TOTAL_REGISTROS_PENDIENTES > 0){
			if (!confirm("Hay "+TOTAL_REGISTROS_PENDIENTES+" registros pendientes NO RESINCRONIZADOS en el móvil, ¿deseas actualizar de todas maneras?")){
				return;
			}
		}
		
		if (progressBar){
			progressBar.destroy();
		}
		progressBar = new ProgressBarComponente().initRender({titulo: "Descargando información", texto_informacion: "Conectando...",valor :"0"});
		progressBar.mostrar();

     	$.when( servicio_web.actualizarDatos()
	     		.done( function(r){	     			
	     			if (r.rpt){	     	
	     				progressBar.setTotalRegistros(r.data.contador_registros);
	     				self.insertarActualizarDatos(r.data);
						//servicio_web.insertarActualizarDatos(r.data);		     				
	     			}
	      		})
	      		.fail(function(error){
	      			alert(error);
	      			console.error(error);
	      		})
      	); //EndWhen
	};
	/*
	Inserción:  usuarios, campos, parcelas, coordenadas, formularios
	*/
	this.insertarActualizarDatos = function(datos){
		var total_registros_afectados  = 0,
			fnError = function(e){
				progressBar.setErrorState(e.message);
		        setTimeout(function(){
		          progressBar.esconder();
		          progressBar.destroy();
		          progressBar = null;
		        }, 2000);
			};


	 	$.when( servicio.insertarUsuarios(datos.usuarios)
	 		.done(function(res){
	 			/*Actualizando... X%*/
	 			total_registros_afectados += res.rowsAffected;
	 			progressBar.actualizarPorcentaje("Actualizando usuarios", total_registros_afectados);	 			
	 			$.when(servicio.insertarCampos(datos.campos)
	 				.done(function(res){
	 					total_registros_afectados += res.rowsAffected;
	 					progressBar.actualizarPorcentaje("Actualizando campos", total_registros_afectados);

	 					$.whenAll( servicio.insertarParcelas(datos.parcelas))
	 						.done(function(res){
	 							var arrKeys = Object.keys(res),
	 								registros_afectados = 0;
	 							for (var i = 0; i < arrKeys.length; i++) {
	 								registros_afectados = registros_afectados + res[arrKeys[i]].rowsAffected;
	 							};

	 							total_registros_afectados += registros_afectados;
	 							progressBar.actualizarPorcentaje("Actualizando parcelas", total_registros_afectados);
	 							$.when ( servicio.insertarFormularios (datos.formularios)
	 								.done(function(res){
	 									total_registros_afectados += res.rowsAffected;
	 									progressBar.actualizarPorcentaje("Actualizando formularios", total_registros_afectados);
	 									$.when ( servicio.insertarVariables (datos._variables_)
			 								.done(function(res){
			 									total_registros_afectados += res.rowsAffected;
			 									progressBar.actualizarPorcentaje("Actualizando variables", total_registros_afectados);
			 									$.when ( servicio.insertarEtapas(datos.etapas)
			 										.done(function(res){
														total_registros_afectados += res.rowsAffected;
			 											progressBar.actualizarPorcentaje("Actualizando información", total_registros_afectados);
			 											self.fin(total_registros_afectados);
			 										})
			 										.fail(fnError)
			 									);
		 									})
		 									.fail(fnError)
		 								);//EndWhen
	 									//self.fin(total_registros_afectados);
	 								})
	 								.fail (fnError)
	 								);//EndWhen
	 						})
							.fail (fnError)
	 						//);//EndWhen

	 				}).
	 				fail(fnError)
	 				);//EndWhen
	 		})
	 		.fail (fnError)
	 	);//EndWhen
	};

	this.fin = function(){
		progressBar.completarPorcentaje("¡Listo!");
		//renderLblEnviar(0,0);
		setTimeout(function(){
			progressBar.esconder();
			progressBar.destroy();
			progressBar = null;	
		},1300);
	};

	this.sincronizarDatos = function(){
		/*parcela, cultivo (variedad), hectareas, parcela,  muestras por hectareas, muestra actual*/
		var cod_usuario = DATA_NAV.usuario.cod_usuario,
			reqObj = {
			  biometria: servicio.obtenerFrmRegistros("frm-biometria",cod_usuario),
              diatraea: servicio.obtenerFrmRegistros("frm-diatraea",cod_usuario),
              roya: servicio.obtenerFrmRegistros("frm-roya",cod_usuario),
              carbon: servicio.obtenerFrmRegistros("cfrm-arbon",cod_usuario),
              elasmopalpus: servicio.obtenerFrmRegistros("frm-elasmopalpus",cod_usuario),
              metamasius: servicio.obtenerFrmRegistros("frm-metamasius",cod_usuario)
            },
            self = this;

        $.whenAll(reqObj)
          .done(function (res) {
            //var uiRow = res.UI.rows.item(0);
            var objEnvioFrm = {
            	"biometria" : procesarData(res.biometria.rows),
            	"diatraea" : procesarData(res.diatraea.rows),
            	"roya": procesarData(res.roya.rows),
            	"carbon": procesarData(res.carbon.rows),
            	"elasmopalpus" : procesarData(res.elasmopalpus.rows),
            	"metamasius" : procesarData(res.metamasius.rows),
            	"cabecera": {
            		cod_evaluador: DATA_NAV.usuario.cod_usuario,
            		cod_movil: "ANDROID_DEMO"
            	}
            };
            try{

            	if (TOTAL_REGISTOS_ENVIO <= 0){
            		alert("No hay registros para enviar.");
            		TOTAL_REGISTOS_ENVIO = 0;
            		return;
            	}
            	enviarData(JSON.stringify(objEnvioFrm));
            } catch(e){
            	console.error("JSON", e);
            }	
	            
          })
          .fail(function (firstFail, name) {
            console.log('Fail for: ' + name);
            console.error(firstFail);
          });
		/*consultar datos del server
			1.- Consultar los datos de las tablas frm_*
			2.- Enviar un formato 
					frm.* [
						a,
						b,
						c,
						d,
						e.
					]

			3.- Cada bloque tendra puros hijos
				Se armará la cabecerá segun
				cod_parcela y formulario
				los registros deberán ordenarse por cod_parcela

		*/
	};

	var renderLblEnviar = function (numero_registros_totales, numero_registros_propios) {
		if ($label_enviar){
			if (numero_registros_propios > 0){
	     		$label_enviar.html("Hay <b>"+numero_registros_propios+"</b> registros para enviar.");	
	     	} else {
	     	 	$label_enviar.empty();	
	     	}
		}
		
     	TOTAL_REGISTROS_PENDIENTES = numero_registros_totales;
     	TOTAL_REGISTROS_PENDIENTES_PROPIOS = numero_registros_propios;
	};

	this.consultarRegistrosPendientes = function(){		
		var self = this;
        
		$.when( servicio.consultarRegistrosPendientes(data_usuario.cod_usuario)
     		.done( function( resultado ){ 
     			var rows = resultado.rows,
     				numRegistros = 0,
     				numRegistrosPropios = 0;

     			if (rows.length > 0){
     			  numRegistros = rows[0].totales;
     			  numRegistrosPropios = rows[0].propios;
     			  renderLblEnviar(numRegistros, numRegistrosPropios);    
     			}
      		})
          .fail(function(e){
              console.error(e);    
          })
      	); 
      	//EndWhen
	};

	var setDetalle = function(objDetalle, cod_formulario){		
		var objRetorno = {};
		switch (cod_formulario){	
			case 1: 
				objRetorno = {
					bio_data_entrenudos : objDetalle.bio_data_entrenudos,
					bio_etapa_fenologica : objDetalle.bio_etapa_fenologica,
					bio_ml_metros:  objDetalle.bio_ml_metros,
					bio_ml_tallos: objDetalle.bio_ml_tallos,
					bio_ml_tallos_metros:  objDetalle.bio_ml_tallos_metros,
					bio_pt_peso_tallos:  objDetalle.bio_pt_peso_tallos,
					bio_pt_pesos:  objDetalle.bio_pt_pesos,
					bio_pt_tallos:  objDetalle.bio_pt_tallos,
					bio_pt_toneladas:  objDetalle.bio_pt_toneladas
				};
			break;

			case 2: 
				objRetorno = {
					dia_billaea_larvas: objDetalle.dia_billaea_larvas,
					dia_billaea_pupas: objDetalle.dia_billaea_pupas,
					dia_crisalidas: objDetalle.dia_crisalidas,
					dia_entrenudos: objDetalle.dia_entrenudos,
					dia_entrenudos_infestados: objDetalle.dia_entrenudos_infestados,
					dia_larvas_estadio_1: objDetalle.dia_larvas_estadio_1,
					dia_larvas_estadio_2: objDetalle.dia_larvas_estadio_2,
					dia_larvas_estadio_3: objDetalle.dia_larvas_estadio_3,
					dia_larvas_estadio_4: objDetalle.dia_larvas_estadio_4,
					dia_larvas_estadio_5: objDetalle.dia_larvas_estadio_5,
					dia_larvas_estadio_6: objDetalle.dia_larvas_estadio_6,
					dia_larvas_indice: objDetalle.dia_larvas_indice,
					dia_larvas_parasitadas: objDetalle.dia_larvas_parasitadas,
					dia_tallos: objDetalle.dia_tallos,
					dia_tallos_infestados: objDetalle.dia_tallos_infestados
				};
			break;

			case 3: 
				objRetorno = {
					ela_area_muestreada: objDetalle.ela_area_muestreada,
					ela_larvas: objDetalle.ela_larvas,
					ela_larvas_muertas: objDetalle.ela_larvas_muertas,
					ela_pupas: objDetalle.ela_pupas,
					ela_tallos_infectados: objDetalle.ela_tallos_infectados,
					ela_tallos_metro: objDetalle.ela_tallos_metro
				};
			break;

			case 4: 
				objRetorno = {
					car_n_metros: objDetalle.car_n_metros,
					car_tallos: objDetalle.car_tallos,
					car_tallos_latigo: objDetalle.car_tallos_latigo
				};
			break;

			case 5: 
				objRetorno = {
					met_entrenudos_evaluados: objDetalle.met_entrenudos_evaluados,
					met_tallos_danados: objDetalle.met_tallos_danados,
					met_tallos_evaluados: objDetalle.met_tallos_evaluados
				};
			break;

			case 6: 
				objRetorno = {
					roy_hojas: objDetalle.roy_hojas,
					roy_hojas_afectadas: objDetalle.roy_hojas_afectadas,
					roy_porcentaje_afectadas: objDetalle.roy_porcentaje_afectadas
				};
			break;

		}

		objRetorno.foto_registro_1  = objDetalle.foto_registro_1;
		objRetorno.foto_registro_2  = objDetalle.foto_registro_2;
		objRetorno.foto_registro_3  = objDetalle.foto_registro_3;
		objRetorno.longitud_coord  = objDetalle.longitud_coord;
		objRetorno.latitud_coord  = objDetalle.latitud_coord;

		return objRetorno;
	};

	var procesarData = function(res){
	 	var nuevoArreglo = [],
	 		aArreglo = rs2Array(res),
	 		cabecera = {},
	 		registros = {},
	 		nuevoCodParcela = null,
	 		lastCodParcela = null,
	 		objParcela = null,
	 		arDetalles = [];

	 	 //ordenador por cod_parcela
	 	 //cada procesar data es el una enfermedad.
		 for (var i = 0; i < aArreglo.length; i++) {
		 	var objArreglo = aArreglo[i];
		 	nuevoCodParcela = objArreglo.cod_parcela;

		 	if (lastCodParcela == null){
		 		lastCodParcela = nuevoCodParcela;
		 		objParcela = {
		 			cod_parcela : lastCodParcela,
		 			fecha_registro : objArreglo.fecha_hora_registro,
		 			cod_formulario : objArreglo.cod_formulario,
		 			detalles: []
		 		};

		 		arDetalles.push(setDetalle(objArreglo, objArreglo.cod_formulario));
		 		continue;
		 	}

		 	if (nuevoCodParcela != lastCodParcela){
		 		//Es una nueva parcela
		 		objParcela.detalles = arDetalles;
		 		nuevoArreglo.push(objParcela);

		 		arDetalles = [];
		 		objParcela = {
		 			cod_parcela : nuevoCodParcela,
		 			fecha_registro : objArreglo.fecha_hora_registro,
		 			cod_formulario : objArreglo.cod_formulario,
		 			detalles: []
		 		}; 
		 	}

		 	arDetalles.push(setDetalle(objArreglo, objArreglo.cod_formulario));
		 	lastCodParcela = nuevoCodParcela;
		 };

		 if (objParcela){
		 	objParcela.detalles = arDetalles;
			 nuevoArreglo.push(objParcela);
			 arDetalles = [];
		 }

		 nuevoCodParcela = null;
	 	 lastCodParcela = null;
	 	 objParcela = null;

	 	 TOTAL_REGISTOS_ENVIO += nuevoArreglo.length;
       	 return nuevoArreglo;
	};

	var enviarData = function(JSONData){

		if (modalMensaje){
			modalMensaje.destroy();
		}
		modalMensaje = new ModalMensajeComponente().initRender({titulo: "Resincronizando...", texto_informacion: "Enviando información al servidor. Espere."});
		modalMensaje.mostrar();

		$.when( servicio_web.sincronizarDatos(JSONData)
	     		.done( function(r){	     			
	     			if (r.rpt){	
	     				modalMensaje.esconder();
	     				modalMensaje.destroy();
	     				modalMensaje = null;     	
						//eliminar todos los registros de este usuario.
	     				alert("Registros enviados");
	     				renderLblEnviar(TOTAL_REGISTROS_PENDIENTES - TOTAL_REGISTROS_PENDIENTES_PROPIOS, 0);
     			 		servicio.eliminarRegistrosEnviados(data_usuario.cod_usuario);
	     			}
	      		})
	      		.fail(function(error){
	      			console.error(error);
	      		})
	      		);
	}

	this.irMapas = function(){
		router.load("mapa");
	};

	this.irListaCampo = function(){
		router.load("lista-campos");
	};

	this.destroy = function(){
		$label_enviar = null;
		progressBar = null;
		modalMensaje = null;
		TOTAL_REGISTOS_ENVIO = 0;
		this.$el.off("click","#btn-actualizar", this.actualizarDatos);
     	this.$el.off("click","#btn-enviar", this.sincronizarDatos);
     	this.$el.off("click","#btn-mapa", this.irMapas);
     	this.$el.off("click","#btn-listas", this.irListaCampo);

		this.$el = null;
	};

    this.initialize();  
}
