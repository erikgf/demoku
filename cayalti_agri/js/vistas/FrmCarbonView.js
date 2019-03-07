var FrmCarbonView = function (servicio_frm, params) {
	var self = this,
        $content,
        arregloCarbonComponente = [],
        INFESTACION_PROMEDIO = 0.00,
        $resumen,
		rs2Array = resultSetToArray,
        $DOM;

	this.initialize = function () {
        this.$el = $('<div/>');
        if (ACTUAL_PAGE != null && (typeof ACTUAL_PAGE.destroy == "function")){
            ACTUAL_PAGE.destroy();
        }
        ACTUAL_PAGE = this;
    };

    this.render = function() {
        this.consultarDatosInterfaz();
	    return this;
	};

    this.setDOM = function(){
        $DOM = preDOM2DOM($content, 
                    [{"muestras_registradas":"._muestras-registradas"},
                     {"bloque_carbon":"._bloque-carbon"},
                     {"agregar_nuevo_carbon": "._agregar-nuevo-carbon"},
                     {"infestacion_promedio": "._infestacion-promedio"}
                    ]);
    };


    var __agregarCarbon = function(e){
            e.preventDefault();
            agregarCarbon();
        },
        __changeInput = function(e){
            var classList = this.classList;
            if (classList.contains("_tallos")  ){
                verificarTallos(this);
                return;
            }

            if (classList.contains("_tallos-latigo")  ){
                verificarLatigos(this);
                return;
            }
        },
        __guardarMuestra =  function(e){
            self.finalizarEvaluacion();
        };

    this.setEventos = function(){
        $content.on("click", "li._agregar-nuevo-carbon", __agregarCarbon);
        $content.on("change","input", __changeInput);
        $content.on("click", "#btn-guardar-muestra",__guardarMuestra);
    };

     var validarMuestra = function(){
        var valido = true;

        for (var i = 0, len = arregloCarbonComponente.length; i < len; i++) {
            var objCarbon = arregloCarbonComponente[i];
            if (objCarbon != null){
              if (!objCarbon.validar()){
                valido = false;
                break;
             }      
            }
        };

        return valido;
    };

    var verificarTallos = function(input){
        var tallos = input.value;

        input.classList.remove("error");

        if (tallos.length < 1 || tallos < 1){
            input.value = "";
        } else {
            tallos = parseInt(tallos);
            input.value = tallos;
        }
    };

    var verificarLatigos = function(input){
        var tallos = input.value;

        if (tallos.length < 1 || tallos < 0){
            input.value = "0";
        } else {
            tallos = parseInt(tallos);
            input.value = tallos;
        }
    };


     var agregarCarbon = function(){
        var DOM = $DOM,
            $bloque_carbon = DOM.bloque_carbon,
            index = arregloCarbonComponente.length,
            objNuevoCarbon = new NuevoCarbonComponente(index, self);

        arregloCarbonComponente[index] = objNuevoCarbon;
        $bloque_carbon.append(objNuevoCarbon.render().$el);
    };

    var UIDone = function (res) {
            var uiRow = res.UI.rows.item(0),
                finalizado = uiRow.muestras_finalizadas > 0;

            if (!finalizado){
                uiRow.puedo_registrar = true;
                self.$el.html(self.template(uiRow)); 
                $content = self.$el.find(".content"); 
                self.setDOM();
                self.setEventos();
            } else {
                uiRow.puedo_registrar = false;
                self.$el.html(self.template(uiRow));     
                $content = self.$el.find(".content");
                self.setDOM();
            }

            llenarRegistros(rs2Array(res.registros.rows), finalizado);
        },
        UIFail = function (firstFail, name) {
            console.log('Fail for: ' + name);
            console.error(firstFail);
        }; 

    this.consultarDatosInterfaz = function(){
        /*parcela, cultivo (variedad), hectareas, parcela,  muestras por hectareas, muestra actual*/
        var reqObj = {
              UI: servicio_frm.obtenerUICarbon(params.cod_parcela),
              registros : servicio_frm.obtenerRegistrosCarbon(params.cod_parcela)
            };

        $.whenAll(reqObj)
          .done(UIDone)
          .fail(UIFail);
    };

    var llenarRegistros = function(arregloRegistros, finalizado){
       var  DOM = $DOM,
            $bloque_carbon = DOM.bloque_carbon,
            $infestacion_promedio = DOM.infestacion_promedio,
            sumatoriaPorcentajeDañados = 0.0,
            promedioPorcentaje = 0.00,
            fnCalcular = function(tallos, tallos_latigos){
                return tallos_latigos / tallos * 100;
            },
            contador = 0;

        for (var i = 0; i < arregloRegistros.length; i++) {
            var objRegistros = arregloRegistros[i],
                index = objRegistros.item,
                objNuevoCarbon = new NuevoCarbonComponente(index, self),
                porcentajeDaño = parseFloat(fnCalcular(objRegistros.car_tallos, objRegistros.car_tallos_latigo)).toFixed(1);

            $bloque_carbon.append(objNuevoCarbon.render().$el);
            objNuevoCarbon.setValores({tallos: objRegistros.car_tallos, tallos_latigos: objRegistros.car_tallos_latigo},
                                            porcentajeDaño
                                            );

            sumatoriaPorcentajeDañados += parseFloat(porcentajeDaño);
            contador++;
            arregloCarbonComponente[index] = objNuevoCarbon;
        };

        if (contador <= 0){
            INFESTACION_PROMEDIO = 0.00;
            $infestacion_promedio.html("0.00%");
        } else {
            $bloque_carbon.find("input").attr("readonly", finalizado);
            promedioPorcentaje = sumatoriaPorcentajeDañados / contador;
            INFESTACION_PROMEDIO = parseFloat(promedioPorcentaje).toFixed(2);

            $infestacion_promedio.html(INFESTACION_PROMEDIO+ " %");
       

        }

        $bloque_carbon = null;
        $infestacion_promedio = null;
        DOM = null;
        sumatoriaPorcentajeDañados = null;
        promedioPorcentaje = null;
        contador = 0;
    };

    this.recalcular = function(indexBorrado){
        var sumatoriaPorcentajeDañados = 0,
            promedioPorcentaje,
            fnCalcular = function(tallos, tallos_latigos){
                return tallos_latigos / tallos * 100;
            },
            len = arregloCarbonComponente.length,
            $infestacion_promedio = $DOM.infestacion_promedio,
            contador = 0;

        if (indexBorrado != undefined){
            arregloCarbonComponente[indexBorrado] = null;    
        }

        for (var i = 0; i < len; i++) {
            objCarbon = arregloCarbonComponente[i];
            if (objCarbon != null){
                objCarbon = objCarbon.getValores();
                if (objCarbon.tallos == "" || objCarbon.tallos_latigos == ""){
                    continue;
                }

                sumatoriaPorcentajeDañados += parseFloat(fnCalcular(objCarbon.tallos, objCarbon.tallos_latigos).toFixed(2));
                contador++;
            }
        };

        if (contador <= 0){
            INFESTACION_PROMEDIO = 0.00;
            $infestacion_promedio.html("0.00%");
            return;
        }

        promedioPorcentaje = sumatoriaPorcentajeDañados / contador;
        INFESTACION_PROMEDIO = parseFloat(promedioPorcentaje).toFixed(2);

        $infestacion_promedio.html(INFESTACION_PROMEDIO+ " %");
    };

    this.grabarRegistro = function(index, valores, insertUpdate){
            var DOM = $DOM, 
                objMuestra = {
                    item: index,
                    cod_parcela : params.cod_parcela,
                    car_tallos: parseInt(valores.tallos),
                    car_tallos_latigo: parseInt(valores.tallos_latigos),
                    finalizacion : false,
                    usuario_registro : DATA_NAV.usuario.cod_usuario
                };
        /*insertUpdate : + => hacer un insert, * => hacer un update bajo cod_parcela, index, cod_formulario */
            $.when( servicio_frm.agregarMuestraCarbon(objMuestra, insertUpdate)
                    .done(function(r){
                        var $muestras_registradas = $DOM.muestras_registradas;
                        self.recalcular();
                        console.log(insertUpdate);
                        if (insertUpdate == "+"){
                            $muestras_registradas.html(parseInt($muestras_registradas.html()) + 1);
                        }
                    })
                    .fail(function(e){
                        console.error(e);
                    })
                );
    };  

    this.quitarRegistro = function(index){
        $.when( servicio_frm.quitarMuestraCarbon(index, params.cod_parcela)
                    .done(function(r){
                        var $muestras_registradas = $DOM.muestras_registradas;
                        self.recalcular(index);
                        $muestras_registradas.html(parseInt($muestras_registradas.html()) - 1);
                    })
                    .fail(function(e){
                        console.error(e);
                    })
                );
    };

    this.finalizarEvaluacion = function(){
        if (validarMuestra()){
            if (!confirm("¿Desea finalizar evaluación?")){
                return;
            }

            var reqObj = {
              getIndex: servicio_frm.obtenerLastIndexCarbon(params.cod_parcela)
            };

            $.whenAll(reqObj)
              .done(function (res) {
                var uiRow = res.getIndex.rows.item(0),
                    lastIndex = uiRow.last_index,
                    objMuestra = {
                          cod_parcela : params.cod_parcela,
                          item : lastIndex
                        };

                    $.when( servicio_frm.finalizarCarbon(objMuestra)
                            .done(function(r){
                                history.back();
                                alert("Evaluación finalizada.");
                            })
                            .fail(function(e){
                                console.error(e);
                            })
                        );
               
              })
              .fail(UIFail);
        }
    };  

    this.initialize();  
};