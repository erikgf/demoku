var ListaFormulariosView = function (servicio, cache, params) {
	var listaFormulariosListView,
        $content,
		rs2Array = resultSetToArray;

	this.initialize = function () {
        this.$el = $('<div/>');
        listaFormulariosListView = new ListaFormulariosListView();
        
        if (ACTUAL_PAGE != null && (typeof ACTUAL_PAGE.destroy == "function")){
            ACTUAL_PAGE.destroy();
        }
        ACTUAL_PAGE = this;
    };
 
    this.render = function() {
	    this.$el.html(this.template());
      $content = this.$el.find(".content");
      this.consultarFormularios();
      this.consultarNombreParcela();
	    return this;
	};

  this.consultarNombreParcela = function(){   
    var self = this;
        
    $.when( servicio.consultarNombreParcela(params.cod_parcela)
        .done( function( resultado ){ 
          var rows = resultado.rows;
          if (rows.length > 0){
              self.$el.find("h1").html(rows.item(0).rotulo_parcela);
          }
          })
            .fail(function(e){
                console.error(e);    
            })
        ); 
        //EndWhen
  };

	this.consultarFormularios = function(){		
		var self = this;
        
		$.when( servicio.consultarFormularios(params.cod_parcela)
     		.done( function( resultado ){ 
     			var rows = resultado.rows;
     			if (rows.length > 0){
              listaFormulariosListView.setFormularios({formularios: rs2Array(rows), cod_parcela : params.cod_parcela});
              $content.html(listaFormulariosListView.$el);
     			}
      		})
          .fail(function(e){
              console.error(e);    
          })
      	); 
      	//EndWhen
	};

  this.destroy = function(){
    listaFormulariosListView.destroy();
    listaFormulariosListView = null;
    $content = null;
    this.$el = null;
  };

  this.initialize();  
};