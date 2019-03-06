var FrmCarbonView = function (servicio_frm, params) {
	var self = this,
        $content,
        NUMERO_METROS,
        arregloCarbonComponente = [],
        INFESTACION_PROMEDIO = 0.00,
        LAST_INDEX = -1,
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
                    [{"tallos":"._tallos"},
                     {"tallos_latigo":"._tallos-latigo"}
                    ]);

        $DOM = preDOM2DOM($content, 
                    [{"bloque_carbon":"._bloque-carbon"},
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
            self.guardaMuestra();
        };

    this.setEventos = function(){
        $content.on("click", "li._agregar-nuevo-carbon", __agregarCarbon);
        $content.on("change","input", __changeInput);
        $content.on("click", "#btn-guardar-muestra",__guardarMuestra);
    };

     var validarMuestra = function(){
        var $tallos = $DOM.tallos,
            tallos = $tallos.val();

        if (tallos.length < 1 || tallos < 1){
            $tallos.focus().addClass("error");
            alert("Necesita registrar número de tallos");
            return false;
        }

        return true;
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

    this.consultarDatosInterfaz = function(){
        /*parcela, cultivo (variedad), hectareas, parcela,  muestras por hectareas, muestra actual*/
        var reqObj = {
              UI: servicio_frm.obtenerUICarbon(params.cod_parcela)
            },
            self = this;

        $.whenAll(reqObj)
          .done(function (res) {
            var uiRow = res.UI.rows.item(0);
            NUMERO_METROS = uiRow.n_metros; 
            if ((uiRow.muestras_recomendadas >= parseInt(uiRow.numero_muestra_actual)) && (uiRow.muestras_finalizadas == 0)) {
                uiRow.puedo_registrar = true;
                self.$el.html(self.template(uiRow)); 
                $content = self.$el.find(".content");
                self.setDOM();
                self.setEventos();
            } else {
                uiRow.puedo_registrar = false;
                uiRow.numero_muestra_actual--;
                self.$el.html(self.template(uiRow));     
                $content = self.$el.find(".content");
            }
          })
          .fail(function (firstFail, name) {
            console.log('Fail for: ' + name);
            console.error(firstFail);
          });
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


            console.log(insertUpdate);

        /*insertUpdate : + => hacer un insert, * => hacer un update bajo cod_parcela, index, cod_formulario */
            $.when( servicio_frm.agregarMuestraCarbon(objMuestra, insertUpdate)
                    .done(function(r){
                        if (insertUpdate == "+"){
                            LAST_INDEX = index;    
                        }
                        self.recalcular();
                        console.log("Agregado");
                    })
                    .fail(function(e){
                        console.error(e);
                    })
                );
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

        console.log(sumatoriaPorcentajeDañados, contador);

        promedioPorcentaje = sumatoriaPorcentajeDañados / contador;
        INFESTACION_PROMEDIO = parseFloat(promedioPorcentaje).toFixed(2);

        $infestacion_promedio.html(INFESTACION_PROMEDIO+ " %");
    };

    this.quitarRegistro = function(index){
        $.when( servicio_frm.quitarMuestraCarbon(index, params.cod_parcela)
                    .done(function(r){
                        self.recalcular(index);
                        console.log("Eliminado");                        
                        //history.back();
                    })
                    .fail(function(e){
                        console.error(e);
                    })
                );
    };

    this.guardaMuestra = function(){
        if (validarMuestra()){
            if (!confirm("¿Desea guardar esta muestra?")){
                return;
            }

            var DOM = $DOM, 
                objMuestra = {
                    cod_parcela : params.cod_parcela,
                    car_n_metros: NUMERO_METROS,
                    car_tallos: parseInt(DOM.tallos.val()),
                    car_tallos_latigo: parseInt(DOM.tallos_latigo.val()),
                    finalizacion : false,
                    usuario_registro: DATA_NAV.usuario.cod_usuario
                };

            $.when( servicio_frm.agregarMuestraCarbon(objMuestra)
                    .done(function(r){
                        history.back();
                        console.log("Muestra guardada.");
                    })
                    .fail(function(e){
                        console.error(e);
                    })
                );
        }
    };  

    this.initialize();  
};