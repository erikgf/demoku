var FrmCarbonView = function (servicio_frm, params) {
	var $content,
        NUMERO_METROS,
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

    this.setEventos = function(){
        var self = this;

        $content.on("change","input", function(e){
            var classList = this.classList;
            if (classList.contains("_tallos")  ){
                verificarTallos(this);
                return;
            }

            if (classList.contains("_tallos-latigo")  ){
                verificarLatigos(this);
                return;
            }
        });

        $content.on("click", "#btn-guardar-muestra", function(e){
            self.guardaMuestra();
        });
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
                    finalizacion : false
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