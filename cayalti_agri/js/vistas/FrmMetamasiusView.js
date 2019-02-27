var FrmMetamasiusView = function (servicio_frm, params) {
	var $content,
        TALLOS_EVALUADOS,
        ENTRENUDOS_EVALUADOS,
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
                    [{"tallos_evaluados": "._tallos-evaluados"},
                     {"tallos_dañados":"._tallos-dañados"},
                     {"tallos_dañados_porcentaje":"._tallos-dañados-porcentaje"},
                     {"entrenudos_evaluados": "._entrenudos-evaluados"},
                     {"entrenudos_dañados":"._entrenudos-dañados"},
                     {"entrenudos_dañados_porcentaje":"._entrenudos-dañados-porcentaje"},
                    ]);
        //$entrenudos = $(", $entrenudos_infestados, $entrenudos_porcentaje_infestado
    }

    this.setEventos = function(){
        var self = this;

        $content.off("change").on("change","input", function(e){
            var classList = this.classList;
            if (classList.contains("_tallos-dañados")){
                calcularInfestacion("tallos");
                return;
            }

            if (classList.contains("_entrenudos-dañados")){
                calcularInfestacion("entrenudos");
                return;
            }

        });

        $content.off("click").on("click", "#btn-guardar", function(e){
            self.guardar();
        });
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

    this.consultarDatosInterfaz = function(){
        /*parcela, cultivo (variedad), hectareas, parcela,  muestras por hectareas, muestra actual*/
        var reqObj = {
              UI: servicio_frm.obtenerUIMetamasius(params.cod_parcela)
            },
            self = this;

        $.whenAll(reqObj)
          .done(function (res) {
            var uiRow = res.UI.rows.item(0);
            self.$el.html(self.template(uiRow)); 
                $content = self.$el.find(".content");
                self.setDOM();
                self.setEventos();
          })
          .fail(function (firstFail, name) {
            console.log('Fail for: ' + name);
            console.error(firstFail);
          });
    };

   var validarMuestra = function(){
        return true;
    };

    this.guardar = function(){
        if (validarMuestra()){
            if (!confirm("¿Desea guardar?")){
                return;
            }

            var DOM = $DOM, 
                objMuestra = {
                    cod_parcela : params.cod_parcela,
                    met_tallos_evaluados: parseInt(DOM.tallos_evaluados.val()),
                    met_tallos_danados: parseInt(DOM.tallos_dañados.val()),
                    met_entrenudos_evaluados: parseInt(DOM.entrenudos_evaluados.val()),
                    met_entrenudos_danados : parseInt(DOM.entrenudos_dañados.val()),
                    finalizacion : false,
                    usuario_registro: DATA_NAV.usuario.cod_usuario
                };
            
            $.when( servicio_frm.agregarMuestraMetamasius(objMuestra)
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