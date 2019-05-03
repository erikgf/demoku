var FrmElasmopalpusView = function (servicio_frm, params) {
	var self = this,
        $content,
        MUESTRA_ACTUAL,
        MUESTRAS_RECOMENDADAS,
        TALLOS_MUESTREADOS,
        $resumen,
		rs2Array = resultSetToArray,
        frmElasmopalpusResumenView,
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
                    [{"tallos_metro": "._tallos-metro"},
                     {"tallos_infectados":"._tallos-infectados"},
                     {"tallos_porcentaje":"._tallos-porcentaje"},
                     {"area_muestreada":"._area-muestreada"},
                     {"larvas":"._larvas"},
                     {"larvas_metro":"._larvas-metro"},
                     {"pupas":"._pupas"},
                     {"pupas_metro":"._pupas-metro"},
                     {"larvas_muertas":"._larvas-muertas"},
                     {"larvas_muertas_metro":"._larvas-muertas-metro"}
                    ]);
        //$entrenudos = $(", $entrenudos_infestados, $entrenudos_porcentaje_infestado
    }

    var __changeInput = function(e){
            var classList = this.classList;
            if (classList.contains("_tallos-metro") ||  classList.contains("_tallos-infectados")){
                calcularTallosInfestacion();
                return;
            }

            if (classList.contains("_area-muestreada")){
                calcularBaseAreas();
                return;
            }

            if (classList.contains("_larvas")){
                calcularPorMetro("larvas");
                return;
            }

            if (classList.contains("_pupas")){
                calcularPorMetro("pupas");
                return;
            }

            if (classList.contains("_larvas-muertas")){
                calcularPorMetro("larvas_muertas");
                return;
            }
        },
        __guardarMuestra = function(e){
            e.preventDefault();
            self.guardarMuestra();
        },
        __finalizarEvaluacion = function(e){
            self.finalizarEvaluacion();
        };

    this.setEventos = function(){
        $content.on("change","input", __changeInput);
        $content.on("click", "#btn-guardar-muestra", __guardarMuestra);
        $content.on("click", "#btn-finalizar", __finalizarEvaluacion);

    };

    var calcularTallosInfestacion = function(){
        var DOM = $DOM,
            $tallos_metro = DOM.tallos_metro,
            $tallos_infectados = DOM.tallos_infectados,
            $tallos_porcentaje = DOM.tallos_porcentaje,
            tallos_metro = $tallos_metro.val(),
            tallos_infectados = $tallos_infectados.val(),
            fnResetearEstado = function(){
                $tallos_infectados.val("0")
                $tallos_porcentaje.html((tallos_metro.length < 1) ? "n/a" : "0.00%");
            };

        $tallos_metro.removeClass("error");

        if (tallos_metro.length > 0){
            tallos_metro = parseInt(tallos_metro);

            if ( tallos_metro <= 0){
                $tallos_metro.val("");
                fnResetearEstado();
            } else {
                if (tallos_infectados.length < 1){
                  fnResetearEstado();
                } else {
                  tallos_infectados = parseInt(tallos_infectados);
                  if (tallos_infectados < 0){
                    fnResetearEstado();
                  } else {
                    if (tallos_infectados >= tallos_metro){
                        $tallos_infectados.val(tallos_metro);
                        $tallos_porcentaje.html("100.00%");
                    } else {
                        /*Estado deseado */
                        $tallos_porcentaje.html(parseFloat(tallos_infectados/tallos_metro*100).toFixed(2)+"%");
                    }
                  }
                }
            }
        } else {
            fnResetearEstado();
        }
    };

    var calcularBaseAreas = function(){
        var DOM = $DOM,
            $area_muestreada = DOM.area_muestreada,
            $larvas_metro = DOM.larvas_metro,
            $pupas_metro = DOM.pupas_metro,
            $larvas_muertas_metro = DOM.larvas_muertas_metro,
            area_muestreada = $area_muestreada.val(),
            larvas =  DOM.larvas.val(),
            pupas =  DOM.pupas.val(),
            larvas_muertas =  DOM.larvas_muertas.val();

        $area_muestreada.removeClass("error");

        if (area_muestreada.length > 0){
            if (area_muestreada <= 0){
                $area_muestreada.val("");
            } else {
                $larvas_metro.html(parseFloat(larvas / area_muestreada).toFixed(1));
                $pupas_metro.html(parseFloat(pupas / area_muestreada).toFixed(1));
                $larvas_muertas_metro.html(parseFloat(larvas_muertas / area_muestreada).toFixed(1));    
            }
        } else {
            $larvas_metro.html("n/a");
            $pupas_metro.html("n/a");
            $larvas_muertas_metro.html("n/a");
        }
    };

    var calcularPorMetro = function(str_tipo){
        var DOM = $DOM,
            $area_muestreada = DOM.area_muestreada,
            area_muestreada = $area_muestreada.val(),
            $tipo_metro = DOM[str_tipo+"_metro"],
            $tipo = DOM[str_tipo],
            tipo = $tipo.val();

        if (tipo.length < 1){            
            $tipo.val("0");
            $tipo_metro.html(area_muestreada.length > 0 ? "0.0" : "n/a");
        } else{
            tipo = parseInt(tipo);
            if (tipo < 0){
               $tipo.val("0");     
               $tipo_metro.html(area_muestreada.length > 0 ? "0.0" : "n/a");
            } else {
               $tipo_metro.html(area_muestreada.length > 0 ? (parseFloat(tipo/area_muestreada).toFixed(1)) : "n/a");
            }
        }
    };

    var UIDone = function (res) {
            var uiRow = res.UI.rows.item(0),
                muestras_recomendadas = uiRow.muestras_recomendadas;

            if (muestras_recomendadas <= 0){
                uiRow.muestras_recomendadas = 1;
                muestras_recomendadas = 1;
            }

            if ((muestras_recomendadas >=  uiRow.numero_muestra_actual) && (uiRow.muestras_finalizadas == 0)) {
                MUESTRA_ACTUAL = parseInt(uiRow.numero_muestra_actual);
                MUESTRAS_RECOMENDADAS = muestras_recomendadas;
                uiRow.puedo_registrar = true;
                self.$el.html(self.template(uiRow)); 
                $content = self.$el.find(".content");
                self.setDOM();
                self.setEventos();
            } else {
                uiRow.puedo_registrar = false;
                self.$el.html(self.template(uiRow));     
                $content = self.$el.find(".content");
            }

            $resumen = self.$el.find("#frm-resumen");
            frmElasmopalpusResumenView = new FrmElasmopalpusResumenView(procesarResumen(rs2Array(res.resumen.rows)));
            $resumen.html(frmElasmopalpusResumenView.$el);       
        },
        UIFail = function (firstFail, name) {
            console.log('Fail for: ' + name);
            console.error(firstFail);
        };


    this.consultarDatosInterfaz = function(){
        /*parcela, cultivo (variedad), hectareas, parcela,  muestras por hectareas, muestra actual*/
        var reqObj = {
              UI: servicio_frm.obtenerUIElasmopalpus(params.cod_parcela),
              resumen: servicio_frm.obtenerResumenElasmopalpus(params.cod_parcela)
            },
            self = this;

        $.whenAll(reqObj)
          .done(UIDone)
          .fail(UIFail);
    };

    var validarMuestra = function(){
        var DOM = $DOM,
            $tallos_metro = DOM.tallos_metro,
            tallos_metro = $tallos_metro.val(),
            $area_muestreada,
            area_muestreada;

        if (tallos_metro.length < 1 || tallos_metro < 1){
            $tallos_metro.focus().addClass("error");
            alert("Necesita registrar total tallos por metro.");
            return false;
        }

        $area_muestreada = DOM.area_muestreada,
        area_muestreada = $area_muestreada.val();

        if (area_muestreada.length < 1 || area_muestreada < 1){
            $area_muestreada.focus().addClass("error");
            alert("Necesita registrar total área muestreada (metros).");
            return false;
        }

        return true;
    };

    this.guardarMuestra = function(){
        var rpt = false;
        if (MUESTRAS_RECOMENDADAS == MUESTRA_ACTUAL){
            rpt = true;
        }
        agregarMuestra(rpt);
    };  

    this.finalizarEvaluacion = function(){
        agregarMuestra(true);
    };

    var agregarMuestra = function(esFinalizar){
        if (validarMuestra()){
            var fnConfirm = function(){
                var DOM = $DOM, 
                    objMuestra = {
                        item: MUESTRA_ACTUAL,
                        cod_parcela : params.cod_parcela,
                        ela_tallos_metro: parseInt(DOM.tallos_metro.val()),
                        ela_tallos_infectados: parseInt(DOM.tallos_infectados.val()),
                        ela_area_muestreada: parseFloat(DOM.area_muestreada.val()).toFixed(2),
                        ela_larvas : parseInt(DOM.larvas.val()),
                        ela_pupas : parseInt(DOM.pupas.val()),
                        ela_larvas_muertas : parseInt(DOM.larvas_muertas.val()),
                        finalizacion : esFinalizar,
                        usuario_registro: DATA_NAV.usuario.cod_usuario
                    };
                
                $.when( servicio_frm.agregarMuestraElasmopalpus(objMuestra)
                        .done(function(r){
                           if (esFinalizar){
                                history.back();
                                alert("Muestra FINALIZADA.");
                            } else {
                                reiniciarFormulario();
                                alert("Muestra GUARDADA.");
                            }
                        })
                        .fail(function(e){
                            console.error(e);
                        })
                    );
            };

            confirmar("¿Desea "+(esFinalizar ? "finalizar esta parcela" : "guardar esta muestra")+"?",fnConfirm);
        }
    };  

    var procesarResumen = function(arregloMuestras){

        var porcentaje_tallos_dañados = 0.00,
            larvas_metro = 0.0,
            pupas_metro = 0.0,
            larvas_muertas_metro = 0.0,
            totalMuestras = arregloMuestras.length;

        for (var i = totalMuestras - 1; i >= 0; i--) {
            var objMuestra = arregloMuestras[i];
            porcentaje_tallos_dañados +=  (objMuestra.ela_tallos_infectados / objMuestra.ela_tallos_metro);
            larvas_metro += ( objMuestra.ela_larvas / objMuestra.ela_area_muestreada);
            pupas_metro += objMuestra.ela_larvas / objMuestra.ela_area_muestreada;
            larvas_muertas_metro += objMuestra.ela_larvas_muertas / objMuestra.ela_area_muestreada;
        };

        return {
                cantidad: totalMuestras,
                cantidad_valida:totalMuestras > 0,
                porcentaje_tallos_dañados : parseFloat(porcentaje_tallos_dañados / totalMuestras * 100).toFixed(2),
                larvas_metro : parseFloat(larvas_metro / totalMuestras).toFixed(1),
                pupas_metro : parseFloat(pupas_metro / totalMuestras).toFixed(1),
                larvas_muertas_metro : parseFloat(larvas_metro / totalMuestras).toFixed(1)
            };
   };

    var reiniciarFormulario = function(){
        var reqObj = {
              UI: servicio_frm.obtenerUIElasmopalpus(params.cod_parcela),
              resumen: servicio_frm.obtenerResumenElasmopalpus(params.cod_parcela)
            };

        $.whenAll(reqObj)
          .done(function (res) {
            destroyBase();
            UIDone(res);
            $content[0].scrollTop = 0;
          })
          .fail(UIFail);
    };

    var destroyBase = function(){
       TALLOS_MUESTREADOS = null;

       if ($content){
        $content.off("change","input", __changeInput);
        $content.off("click", "#btn-guardar-muestra", __guardarMuestra);
        $content.off("click", "#btn-finalizar", __finalizarEvaluacion);
       }
       frmElasmopalpusResumenView.destroy();
       frmElasmopalpusResumenView = null;
       $resumen = null;
       $DOM = null; 
    };

    this.destroy = function(){
        destroyBase();
        MUESTRAS_RECOMENDADAS = null;
        $content = null;
        $resumen = null;
        rs2Array = null ;
        this.$el = null; 
        self = null;
    };


    this.initialize();  
};