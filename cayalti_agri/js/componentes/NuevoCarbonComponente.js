var NuevoCarbonComponente = function(index, FrmPadre) {    
      var self,
          $DOM,
          vecesRegistro = 0,
          _valores;

      this.initialize = function() {
      	this.$el = $('<div>');       
        self = this;
        _valores = {
            tallos: "",
            tallos_latigos: ""              
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

      this.setValores = function(valores, porcentajeDaño){
        var  DOM = $DOM;        
        DOM.tallos.val(valores.tallos);
        DOM.tallos_latigos.val(valores.tallos_latigos);
        DOM.porcentaje_dañados.html(porcentajeDaño);
        vecesRegistro = 1;
        _valores = valores;
      };

      this.getValores = function(){
        return _valores;
      };

      this.setDOM  = function(){
        $DOM = preDOM2DOM(this.$el, 
                    [{"tallos":"._tallos"},
                     {"tallos_latigos":"._tallos-latigos"},
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
          FrmPadre.quitarRegistro(index);
          self.destroy();
        },__cambiarInput = function(e){
          var classList = this.classList;

          if (classList.contains("_tallos")){
              validarEntrada(this, "tallos");
              return;
          }

          if (classList.contains("_tallos-latigos")){
              validarEntrada(this, "tallos_latigos");
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

        if (nombre_elemento == "tallos_latigos"){

          $DOM.tallos_latigos.removeClass("error");

          if (_valores["tallos"]  == ""){
            $this.value = "";
            _valores[nombre_elemento] = valor;
            return;
          }

          if (valor > _valores["tallos"]){
            valor = _valores["tallos"];
          }
        } else {

          $DOM.tallos.removeClass("error");

          if (_valores["tallos_latigos"] > valor){
            _valores["tallos_latigos"] = valor;
            $DOM.tallos_latigos.val(valor);
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
            tallos_latigo = _valores["tallos_latigos"],
            $porcentaje_dañados = DOM.porcentaje_dañados;

            if (tallos.length <= 0 || tallos_latigo.length <= 0 ){
              $porcentaje_dañados.html("0.0");
              return;
            }

            $porcentaje_dañados.html(parseFloat(tallos_latigo / tallos * 100).toFixed(1));
            vecesRegistro++;
            FrmPadre.grabarRegistro(index,_valores, vecesRegistro == 1 ? "+" : "*");
      };

      this.validar = function(){
        var DOM = $DOM,
            $tallos = $DOM.tallos,
            $tallos_latigos = $DOM.tallos_latigos,
            tallos = _valores["tallos"],
            tallos_latigos = _valores["tallos_latigos"];


        if (tallos.length == ""){
            $DOM.tallos_latigos.focus().addClass("error");
            alert("Necesita registrar número TALLOS.");
            return false;
         } 

         if (tallos_latigos.length == ""){
            $DOM.tallos_latigos.focus().addClass("error");
            alert("Necesita registrar número LÁTIGOS.");
            return false;
         }

         return true;
      };

      this.destroy = function(){
        this.$el.off("click", "._quitar-carbon", __quitarCarbon);
        this.$el.off("change", "input", __cambiarInput);
        this.$el.remove();
        this.$el = null;
        FrmPadre = null;
        self = null;
      };

      this.initialize(index);
  }