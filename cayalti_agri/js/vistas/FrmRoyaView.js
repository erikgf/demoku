var FrmRoyaView = function (servicio_frm, params) {
	var self = this,
        $content,
        MUESTRA_ACTUAL,
        MUESTRAS_RECOMENDADAS,
        HOJAS_MUESTREADAS,
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
                    [//{"hojas": "._hojas"},
                     {"hojas_afectadas":"._hojas-afectadas"},
                     {"porcentaje_afectadas":"._porcentaje-afectadas"}
                    ]);
    };

    var __changeInput = function(e){
            var classList = this.classList;
            if (classList.contains("_hojas-afectadas")){
                calcularHojaAfectadas();
                return;
            }
        },
        __guardarMuestra = function(e){
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

    var calcularHojaAfectadas = function(){
        var DOM = $DOM,
            $hojas_afectadas = DOM.hojas_afectadas,
            $porcentaje_afectadas = DOM.porcentaje_afectadas,
            hojas_muestreadas = HOJAS_MUESTREADAS,
            hojas_afectadas = $hojas_afectadas.val(),
            fnResetearEstado = function(desactivar){
                $hojas_afectadas.val("0")
                $porcentaje_afectadas.html("0.00%");
            };

        if (hojas_muestreadas.length > 0){
            hojas_muestreadas = parseInt(hojas_muestreadas);

            if ( hojas_muestreadas <= 0){
                fnResetearEstado();
            } else {
                if (hojas_afectadas.length < 1 || hojas_afectadas < 0){
                  fnResetearEstado();
                } else {
                  hojas_afectadas = parseInt(hojas_afectadas);
                  if (hojas_afectadas < 0){
                    fnResetearEstado();
                  } else {
                    if (hojas_afectadas >= hojas_muestreadas){
                        $hojas_afectadas.val(hojas_muestreadas);
                        $porcentaje_afectadas.html("100.00%");
                    } else {
                        /*Estado deseado */
                        $porcentaje_afectadas.html(parseFloat(hojas_afectadas/hojas_muestreadas*100).toFixed(2)+"%");
                    }
                  }
                }
            }
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

            if ((muestras_recomendadas >= parseInt(uiRow.numero_muestra_actual)) && (uiRow.muestras_finalizadas == 0)) {
                MUESTRA_ACTUAL = uiRow.numero_muestra_actual;
                MUESTRAS_RECOMENDADAS = muestras_recomendadas;
                HOJAS_MUESTREADAS = uiRow.hojas_muestreadas;
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
            frmRoyaResumenView = new FrmRoyaResumenView(procesarResumen(rs2Array(res.resumen.rows)));
            $resumen.html(frmRoyaResumenView.$el);    
        },
        UIFail = function (firstFail, name) {
            console.log('Fail for: ' + name);
            console.error(firstFail);
        };

    this.consultarDatosInterfaz = function(){
        /*parcela, cultivo (variedad), hectareas, parcela,  muestras por hectareas, muestra actual*/
        var reqObj = {
              UI: servicio_frm.obtenerUIRoya(params.cod_parcela),
              resumen: servicio_frm.obtenerResumenRoya(params.cod_parcela)
            },
            self = this;

        $.whenAll(reqObj)
          .done(UIDone)
          .fail(UIFail);
    };

   var procesarResumen = function(arregloMuestras){
        var porcentaje_afectadas = 0.0,
            totalMuestras = arregloMuestras.length;

        for (var i = totalMuestras - 1; i >= 0; i--) {
            var objMuestra = arregloMuestras[i];
            porcentaje_afectadas += objMuestra.roy_porcentaje_afectadas;
        };

        return {
                cantidad: totalMuestras,
                cantidad_valida:totalMuestras > 0,
                porcentaje_afectadas : parseFloat(porcentaje_afectadas / totalMuestras).toFixed(2)
            };
   };

    var agregarMuestra = function(esFinalizar){   
            var fnConfirm = function(){
                var DOM = $DOM, 
                    objMuestra = {
                        item : MUESTRA_ACTUAL,
                        cod_parcela : params.cod_parcela,
                        roy_hojas: HOJAS_MUESTREADAS,
                        roy_hojas_afectadas: parseInt(DOM.hojas_afectadas.val()),
                        roy_porcentaje_afectadas: parseFloat(DOM.porcentaje_afectadas.html()).toFixed(2),
                        finalizacion : esFinalizar,
                        usuario_registro: DATA_NAV.usuario.cod_usuario
                    };

                $.when( servicio_frm.agregarMuestraRoya(objMuestra)
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

            confirmar("¿Desea "+(esFinalizar ? "finalizar esta parcela" : "guardar esta muestra")+"?", fnConfirm);

            
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

    var reiniciarFormulario = function(){
        var reqObj = {
              UI: servicio_frm.obtenerUIRoya(params.cod_parcela),
              resumen: servicio_frm.obtenerResumenRoya(params.cod_parcela)
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
            $content.off("change","input", __changeInput);
            $content.off("click", "#btn-guardar-muestra", __guardarMuestra);
            $content.off("click", "#btn-finalizar", __finalizarEvaluacion);
        }
        frmRoyaResumenView.destroy();
        frmRoyaResumenView = null;
        $resumen = null;
        $DOM = null;
    };

    this.destroy = function(){
        destroyBase();
        MUESTRAS_RECOMENDADAS = null;
        $content = null;
        HOJAS_MUESTREADAS = null;
        self = null;
    };

    this.initialize();  
};