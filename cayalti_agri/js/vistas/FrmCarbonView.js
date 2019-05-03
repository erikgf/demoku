var FrmCarbonView = function (servicio_frm, params) {
	var self = this,
        $content,
        AREA_TOTAL,
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
                    [{"numero_cepas":"._numero-cepas"},
                     {"numero_latigos":"._numero-latigos"},
                     {"latigos_cepa": "._latigos-cepa"},
                     {"latigos_area": "._latigos-area"},
                    ]);
    };

    var  __changeInput = function(e){
            var classList = this.classList;
            if (classList.contains("_numero-cepas")){
                verificarValor(this, '1');
                return;
            }

            if (classList.contains("_numero-latigos")  ){
                verificarValor(this, '0');
                return;
            }
        },
        __guardarMuestra =  function(e){
            self.finalizarEvaluacion();
        };

    this.setEventos = function(){
        $content.on("change","input", __changeInput);
        $content.on("click", "#btn-guardar-muestra",__guardarMuestra);
    };

     var validarMuestra = function(){
        return true;
    };

    var verificarValor = function(input, valorMinimo){
        var valor = input.value;

        if (valor.length < 1 || valor < 0){
            input.value = valorMinimo;
        } else {
            valor = parseInt(valor);
            input.value = valor;
        }

        calcularValores();
    };

    var calcularValores = function(){
        var DOM = $DOM,
            $numero_cepas = DOM.numero_cepas,
            $numero_latigos = DOM.numero_latigos,
            $latigos_cepa  = DOM.latigos_cepa,
            $latigos_area = DOM.latigos_area,
            numero_cepas = $numero_cepas.val(),
            numero_latigos = $numero_latigos.val(),
            latigos_cepa,
            latigos_area;

        if (numero_cepas <= 0){
            latigos_cepa = "0.00";
        } else {
            latigos_cepa = parseFloat(numero_latigos / numero_cepas).toFixed(2);    
        }
        latigos_area = parseFloat(numero_latigos / AREA_TOTAL).toFixed(2);

        $latigos_cepa.html(latigos_cepa);
        $latigos_area.html(latigos_area);
    };


    var UIDone = function (res) {
            var uiRow = res.UI.rows.item(0),
                registros,
                finalizado = uiRow.muestras_finalizadas > 0;

            AREA_TOTAL  = uiRow.area;
 
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
                registros = res.registros.rows.item(0);
                llenarRegistros(registros.numero_cepas, registros.numero_latigos);
            }

           // llenarRegistros(rs2Array(res.registros.rows), finalizado);
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

    var llenarRegistros = function(numeroCepas, numeroLatigos){
       var  DOM = $DOM,
            $numero_cepas = DOM.numero_cepas
            $numero_latigos = DOM.numero_latigos;

        $numero_cepas.val(numeroCepas).attr("readonly",true);
        $numero_latigos.val(numeroLatigos).attr("readonly",true);

        calcularValores();
    };


    this.finalizarEvaluacion = function(){
        if (validarMuestra()){           
            var fnConfirm  = function(){
                var  DOM = $DOM,
                    $numero_cepas = DOM.numero_cepas
                    $numero_latigos = DOM.numero_latigos,
                    objMuestra = {
                        item : 1,
                        cod_parcela: params.cod_parcela,
                        car_tallos : $numero_cepas.val(),
                        car_tallos_latigo: $numero_latigos.val(),
                        usuario_registro : DATA_NAV.usuario.cod_usuario,
                        finalizacion : true
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
            };

           confirmar("¿Desea finalizar evaluación?", fnConfirm);
        }
    };  

    this.initialize();  
};