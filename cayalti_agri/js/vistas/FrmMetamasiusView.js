var FrmMetamasiusView = function (servicio_frm, params) {
	var self = this,
        $content,
        MUESTRA_ACTUAL,
        MUESTRAS_RECOMENDADAS,
        TALLOS_EVALUADOS,
        ENTRENUDOS_EVALUADOS,
        $resumen,
		rs2Array = resultSetToArray,
        frmRoyaResumenView,
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
                    [{"tallos_evaluados": "._tallos-evaluados"},
                     {"tallos_dañados":"._tallos-dañados"},
                     {"tallos_dañados_porcentaje":"._tallos-dañados-porcentaje"},
                     {"entrenudos_evaluados": "._entrenudos-evaluados"},
                     {"entrenudos_dañados":"._entrenudos-dañados"},
                     {"entrenudos_dañados_porcentaje":"._entrenudos-dañados-porcentaje"},
                     {"larvas":"._larvas"},
                     {"larvas_tallo":"._larvas-tallo"}
                    ]);
        //$entrenudos = $(", $entrenudos_infestados, $entrenudos_porcentaje_infestado
    }

    //Callbacks Eventos;

    var __changeInput = function(e){
            var classList = this.classList;
            if (classList.contains("_tallos-dañados")){
                calcularInfestacion("tallos");
                return;
            }

            if (classList.contains("_entrenudos-dañados")){
                calcularInfestacion("entrenudos");
                return;
            }

            if (classList.contains("_larvas")){
                calcularLarvasTallo();
                return;
            }

        },
        __guardar = function(e){
            e.preventDefault();
            self.guardarMuestra();
        },
        __finalizarEvaluacion = function(e){
            self.finalizarEvaluacion();
        };

    this.setEventos = function(){
        $content.off("change").on("change","input",__changeInput);
        $content.off("click").on("click", "#btn-guardar", __guardar);
        $content.on("click", "#btn-finalizar", __finalizarEvaluacion);
    };

    var calcularInfestacion = function(opcion){
        var DOM = $DOM,
            opcion_ = DOM[opcion+"_evaluados"].val(),
            $opcion_dañados = DOM[opcion+"_dañados"],
            $opcion_porcentaje = DOM[opcion+"_dañados_porcentaje"],
            opcion_dañados = $opcion_dañados.val(),
            opcion_porcentaje = $opcion_porcentaje.val(),
            fnResetearEstado = function(){
                $opcion_dañados.val("0")
                $opcion_porcentaje.html("0.00%");
            };

        $opcion_dañados.removeClass("error");

        if (opcion_dañados.length > 0){
            opcion_dañados = parseInt(opcion_dañados);

            if (opcion_ < opcion_dañados || opcion_dañados <= 0){
                fnResetearEstado();
                return;
            }

            $opcion_porcentaje.html(parseFloat(opcion_dañados/opcion_*100).toFixed(2)+"%");
        } else {
            fnResetearEstado();
        }
    };
    var calcularLarvasTallo = function(){
        var DOM = $DOM,
            $larvas = DOM.larvas,
            larvas = $larvas.val(),
            $larvas_tallo = DOM.larvas_tallo,
            tallos_evaluados = DOM.tallos_evaluados.val(),
            fnResetearEstado = function(){
                $larvas.val("0")
                $larvas_tallo.html("0.0");
            };

        if (larvas.length > 0){
            larvas = parseInt(larvas);

            if (larvas <= 0){
                fnResetearEstado();
                return;
            }

            $larvas_tallo.html(tallos_evaluados <= 0 ? 
                                    "0.0" :
                                    parseFloat(larvas/tallos_evaluados).toFixed(1));
        } else {
            fnResetearEstado();
        }
    };

    var UIDone = function (res) {
           var uiRow = res.UI.rows.item(0),
                muestras_recomendadas = uiRow.muestras_recomendadas;     

                if (muestras_recomendadas <= 0){
                    uiRow.muestras_recomendadas = 1;
                    muestras_recomendadas = 1;
                }            

                if ((muestras_recomendadas >= uiRow.numero_muestra_actual) && (uiRow.muestras_finalizadas == 0)) {
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
                frmMetamasiusResumenView = new FrmMetamasiusResumenView(procesarResumen(rs2Array(res.resumen.rows)));
                $resumen.html(frmMetamasiusResumenView.$el);    
        },
        UIFail = function (firstFail, name) {
            console.log('Fail for: ' + name);
            console.error(firstFail);
        };


    this.consultarDatosInterfaz = function(){
        /*parcela, cultivo (variedad), hectareas, parcela,  muestras por hectareas, muestra actual*/
        var reqObj = {
              UI: servicio_frm.obtenerUIMetamasius(params.cod_parcela),
              resumen: servicio_frm.obtenerResumenMetamasius(params.cod_parcela)
            },
            self = this;

        $.whenAll(reqObj)
          .done(UIDone)
          .fail(UIFail);
    };

   var validarMuestra = function(){
        return true;
    };

   var procesarResumen = function(arregloMuestras){
        var porcentaje_tallos_dañados = 0.00,
            porcentaje_entrenudos_dañados = 0.00,
            promedio_larvas_tallo = 0.0,
            totalMuestras = arregloMuestras.length;

        for (var i = totalMuestras - 1; i >= 0; i--) {
            var objMuestra = arregloMuestras[i];
            porcentaje_tallos_dañados +=  (objMuestra.met_tallos_danados / objMuestra.met_tallos_evaluados);
            porcentaje_entrenudos_dañados += ( objMuestra.met_entrenudos_danados / objMuestra.met_entrenudos_evaluados);
            promedio_larvas_tallo += objMuestra.met_larvas / objMuestra.met_tallos_evaluados;
        };

        return {
                cantidad: totalMuestras,
                cantidad_valida:totalMuestras > 0,
                porcentaje_tallos_dañados : parseFloat(porcentaje_tallos_dañados / totalMuestras * 100).toFixed(2),
                porcentaje_entrenudos_dañados : parseFloat(porcentaje_entrenudos_dañados / totalMuestras * 100).toFixed(2),
                promedio_larvas_tallo : parseFloat(promedio_larvas_tallo / totalMuestras).toFixed(1)
            };
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
                        item : MUESTRA_ACTUAL,
                        cod_parcela : params.cod_parcela,
                        met_tallos_evaluados: parseInt(DOM.tallos_evaluados.val()),
                        met_tallos_danados: parseInt(DOM.tallos_dañados.val()),
                        met_entrenudos_evaluados: parseInt(DOM.entrenudos_evaluados.val()),
                        met_entrenudos_danados : parseInt(DOM.entrenudos_dañados.val()),
                        met_larvas : parseInt(DOM.larvas.val()),
                        finalizacion : esFinalizar,
                        usuario_registro: DATA_NAV.usuario.cod_usuario
                    };
                
                $.when( servicio_frm.agregarMuestraMetamasius(objMuestra)
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

    var reiniciarFormulario = function(){
        var reqObj = {
              UI: servicio_frm.obtenerUIMetamasius(params.cod_parcela),
              resumen: servicio_frm.obtenerResumenMetamasius(params.cod_parcela)
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
        if ($content){
            $content.off("change").off("change","input",__changeInput);
            $content.off("click").off("click", "#btn-guardar", __guardar);
            $content.off("click", "#btn-finalizar", __finalizarEvaluacion);
        }
        frmMetamasiusResumenView.destroy();
        frmMetamasiusResumenView = null;
        $resumen = null;
        $DOM = null;
    };

    this.destroy = function(){
        destroyBase();
        MUESTRAS_RECOMENDADAS = null;
        $content = null;
        rs2Array = null ;
        this.$el = null;        
        self = null;
    };


    this.initialize();  
};