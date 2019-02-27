var FrmBiometriaView = function (servicio_frm, params) {
	var self,
        NUMERO_METROS,
        FACTOR,
        TASA_CRECIMIENTO,
        VOLUMEN_PROMEDIO = 0.00,
        LARGO_PROMEDIO = 0.00,
        TASA_CRECIMIENTO_PROMEDIO = 0.00,
        $content,
		rs2Array = resultSetToArray,
        arregloEntrenudosComponente = [],
        $DOM;

	this.initialize = function () {
        this.$el = $('<div/>');
        if (ACTUAL_PAGE != null && (typeof ACTUAL_PAGE.destroy == "function")){
            ACTUAL_PAGE.destroy();
        }
        ACTUAL_PAGE = this;
        self = this;
    };

    this.render = function() {
        this.consultarDatosInterfaz();
	    return this;
	};

    this.getArreglo = function(){
        console.log(arregloEntrenudosComponente);
    };

    this.setDOM = function(){
        $content = self.$el.find(".content");
        $DOM = preDOM2DOM($content, 
                    [{"etapa_fenologica":"._etapa-fenologica"},
                     {"bloque_nudos":"._bloque-nudos"},
                     {"agregar_nuevo_tallo": "._agregar-nuevo-tallo"},
                     {"volumen_promedio": "._volumen-promedio"},
                     {"largo_promedio": "._largo-promedio"},
                     {"crecimiento_promedio": "._crecimiento-promedio"},
                     {"ml_metros": "._ml-metros"},
                     {"ml_tallos": "._ml-tallos"},
                     {"ml_tallos_metro": "._ml-tallos-metro"},
                     {"pt_pesos": "._pt-pesos"},
                     {"pt_tallos": "._pt-tallos"},
                     {"pt_promedio_peso": "._pt-promedio-peso"},
                     {"pt_toneladas": "._pt-toneladas"}
                    ]);

        //$entrenudos = $(", $entrenudos_infestados, $entrenudos_porcentaje_infestado
    };

    var __agregarEntrenudo = function(e){
            var classList = this.classList;

            if (classList.contains("_agregar-nuevo-tallo")){
                agregarEntrenudo();
                return;
            }
        },
        __cambiarInput = function(e){
            var classList = this.classList;
            /*
            if (classList.contains("_ml-metros")){
                calcularTalloMetroLineal();
                return;
            }
            */
            if (classList.contains("_ml-tallos")){
                calcularTalloMetroLineal();
                calcularPromedioTallo();
                return;
            }

            if (classList.contains("_pt-pesos")){
                calcularPromedioTallo();
                return;
            }

            if (classList.contains("_pt-tallos")){
                calcularPromedioTallo();
                return;
            }
        };

    this.setEventos = function(){
        $content.on("click", "li", __agregarEntrenudo);
        $content.on("change","input", __cambiarInput);
        $content.on("click", "#btn-guardar", agregarMuestra);
/*
        $content.on("click", "#btn-finalizar", function(e){
            self.finalizarEvaluacion();
        });
        */
    };

    var calcularTalloMetroLineal = function(){
        var DOM = $DOM,
            $ml_tallos = DOM.ml_tallos,
            ml_tallos = $ml_tallos.val(),
            $ml_tallos_metro = DOM.ml_tallos_metro,
            ml_tallos_metro,   
            numero_metros = NUMERO_METROS,
            fnResetearEstado = function(){
                $ml_tallos_metro.html("0");
            };

        if (ml_tallos.length <= 0){
            fnResetearEstado();
            return;
        } else {
            ml_tallos = parseInt(ml_tallos);
            ml_tallos_metro = parseFloat(ml_tallos / numero_metros).toFixed(2);
            $ml_tallos_metro.html(ml_tallos_metro);
            $ml_tallos.val(ml_tallos);
        }
    };

    var calcularPromedioTallo  = function(){
        var DOM = $DOM,
            $pt_pesos = DOM.pt_pesos,
            pt_pesos = $pt_pesos.val(),
            $pt_tallos = DOM.pt_tallos,
            pt_tallos = $pt_tallos.val(),
            $pt_promedio_peso = DOM.pt_promedio_peso,
            $pt_toneladas = DOM.pt_toneladas,
            factor = FACTOR,
            fnResetearEstado = function(){
                $pt_promedio_peso.html("0")
                $pt_toneladas.html("0");
            },
            pesosVacio = pt_pesos.length <= 0,
            tallosVacio = pt_tallos.length <= 0,
            ml_tallos_metro = DOM.ml_tallos_metro.html(),
            pt_promedio_peso,
            pt_toneladas;

        if (pesosVacio || tallosVacio){
            fnResetearEstado();
            return;
        }

        if (pt_tallos <= 0){
            pt_tallos = 0;
        }

        if (pt_pesos <= 0){
            pt_pesos = 0;
            $pt_pesos.val(pt_pesos);    
        }

        pt_promedio_peso = pt_pesos / pt_tallos;

        pt_toneladas = parseFloat(pt_promedio_peso * factor * ml_tallos_metro).toFixed(2);
        $pt_tallos.val(parseInt(pt_tallos));
        $pt_toneladas.html(pt_toneladas);
        $pt_promedio_peso.html(parseFloat(pt_promedio_peso).toFixed(2));
    };

    var agregarEntrenudo = function(){
        var DOM = $DOM,
            $bloque_nudos = DOM.bloque_nudos,
            index = arregloEntrenudosComponente.length,
            objNuevoTallo = new NuevoTalloComponente(index, self);

        arregloEntrenudosComponente[index] = objNuevoTallo;
        $bloque_nudos.append(objNuevoTallo.render().$el);
    };

    this.recalcular = function(indexEntrenudoBorrado){
        var sumatoriaVolumen = 0,
            sumatoriaLargo = 0,
            sumatoriaCrecimiento = 0,
            promedioLargo,
            fnCalcularVolumen = function(largo, diametro){
                return Math.PI * Math.pow(diametro/2,2) * largo;
            },
            len = arregloEntrenudosComponente.length,
            DOM = $DOM,
            $volumen_promedio = DOM.volumen_promedio,
            $largo_promedio = DOM.largo_promedio,
            $crecimiento_promedio = DOM.crecimiento_promedio,
            contador = 0;

        if (indexEntrenudoBorrado != undefined){
            arregloEntrenudosComponente[indexEntrenudoBorrado] = null;    
        }

        for (var i = 0; i < len; i++) {
            objEntrenudo = arregloEntrenudosComponente[i];
            if (objEntrenudo != null){
                objEntrenudo = objEntrenudo.getValores();
                if (objEntrenudo.largo == "" && objEntrenudo.diametro == ""){
                    continue;
                }
                sumatoriaVolumen += fnCalcularVolumen(objEntrenudo.largo == "" ? 0: objEntrenudo.largo, objEntrenudo.diametro == "" ? 0: objEntrenudo.diametro);
                sumatoriaLargo += parseInt(objEntrenudo.largo);        
                contador++;
            }
        };

        if (contador <= 0){
            $volumen_promedio.html("n/a");
            $largo_promedio.html("n/a");
            $crecimiento_promedio.html("n/a");
            VOLUMEN_PROMEDIO = 0.00;
            LARGO_PROMEDIO = 0.00;
            TASA_CRECIMIENTO_PROMEDIO = 0.00;
            return;
        }

        promedioLargo = sumatoriaLargo / contador;


        VOLUMEN_PROMEDIO = parseFloat(sumatoriaVolumen / contador).toFixed(2);
        LARGO_PROMEDIO = parseFloat(promedioLargo).toFixed(2);
        TASA_CRECIMIENTO_PROMEDIO = parseFloat(promedioLargo - TASA_CRECIMIENTO).toFixed(2);

        $volumen_promedio.html(VOLUMEN_PROMEDIO+ " cm<sup>3</sup>");
        $largo_promedio.html(LARGO_PROMEDIO+ " cm");
        $crecimiento_promedio.html(TASA_CRECIMIENTO_PROMEDIO+ " cm");

    }

    this.consultarDatosInterfaz = function(){
        /*parcela, cultivo (variedad), hectareas, parcela,  muestras por hectareas, muestra actual*/
        var reqObj = {
              UI: servicio_frm.obtenerUIBiometria(params.cod_parcela)
            };

        $.whenAll(reqObj)
          .done(UIDone)
          .fail(UIFail);
    };

    var UIDone = function (res) {
            var uiRow = res.UI.rows.item(0);
            NUMERO_METROS = uiRow.numero_metros;
            FACTOR = uiRow.factor;
            TASA_CRECIMIENTO = uiRow.tasa_crecimiento;
            self.$el.html(self.template(uiRow)); 

            self.setDOM();
            self.setEventos();
        },
        UIFail = function (firstFail, name) {
            console.log('Fail for: ' + name);
            console.error(firstFail);
        };

    var agregarMuestra = function(){        
        if (validarMuestra()){
            if (!confirm("¿Desea guardar esta muestra?")){
                return;
            }

            var DOM = $DOM,                
                fnObtenerCadenaEntrenudos = function(){
                    var cadenaEntrenudos = "[",
                        len = arregloEntrenudosComponente.length,
                        contador = 0;

                    for (var i = 0; i < len; i++) {
                        objEntrenudo = arregloEntrenudosComponente[i];
                        if (objEntrenudo != null){
                            objEntrenudo = objEntrenudo.getValores();
                            if (objEntrenudo.largo == "" && objEntrenudo.diametro == ""){
                                continue;
                            }

                            cadenaEntrenudos += '{"largo":"'+objEntrenudo.largo+'","diametro":"'+objEntrenudo.diametro+'","entrenudos":"'+(objEntrenudo.entrenudos == undefined ? "" : objEntrenudo.entrenudos)+'"}';
                            contador++;
                            if (len - i < 0){
                                cadenaEntrenudos += ",";
                            }
                        }
                    };

                    cadenaEntrenudos += "]";
                    return cadenaEntrenudos;
                },
                objMuestra = {
                    cod_parcela : params.cod_parcela,
                    bio_etapa_fenologica: DOM.etapa_fenologica.val(),
                    bio_data_entrenudos: fnObtenerCadenaEntrenudos(),
                    bio_volumen_promedio: VOLUMEN_PROMEDIO,
                    bio_largo_promedio: LARGO_PROMEDIO,
                    bio_crecimiento_promedio: TASA_CRECIMIENTO_PROMEDIO,
                    bio_ml_metros: DOM.ml_metros.val(),
                    bio_ml_tallos: DOM.ml_tallos.val() == "" ? null : DOM.ml_tallos.val(),
                    bio_ml_tallos_metros: DOM.ml_tallos_metro.html(),
                    bio_pt_pesos: DOM.pt_pesos.val(),
                    bio_pt_tallos: DOM.pt_tallos.val() == "" ? null : DOM.pt_tallos.val(),
                    bio_pt_peso_tallos: DOM.pt_promedio_peso.html(),
                    bio_pt_toneladas: DOM.pt_toneladas.html(),
                    usuario_registro: DATA_NAV.usuario.cod_usuario
                };

            $.when( servicio_frm.agregarMuestraBiometria(objMuestra)
                    .done(function(r){
                        //history.back();
                        reiniciarFormulario();
                        alert("Muestra guardada");
                    })
                    .fail(function(e){
                        console.error(e);
                    })
                );
        }
    };

    var validarMuestra = function(){
        return true;
    };

    var reiniciarFormulario = function(){
        var reqObj = {
              UI: servicio_frm.obtenerUIBiometria(params.cod_parcela)
            };

        $.whenAll(reqObj)
          .done(function (res) {
            var uiRow = res.UI.rows.item(0);
            destroyBase();

            NUMERO_METROS = uiRow.numero_metros;
            FACTOR = uiRow.factor;
            TASA_CRECIMIENTO = uiRow.tasa_crecimiento;
            self.$el.html(self.template(uiRow));
            self.setDOM();
            self.setEventos();
            $content[0].scrollTop = 0;
          })
          .fail(UIFail);
    };

    var destroyBase = function(){
       NUMERO_METROS = null;
       FACTOR = null;
       TASA_CRECIMIENTO = null;
       
       if ($content){
        $content.off("click","li",__cambiarInput);
        $content.off("change","input",__agregarEntrenudo); 
        $content.off("click", "#btn-guardar", agregarMuestra);
        $content = null;
       }
    
       $DOM = null; 
    };

    this.destroy = function(){
       destroyBase();
       rs2Array = null;
       arregloEntrenudosComponente = null;
    };


    this.initialize();  
};