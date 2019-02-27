var ListaCamposView = function (servicio, cache) {

	var self = this,
    $content,
		listaCamposListView,
		rs2Array = resultSetToArray;

	this.initialize = function () {
        this.$el = $('<div/>');
        listaCamposListView = new ListaCamposListView(this);
        if (ACTUAL_PAGE != null && (typeof ACTUAL_PAGE.destroy == "function")){
            ACTUAL_PAGE.destroy();
        }
        ACTUAL_PAGE = this;
    };

  this.setScrollTop  = function(){
    CACHE_VIEW.lista_campos.scroll = $content[0].scrollTop;
  };

  this.render = function() {
	    this.$el.html(this.template());
      this.consultarCampos();
	    return this;
	};
    
	this.consultarCampos = function(){		
		var $el = this.$el;
        
		$.when( servicio.consultarCampos()
     		.done( function( resultado ){ 
     			var rows = resultado.rows,
            scrollTop = CACHE_VIEW.lista_campos.scroll;

       			if (rows.length > 0){
              listaCamposListView.setCampos(rs2Array(rows));
              $content = $el.find(".content");
              $content.html(listaCamposListView.$el);
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
    $content = null;
    listaCamposListView.destroy();
    listaCamposListView = null;
    this.$el = null;
  };

  this.initialize();  
}