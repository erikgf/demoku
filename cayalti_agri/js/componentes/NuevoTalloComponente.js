var NuevoTalloComponente = function(i, FrmPadre) {    
      var self,
          index,
          $DOM,
          _valores;

      this.initialize = function() {
        index = i;
        frmPadre = FrmPadre,
      	this.$el = $('<div>');       
        self = this;
        _valores = {
            largo: "",
            diametro: "",
            entrenudos : ""          
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
                    [{"largo":"._largo"},
                     {"diametro":"._diametro"},
                     {"entrenudos": "._entrenudos"},
                     {"largo_nudos": "._largo-nudos"}
                    ]);
      };

      this.setEventos = function(){
        this.$el.on("click", "._quitar-entrenudo", __quitarTallo);
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

      var __quitarTallo = function(e){
        FrmPadre.recalcular(index);
        self.destroy();
      };

      var __cambiarInput = function(e){
        var classList = this.classList;

            if (classList.contains("_largo")){
                validarEntrada(this, "largo");
           //     _valores.largo = this.value;
                calcularLargo();
                return;
            }

            if (classList.contains("_diametro")){
                validarEntrada(this, "diametro");
             //   _valores.diametro = this.value;
                return;
            }

            if (classList.contains("_entrenudos")){
                validarEntrada(this, "entrenudos",false);
                calcularLargo();
                return;
            }
      };

      var validarEntrada = function($this, nombre_elemento, esDecimal){
        var valor = $this.value;

        if (esDecimal == undefined){
          esDecimal = true;
        }

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

        if (!esDecimal){
          valor = parseInt(valor);
          $this.value = valor;
        }

        _valores[nombre_elemento] = valor;
        FrmPadre.recalcular();
      };

      var calcularLargo = function(){
        var DOM = $DOM,
            largo = DOM.largo.val(),
            entrenudos = DOM.entrenudos.val(),
            $largo_nudos = DOM.largo_nudos;

            if (largo.length <= 0 || entrenudos.length <= 0 ){
              $largo_nudos.html("0.00");
              return;
            }

            $largo_nudos.html(parseFloat(largo / entrenudos).toFixed(2));
      };

      this.destroy = function(){
        this.$el.off("click", "._quitar-entrenudo", __quitarTallo);
        this.$el.off("change", "input", __cambiarInput);
        this.$el.remove();
        this.$el = null;
        FrmPadre = null;
        self = null;
      };

      this.initialize(i);
  }