var FrmDiatraeaView = function (servicio_frm, params) {
    var self,
	    $content,
        TALLOS_MUESTREADOS,
        MUESTRA_ACTUAL,
        $resumen,
		rs2Array = resultSetToArray,
        frmDiatraeaResumenView,
        _estadios = [0,0,0,0,0,0],
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
	   // this.$el.html(this.template());
        //
       //
        //$content = this.$el.find(".content");
        this.consultarDatosInterfaz();
	    return this;
	};

    this.setDOM = function(){
        $DOM = preDOM2DOM($content, 
                    [{"entrenudos": "._entrenudos"},
                     {"entrenudos_infestados":"._entrenudos-infestados"},
                     {"entrenudos_porcentaje":"._entrenudos-porcentaje"},
                     {"tallos_infestados":"._tallos-infestados"},
                     {"tallos_porcentaje":"._tallos-porcentaje"},
                     {"estadio_1":"#estadio-1"},
                     {"estadio_2":"#estadio-2"},
                     {"estadio_3":"#estadio-3"},
                     {"estadio_4":"#estadio-4"},
                     {"estadio_5":"#estadio-5"},
                     {"estadio_6":"#estadio-6"},
                     {"larvas_total":"._larvas-total"},
                     {"larvas_indice":"._larvas-indice"},
                     {"crisalidas":"._crisalidas"},
                     {"crisalidas_tallo":"._crisalidas-tallo"},
                     {"larvas_parasitadas":"._larvas-parasitadas"},
                     {"larvas_parasitadas_tallo":"._larvas-parasitadas-tallo"},
                     {"billaea_larvas":"._billaea-larvas"},
                     {"billaea_larvas_tallo":"._billaea-larvas-tallo"},
                     {"billaea_pupas":"._billaea-pupas"},
                     {"billaea_pupas_tallo":"._billaea-pupas-tallo"}
                    ]);
        //$entrenudos = $(", $entrenudos_infestados, $entrenudos_porcentaje_infestado
    }

    this.setEventos = function(){
        $content.on("change","input", __inputs);
        $content.on("click", "#btn-guardar-muestra", __guardarMuestra);
        $content.on("click", "#btn-finalizar", __finalizarMuestra);
    };

    var __inputs = function(e){
            var classList = this.classList;
            if (classList.contains("_entrenudos") ||  classList.contains("_entrenudos-infestados")){
                calcularIntensidadDano();
                return;
            }

            if (classList.contains("_tallos-infestados")){
                calcularTallosInfestacion();
                return;
            }

            if (classList.contains("_estadio")){
                calcularTotalLarvas(this);
                return;
            }

            if (classList.contains("_crisalidas")){
                calcularCrisalidas(this);
                return;
            }

            if (classList.contains("_larvas-parasitadas")){
                calcularLarvasParasitadas(this);
                return;
            }

            if (classList.contains("_billaea-larvas")){
                calcularBillaeaLarvas(this);
                return;
            }

            if (classList.contains("_billaea-pupas")){
                calcularBillaeaPupas(this);
                return;
            }
        },
        __guardarMuestra = function(e){
            self.guardaMuestra();
        },
        __finalizarMuestra = function(e){
            self.finalizarEvaluacion();
        };

    var calcularIntensidadDano = function(){
        var DOM = $DOM,
            $entrenudos = DOM.entrenudos,
            $entrenudos_infestados = DOM.entrenudos_infestados,
            $entrenudos_porcentaje = DOM.entrenudos_porcentaje,
            entrenudos_muestreados = $entrenudos.val(),
            entrenudos_infestados = $entrenudos_infestados.val(),
            fnResetearEstado = function(desactivar){
                if (desactivar != undefined){
                  $entrenudos_infestados.attr("disabled",true);
                }
                $entrenudos_infestados.val("0")
                $entrenudos_porcentaje.html("n/a");
            };

        $entrenudos.removeClass("error");

        if (entrenudos_muestreados.length > 0){
            entrenudos_muestreados = parseInt(entrenudos_muestreados);

            if ( entrenudos_muestreados <= 0){
                $entrenudos.val("0");
                fnResetearEstado(true);
            } else {
                if ($entrenudos_infestados.prop("disabled")){
                    $entrenudos_infestados.removeAttr("disabled");
                }

                if (entrenudos_infestados.length < 1 || entrenudos_infestados < 0){
                  fnResetearEstado();
                } else {
                  entrenudos_infestados = parseInt(entrenudos_infestados);
                  if (entrenudos_infestados < 0){
                    fnResetearEstado(true);
                  } else {
                    if (entrenudos_infestados >= entrenudos_muestreados){
                        $entrenudos_infestados.val(entrenudos_muestreados);
                        $entrenudos_porcentaje.html("100.00%");
                    } else {
                        /*Estado deseado */
                        $entrenudos_porcentaje.html(parseFloat(entrenudos_infestados/entrenudos_muestreados*100).toFixed(2)+"%");
                    }
                  }
                }
            }
        } else {
            fnResetearEstado();
        }
    };

    var calcularTallosInfestacion = function(){
        var DOM = $DOM,
            $tallos_infestados = DOM.tallos_infestados,
            $tallos_porcentaje = DOM.tallos_porcentaje,
            tallos_muestreados = TALLOS_MUESTREADOS,
            tallos_infestados = $tallos_infestados.val(),
            fnResetearEstado = function(){
                $tallos_infestados.val("0")
                $tallos_porcentaje.html("0.00%");
            };

                if (tallos_infestados.length < 1 || parseInt(tallos_infestados) == 0){
                  fnResetearEstado();
                } else {
                  if (tallos_infestados <= 0){
                    fnResetearEstado();
                  } else {
                    if (tallos_infestados >= tallos_muestreados){
                        $tallos_infestados.val(tallos_muestreados);
                        $tallos_porcentaje.html("100.00%");
                    } else {
                        /*Estado deseado */
                        $tallos_porcentaje.html(parseFloat(tallos_infestados/tallos_muestreados*100).toFixed(2)+"%");
                    }
                  }
                }
    };

    var calcularTotalLarvas = function(estadioInput){
        var DOM = $DOM,
            valorInput = estadioInput.value,
            n_estadio = parseInt(estadioInput.id.slice(-1)) - 1,
            tallos_muestreados = TALLOS_MUESTREADOS
            $larvas_total = DOM.larvas_total,
            $larvas_indice = DOM.larvas_indice; 

        if (valorInput.length < 1 || valorInput < 1){
            estadioInput.value = "0";
            valorInput = 0;
        }
        _estadios[n_estadio] = valorInput;

        var estadioTotal = 0;

        for (var i = _estadios.length - 1; i >= 0; i--) {
           estadioTotal += parseInt(_estadios[i]);
        };

        $larvas_total.html(estadioTotal);
        $larvas_indice.html(parseFloat(estadioTotal / tallos_muestreados).toFixed(2));
    };

    var calcularIndividuosTallo = function($individuos, $individuos_tallo){
        var individuos = $individuos.val(),
            tallos_muestreados = TALLOS_MUESTREADOS;

        if (individuos.length < 1){
            $individuos.val("0");
            $individuos_tallo.html("0.00");
        } else {
            if (parseInt(individuos) < 1){
                $individuos.val("0");
                individuos = 0;
            }
            $individuos_tallo.html(parseFloat(individuos/tallos_muestreados).toFixed(2));
        }
    };

    var calcularCrisalidas = function(){
        var DOM = $DOM,
            $crisalidas = DOM.crisalidas,        
            $crisalidas_tallo = DOM.crisalidas_tallo;

        calcularIndividuosTallo($crisalidas, $crisalidas_tallo);
    };

    var calcularLarvasParasitadas = function(){
        var DOM = $DOM,
            $larvas_parasitadas = DOM.larvas_parasitadas,        
            $larvas_parasitadas_tallo = DOM.larvas_parasitadas_tallo;

        calcularIndividuosTallo($larvas_parasitadas, $larvas_parasitadas_tallo);
    };

    var calcularBillaeaLarvas = function(){
        var DOM = $DOM,
            $billaea_larvas = DOM.billaea_larvas,        
            $billaea_larvas_tallo = DOM.billaea_larvas_tallo;

        calcularIndividuosTallo($billaea_larvas, $billaea_larvas_tallo);
    };

    var calcularBillaeaPupas = function(){
        var DOM = $DOM,
            $billaea_pupas = DOM.billaea_pupas,        
            $billaea_pupas_tallo = DOM.billaea_pupas_tallo;

        calcularIndividuosTallo($billaea_pupas, $billaea_pupas_tallo);
    };

    var UIDone = function (res) {
            var uiRow = res.UI.rows.item(0);
            TALLOS_MUESTREADOS = uiRow.tallos_muestreados; 
            MUESTRA_ACTUAL = parseInt(uiRow.numero_muestra_actual);
            if ((uiRow.muestras_recomendadas >=  MUESTRA_ACTUAL) && (uiRow.muestras_finalizadas == 0)) {
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
            frmDiatraeaResumenView = new FrmDiatraeaResumenView(procesarResumen(rs2Array(res.resumen.rows)));
            $resumen.html(frmDiatraeaResumenView.$el);       
        },
        UIFail = function (firstFail, name) {
            console.log('Fail for: ' + name);
            console.error(firstFail);
        };

    this.consultarDatosInterfaz = function(){
        /*parcela, cultivo (variedad), hectareas, parcela,  muestras por hectareas, muestra actual*/
        var reqObj = {
              UI: servicio_frm.obtenerUIDiatraea(params.cod_parcela),
              resumen: servicio_frm.obtenerResumenDiatraea(params.cod_parcela)
            };

        $.whenAll(reqObj)
          .done(UIDone)
          .fail(UIFail);
    };

   var procesarResumen = function(arregloMuestras){
        var intensidad_danio = 0.0,
            tallos_infestacion = 0.0,
            estadio_1 = 0.0,
            estadio_2 = 0.0,
            estadio_3 = 0.0,
            estadio_4 = 0.0,
            estadio_5 = 0.0,
            estadio_6 = 0.0,
            larvas_indice = 0.0,
            crisalidas = 0.0,
            larvas_parasitadas = 0.0,
            billaea_larvas = 0.0,
            billaea_pupas = 0.0,
            totalMuestras = arregloMuestras.length;

        for (var i = totalMuestras - 1; i >= 0; i--) {
            var objMuestra = arregloMuestras[i];
            intensidad_danio += objMuestra.dia_entrenudos_infestados / objMuestra.dia_entrenudos;
            tallos_infestacion += objMuestra.dia_tallos_infestados / objMuestra.dia_tallos;
            estadio_1 += objMuestra.dia_larvas_estadio_1;
            estadio_2 += objMuestra.dia_larvas_estadio_2;
            estadio_3 += objMuestra.dia_larvas_estadio_3;
            estadio_4 += objMuestra.dia_larvas_estadio_4;
            estadio_5 += objMuestra.dia_larvas_estadio_5;
            estadio_6 += objMuestra.dia_larvas_estadio_6;

            larvas_indice += objMuestra.dia_larvas_indice;

            crisalidas += objMuestra.dia_crisalidas / objMuestra.dia_tallos;
            larvas_parasitadas += objMuestra.dia_larvas_parasitadas / objMuestra.dia_tallos;
            billaea_pupas += objMuestra.dia_billaea_pupas / objMuestra.dia_tallos;
            billaea_larvas += objMuestra.dia_billaea_larvas / objMuestra.dia_tallos;
        };

        return {
                cantidad: totalMuestras,
                cantidad_valida:totalMuestras > 0,
                intensidad_daño : parseFloat(intensidad_danio / totalMuestras * 100 ).toFixed(2) ,
                tallos_infestacion : parseFloat(tallos_infestacion / totalMuestras * 100).toFixed(2) ,
                estadio_1 : parseFloat(estadio_1 / totalMuestras).toFixed(1),
                estadio_2 : parseFloat(estadio_2 / totalMuestras).toFixed(1),
                estadio_3 : parseFloat(estadio_3 / totalMuestras).toFixed(1),
                estadio_4 : parseFloat(estadio_4 / totalMuestras).toFixed(1),
                estadio_5 : parseFloat(estadio_5 / totalMuestras).toFixed(1),
                estadio_6 : parseFloat(estadio_6 / totalMuestras).toFixed(1),
                larvas_indice: parseFloat(larvas_indice / totalMuestras).toFixed(1),
                crisalidas :  parseFloat(crisalidas / totalMuestras).toFixed(2),
                larvas_parasitadas : parseFloat(larvas_parasitadas / totalMuestras).toFixed(2),
                billaea_pupas :  parseFloat(billaea_pupas / totalMuestras).toFixed(2),
                billaea_larvas :  parseFloat(billaea_larvas / totalMuestras).toFixed(2),
            };
   };

   var validarMuestra = function(){
        /*Qué entra en la vadlidación de muestra?
            entrenudos muestreados > length && mayor 0
                sino focus

            esta seguro?
            send
        */
        var $entrenudos = $DOM.entrenudos,
            entrenudos = $entrenudos.val();

        if (entrenudos.length < 1 ){
            $entrenudos.focus().addClass("error");
            alert("Necesita registrar entrenudos muestreados");
            return false;
        }

        if (entrenudos == 0){
            if (confirm("¿Desea FORZAR CIERRE de este CUARTEL/VÁLVULA?")){
                forzarCierre();
            }
            return false;
        }

        return true;
    };


    var agregarMuestra = function(esFinalizar){        
        if (validarMuestra()){
            if (!confirm("¿Desea "+(esFinalizar ? "finalizar esta parcela" : "guardar esta muestra")+"?")){
                return;
            }

            var DOM = $DOM, 
                objMuestra = {
                    item: MUESTRA_ACTUAL,
                    cod_parcela : params.cod_parcela,
                    dia_entrenudos: parseInt(DOM.entrenudos.val()),
                    dia_entrenudos_infestados: parseInt(DOM.entrenudos_infestados.val()),
                    dia_tallos: parseInt(TALLOS_MUESTREADOS),
                    dia_tallos_infestados: parseInt(DOM.tallos_infestados.val()) ,
                    dia_larvas_estadio_1: parseInt(DOM.estadio_1.val()),
                    dia_larvas_estadio_2: parseInt(DOM.estadio_2.val()),
                    dia_larvas_estadio_3: parseInt(DOM.estadio_3.val()),
                    dia_larvas_estadio_4: parseInt(DOM.estadio_4.val()),
                    dia_larvas_estadio_5: parseInt(DOM.estadio_5.val()),
                    dia_larvas_estadio_6: parseInt(DOM.estadio_6.val()),
                    dia_larvas_indice: parseFloat(DOM.larvas_indice.html()).toFixed(2),
                    dia_crisalidas: parseInt(DOM.crisalidas.val()),
                    dia_larvas_parasitadas: parseInt(DOM.larvas_parasitadas.val()),
                    dia_billaea_larvas: parseInt(DOM.billaea_larvas.val()),
                    dia_billaea_pupas: parseInt(DOM.billaea_pupas.val()) ,
                    finalizacion : esFinalizar,
                    usuario_registro: DATA_NAV.usuario.cod_usuario
                };

            $.when( servicio_frm.agregarMuestraDiatraea(objMuestra)
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
        }
    };

    var reiniciarFormulario = function(){
        var reqObj = {
              UI: servicio_frm.obtenerUIDiatraea(params.cod_parcela),
              resumen: servicio_frm.obtenerResumenDiatraea(params.cod_parcela)
            };

        $.whenAll(reqObj)
          .done(function (res) {
            destroyBase();
            UIDone(res);
            $content[0].scrollTop = 0;
          })
          .fail(UIFail);
    };

    var forzarCierre = function(){
         $.when( servicio_frm.forzarCierreDiatraea(MUESTRA_ACTUAL, params.cod_parcela)
                    .done(function(r){
                        history.back();
                        alert("Evaluación finalizada.");
                    })
                    .fail(function(e){
                        console.error(e);
                    })
                );    

    };

    this.guardaMuestra = function(){
        agregarMuestra(false);
    };  

    this.finalizarEvaluacion = function(){
        agregarMuestra(true);
    };

    var destroyBase = function(){
       TALLOS_MUESTREADOS = null;
       MUESTRA_ACTUAL = null;
       _estadios = [0,0,0,0,0,0]; 

       if ($content){
        $content.off("change", "input", __inputs);
        $content.off("change", "#btn-guardar-muestra", __guardarMuestra);
        $content.off("change", "#btn-finalizar", __finalizarMuestra);
       }
       $DOM = null; 
       $resumen = null;
       frmDiatraeaResumenView.destroy();
       frmDiatraeaResumenView = null;
    };

    this.destroy = function(){
        destroyBase();
        $content = null;
        rs2Array = null ;
        this.$el = null; 
        self = null;
    };

    this.initialize();  
};