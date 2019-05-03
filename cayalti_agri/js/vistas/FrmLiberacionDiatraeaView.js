var FrmLiberacionDiatraeaView = function (servicio_frm, params) {
	var self = this,
        $content,
        LIBERACIONES_TOTAL,
        LIBERACIONES_REALIZADAS,
        LIBERACIONES_PENDIENTES,
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
                    [{"puntos_liberados": "._puntos-liberados"},
                     {"puntos_pendientes":".__puntos-pendientes"}
                    ]);
    }

    var __liberar = function(e){
            e.preventDefault();
            self.liberar();
        },
        __liberarFinalizar = function(e){
            self.liberarFinalizar();
        };

    this.setEventos = function(){
        $content.on("click", "#btn-liberar", __liberar);
        $content.on("click", "#btn-liberar-finalizar", __liberarFinalizar);
    };

    var UIDone = function (res) {
            var uiRow = res.UI.rows.item(0),
                liberaciones_total = uiRow.liberaciones_total,
                liberaciones_realizadas = uiRow.liberaciones_realizadas,
                liberaciones_pendientes = uiRow.liberaciones_total - uiRow.liberaciones_realizadas;
                
            if (liberaciones_pendientes > 0) {
                uiRow.puedo_registrar = true;
            }

            uiRow.liberaciones_pendientes = liberaciones_pendientes;

            LIBERACIONES_TOTAL = parseInt(liberaciones_total);
            LIBERACIONES_REALIZADAS = parseInt(liberaciones_realizadas);
            LIBERACIONES_PENDIENTES = parseInt(liberaciones_pendientes);

            self.$el.html(self.template(uiRow)); 
            $content = self.$el.find(".content");
            self.setDOM();
            self.setEventos();

            liberaciones_total = null;
            liberaciones_realizadas = null;
            liberaciones_pendientes = null;
        },
        UIFail = function (firstFail, name) {
            console.log('Fail for: ' + name);
            console.error(firstFail);
        };

    this.consultarDatosInterfaz = function(){
        /*parcela, cultivo (variedad), hectareas, parcela,  muestras por hectareas, muestra actual*/
        var reqObj = {
              UI: servicio_frm.obtenerUILiberacion(params.cod_parcela)
            },
            self = this;

        $.whenAll(reqObj)
          .done(UIDone)
          .fail(UIFail);
    };


    this.liberar = function(){
        var rpt = false;
        if (LIBERACIONES_PENDIENTES <= 1){
            rpt = true;
        }
        liberarPunto(rpt);
    };  

    this.liberarFinalizar = function(){
        liberarPunto(true);
    };

    var liberarPunto = function(esFinalizar){
        if (validarMuestra()){
            var fnConfirm = function(){
                var DOM = $DOM, 
                    objMuestra = {
                        item: parseInt(LIBERACIONES_REALIZADAS) + 1,
                        cod_parcela : params.cod_parcela,
                        finalizacion : esFinalizar,
                        usuario_registro: DATA_NAV.usuario.cod_usuario
                    };
                
                $.when( servicio_frm.agregarLiberacionPunto(objMuestra)
                        .done(function(r){
                           if (esFinalizar){
                                history.back();
                                alert("Liberación FINALIZADA.");
                            } else {
                                DOM.puntos_liberados.html(++LIBERACIONES_REALIZADAS);
                                DOM.puntos_pendientes.html(--LIBERACIONES_PENDIENTES);
                                alert("Liberación GUARDADA.");
                            }
                        })
                        .fail(function(e){
                            console.error(e);
                        })
                    );
            };

            confirmar("¿Desea "+(esFinalizar ? "finalizar esta liberación" : "guardar esta liberación")+"?",fnConfirm);
        }
    };  

    var destroyBase = function(){
       LIBERACIONES_TOTAL = null;
       LIBERACIONES_REALIZADAS = null;
       LIBERACIONES_PENDIENTES = null;

       if ($content){
            $content.off("click", "#btn-liberar", __liberar);
            $content.off("click", "#btn-liberar-finalizar", __liberarFinalizar);
       }
       $DOM = null; 
    };

    this.destroy = function(){
        destroyBase();
        $content = null;
        this.$el = null; 
        self = null;
    };


    this.initialize();  
};