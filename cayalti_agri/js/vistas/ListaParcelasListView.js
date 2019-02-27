var ListaParcelasListView = function () {

    var parcelas;

    this.initialize = function() {
        this.$el = $('<div/>');
        this.render();
    };

    this.setParcelas = function(list) {
        parcelas = list;
        this.render();
    };

    this.render = function() {
        this.$el.html(this.template(parcelas));
        return this;
    };

    this.destroy = function(){
        this.$el = null;
        campos = null;
    };

    this.initialize();
};