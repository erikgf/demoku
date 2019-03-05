var FrmMetamasiusResumenView = function(datos) {
      this.initialize = function() {
          this.$el = $('<div/>');
          this.render();
      };

      this.render = function() {
          this.$el.html(this.template(datos));
          return this;
      };

      this.destroy = function(){
        this.$el = null;
      };

      this.initialize(); 
  };