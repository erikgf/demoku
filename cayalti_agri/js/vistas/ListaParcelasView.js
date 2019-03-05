var ListaParcelasView = function (servicio, cache, params) {
	var self, 
        listaParcelasListView,
        tipoRiego,
        codCampana,
        $filtro,
        $content,
        $nivelUno,
        $nivelDos,
		rs2Array = resultSetToArray;

    var htmlDistribuciones = function(tipo_riego, data_nivel_1){
        var html = "";
        //TR = 0 : JC, 1 : MTV
        if (tipo_riego == 1){
            html += '<div style="width:50%">';
            html += '<small>Modulo</small>'
            html += '<select id="select-uno" style="padding: 7.5px;">';
                html += '<option value="0">Todos</option>';
            for (var i = 0; i < data_nivel_1.length; i++) {
                html += '<option value="'+data_nivel_1[i].n1+'">'+data_nivel_1[i].n1+'</option>';
            };
            html += '</select>';
            html += '</div>';

            html += '<div style="width:50%">';
            html += '<small>Turno</small>'
            html += '<select id="select-dos" style="padding: 7.5px;">';
                html += '<option value="0">Todos</option>';
            html += '</select>';
            html += '</div>';
        } else {
            html += '<div style="width:50%">';
            html += '<small>Jirón</small>'
            html += '<select id="select-uno" style="padding: 7.5px;">';
                html += '<option value="0">Todos</option>';
            for (var i = 0; i < data_nivel_1.length; i++) {
                html += '<option value="'+data_nivel_1[i].n1+'">'+data_nivel_1[i].n1+'</option>';
            };
            html += '</select>';
            html += '</div>';
        }
        return html;
    };

    var htmlNivelDos = function(data_turnos){
        var html = "";
        //tipo_riego ==> 1 MTV
            html += '<option value="0">Todos</option>';
        for (var i = 0; i < data_turnos.length; i++) {
            html += '<option value="'+data_turnos[i].n2+'">'+data_turnos[i].n2+'</option>';
        };
        return html;
    };


	this.initialize = function () {
        this.$el = $('<div/>');
        cod_campana  = params.cod_campana;
        listaParcelasListView = new ListaParcelasListView(this);
        if (ACTUAL_PAGE != null && (typeof ACTUAL_PAGE.destroy == "function")){
            ACTUAL_PAGE.destroy();
        }
        ACTUAL_PAGE = this;
        self = this;
    };

    this.setScrollTop  = function(){
        CACHE_VIEW.lista_parcelas.scroll = $content[0].scrollTop;
    };
 
    this.render = function() {
	    this.$el.html(this.template());

        $filtro = this.$el.find(".bar-header-secondary");
        $content = this.$el.find(".content");

        this.setEventos();
        this.consultarTipoRiego();
	    return this;
	};

    var __changeSelect = function(e){
            var valor;
            e.preventDefault();
            valor = this.value;
            if (this.id == "select-uno" && tipoRiego == "1") {
               self.consultarNivelDos(valor);
            }
            self.consultarParcelas();
        },
        __click = function(e){
            var cod_parcela = this.dataset.id;
            self.setScrollTop();
            router.load("formularios/"+cod_parcela);
        };

    this.setEventos = function(){
        $filtro.on("change","select", __changeSelect); 
        $content.on("click",".table-view li", __click);
    };

    this.consultarTipoRiego = function(){
        var self = this;
        $.when( servicio.consultarTipoRiego(params.cod_campana)
                .done(function(resultado){
                    var rows = resultado.rows;
                    if (rows.length > 0){
                        tipoRiego = rows.item(0).tipo_riego;
                        self.$el.find("h1").html(rows.item(0).nombre_campo);
                        self.consultarNivelUno();
                    }   
                })
                .fail(function(e){console.error(e);})
        );
    };

    this.consultarNivelUno = function(){
        var self = this;

        $.when( servicio.consultarNivelUno(params.cod_campana)
                .done(function(resultado){
                    $filtro.html(htmlDistribuciones(tipoRiego, rs2Array(resultado.rows)));
                    $nivelUno = $filtro.find("#select-uno");
                    if (tipoRiego == 1){
                        $nivelDos = $filtro.find("#select-dos");
                    }
                    self.consultarParcelas();
                })
                .fail(function(e){console.error(e);})
        );
    };

    this.consultarNivelDos = function(numero_modulo){
        var self = this;

        if (numero_modulo == "0"){
            $nivelDos.html(htmlNivelDos([]));
            return;
        }

        $.when( servicio.consultarNivelDos(params.cod_campana, numero_modulo)
                .done(function(resultado){
                     $nivelDos.html(htmlNivelDos(rs2Array(resultado.rows)));
                })
                .fail(function(e){console.error(e);})
        );
    };
    
	this.consultarParcelas = function(){		
		var self = this,
            scrollTop = CACHE_VIEW.lista_parcelas.scroll;
        
		$.when( servicio.consultarParcelas({
                    codCampana: params.cod_campana,
                    tipoRiego : tipoRiego,
                    nivelUno: $nivelUno.val(),
                    nivelDos : $nivelDos ? $nivelDos.val() : null
                })
     		.done( function( resultado ){ 
     			var rows = resultado.rows;
     			if (rows.length > 0){
                    listaParcelasListView.setParcelas(rs2Array(rows));
                    $content.html(listaParcelasListView.$el);
                    $content[0].scrollTop = scrollTop ? scrollTop : 0;
     			}
      		})
            .fail(function(e){
                console.error(e);    
            })
      	); 
      	//EndWhen
	};

    this.destroy = function(){
        $filtro.off("change","select", __changeSelect); 
        $filtro = null;
        $content.off("click",".table-view li", __click);
        $content = null;
        $nivelUno = null;
        $nivelDos = null;

        listaParcelasListView.destroy();
        listaParcelasListView = null;
        this.$el = null;
      };

    this.initialize();  
}