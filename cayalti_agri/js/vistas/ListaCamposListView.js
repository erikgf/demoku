var ListaCamposListView = function (FrmPadre) {
    var campos;

    this.initialize = function() {
        this.$el = $('<div/>');
        this.render();
        this.setEventos();
    };

    this.setCampos = function(list) {
        campos = list;
        this.render();
    }

    this.render = function() {
        this.$el.html(this.template(campos));
        return this;
    };

    var __click = function(e){
        var cod_campana = this.dataset.id;
        FrmPadre.setScrollTop();
        router.load("lista-parcelas/"+cod_campana);
    };

    this.setEventos = function(){
        this.$el.on("click",".table-view li a", __click);
    };

    this.destroy = function(){
        this.$el.off("click",".table-view li a", __click);
        this.$el = null;
        FrmPadre = null;
        campos = null;
    };

    this.initialize();

};