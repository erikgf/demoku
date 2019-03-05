var ListaFormulariosListView = function () {

    var formularios;

    this.initialize = function() {
        this.$el = $('<div/>');
        this.render();
    };

    this.setFormularios = function(list) {
        formularios = list;
        this.render();
    }

    this.render = function() {
        this.$el.html(this.template(formularios));
        return this;
    };

    this.destroy = function(){
        this.$el = null;
    };

    this.initialize();
};