var NuevoCarbonComponente = function(index, FrmPadre) {    
      var self,
          $DOM,
          _valores;

      this.initialize = function() {
      	this.$el = $('<div>');       
        self = this;
        _valores = {
            tallos: "",
            tallos_latigo: ""              
          };
      };

      this.render = function(_dataRender) {
          this.$el.html(this.template(_dataRender));
          this.setDOM();
          this.setEventos();
          return this;
      };

      this.getDOM = function(){
        return $DOM;
      };

      this.getIndex = function(){
        return index;
      };

      this.getValores = function(){
        return _valores;
      };

      this.setDOM  = function(){
        $DOM = preDOM2DOM(this.$el, 
                    [{"tallos":"._tallos"},
                     {"tallos_latigo":"._tallos-latigo"},
                     {"porcentaje_dañados": "._porcentaje-dañados"}
                    ]);
      };

      this.setEventos = function(){
        this.$el.on("click", "._quitar-carbon", __quitarCarbon);
        this.$el.on("change", "input", __cambiarInput);
        /*
        $content.on("click", "#btn-guardar-muestra", function(e){
            self.guardaMuestra();
        });

        $content.on("click", "#btn-finalizar", function(e){
            self.finalizarEvaluacion();
        });
        */
      };

      var __quitarCarbon = function(e){
        //FrmPadre.recalcular(index);
        FrmPadre.quitarCarbon(index, self);
      };

      var __cambiarInput = function(e){
        var classList = this.classList;

            if (classList.contains("_tallos")){
                validarEntrada(this, "tallos");
                return;
            }

            if (classList.contains("_tallos-latigo")){
                validarEntrada(this, "tallos_latigo");
                return;
            }
      };

      var validarEntrada = function($this, nombre_elemento){
        var valor = $this.value;

        if (valor.length <= 0){
          $this.value = "";
          _valores[nombre_elemento] = valor;
          return;
        }

        if (valor < 0){
          $this.value = 0;
          _valores[nombre_elemento] = valor;
          return;
        }

        if (nombre_elemento == "tallos_latigo"){
          if (_valores["tallos"]  == ""){
            $this.value = "";
            _valores[nombre_elemento] = valor;
            return;
          }

          if (valor > _valores["tallos"]){
            valor = _valores["tallos"];
          }
        }

        valor = parseInt(valor);
        $this.value = valor;

        _valores[nombre_elemento] = valor;       


        calcularPorcentajeDaño();
      };

      var calcularPorcentajeDaño = function(){
        var DOM = $DOM,
            tallos = _valores["tallos"],
            tallos_latigo = valores["tallos_latigo"],
            $porcentaje_dañados = DOM.porcentaje_dañados;

            if (tallos.length <= 0 || tallos_latigo.length <= 0 ){
              $porcentaje_dañados.html("0.00");
              return;
            }

            $porcentaje_dañados.html(parseFloat(tallos / tallos_latigo * 100).toFixed(2));
            FrmPadre.grabarRegistro(index,_valores);
      };

      this.destroy = function(){
        this.$el.off("click", "._quitar-carbon", __quitarCarbon);
        this.$el.off("change", "input", __cambiarInput);
        this.$el.remove();
        this.$el = null;
        FrmPadre = null;
        self = null;
      };

      this.initialize(i);
  }