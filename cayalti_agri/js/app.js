 var CACHE_VIEW = {
    login: {
        txt_usuario: null           
    },
    inicio:{
    },
    lista_campos: {
    },
    lista_parcelas: {
      
    },
    formularios : {
    }
},
  ACTUAL_PAGE = null,
  router;

var DATA_NAV, DATA_NAV_JSON;

var onDeviceReady = function () {   
    /* ---------------------------------- Local Variables ---------------------------------- */
    DATA_NAV_JSON = localStorage.getItem("DATA_NAV__APPCAYALTI");

    if ( DATA_NAV_JSON != null){
      DATA_NAV = JSON.parse(DATA_NAV_JSON); 
    } else {
      DATA_NAV = {
        acceso: false,
        usuario : null
      };
    }

    var slider = new PageSlider($('body')),
        //blockUI = new BlockUI(),
        db = new DBHandlerClase(),
        servicio = new AgriServicio(),
        servicio_web = new AgriServicioWeb(),
        servicio_frm = new AgriServicioFrm();
    
    servicio_web.initialize();
    servicio_frm.initialize(db);
    servicio.initialize(db).then(function (htmlScriptTemplates) {
      try{
        procesarTemplates(htmlScriptTemplates);

        router.addRoute('', function() {
            slider.slidePage(new LoginView(servicio, CACHE_VIEW.login).render().$el);
        });

          router.addRoute('inicio', function() {
            if (DATA_NAV.acceso){
                slider.slidePage(new InicioView(DATA_NAV.usuario,servicio_web, servicio).render().$el);
            }
          });

          router.addRoute('lista-campos', function() {
            if (DATA_NAV.acceso){
                slider.slidePage(new ListaCamposView(servicio, CACHE_VIEW.lista_campos).render().$el);
            }
          });

          router.addRoute('lista-parcelas/:id', function(id) {
            if (DATA_NAV.acceso){
                slider.slidePage(new ListaParcelasView(servicio, CACHE_VIEW.lista_parcelas, {cod_campana: id}).render().$el);
            }
          });

          router.addRoute('formularios/:id', function(id) {
            if (DATA_NAV.acceso){
                slider.slidePage(new ListaFormulariosView(servicio, CACHE_VIEW.formularios, {cod_parcela: id}).render().$el);
            }
          });

          router.addRoute('frm-biometria/:id', function(id) {
            if (DATA_NAV.acceso){
                slider.slidePage(new FrmBiometriaView(servicio_frm, {cod_parcela: id}).render().$el);
            }
          });

          router.addRoute('frm-diatraea/:id', function(id) {
            if (DATA_NAV.acceso){
                slider.slidePage(new FrmDiatraeaView(servicio_frm, {cod_parcela: id}).render().$el);
            }
          });

          router.addRoute('frm-roya/:id', function(id) {
            if (DATA_NAV.acceso){
                slider.slidePage(new FrmRoyaView(servicio_frm, {cod_parcela: id}).render().$el);
            }
          });

          router.addRoute('frm-carbon/:id', function(id) {
            if (DATA_NAV.acceso){
                slider.slidePage(new FrmCarbonView(servicio_frm, {cod_parcela: id}).render().$el);
            }
          });

          router.addRoute('frm-elasmopalpus/:id', function(id) {
            if (DATA_NAV.acceso){
                slider.slidePage(new FrmElasmopalpusView(servicio_frm, {cod_parcela: id}).render().$el);
            }
          });

          router.addRoute('frm-metamasius/:id', function(id) {
            if (DATA_NAV.acceso){
                slider.slidePage(new FrmMetamasiusView(servicio_frm, {cod_parcela: id}).render().$el);
            }
          });

          router.addRoute('frm-liberacion-d/:id', function(id) {
            if (DATA_NAV.acceso){
                slider.slidePage(new FrmLiberacionDiatraeaView(servicio_frm, {cod_parcela: id}).render().$el);
            }
          });


        if (DATA_NAV.acceso){
          router.load("inicio");
        } else {
          router.load("");
        }

        router.start();

      }catch(e){
        console.error(e)
      };
        console.log("Service initialized");
    });

    function procesarTemplates(htmlScriptTemplates){
        $("body").prepend(htmlScriptTemplates);


        var scripts = document.getElementsByTagName('script');

        for(var i = 0; i < scripts.length; i++) {
            var $el = scripts[i], id = $el.id;
            if ($el.type.toLowerCase() == "text/template"){
              console.log(id.slice(0,-4));
                window[id.slice(0,-4)].prototype.template = Handlebars.compile(document.getElementById(id).innerHTML);
            }
        }
    };
    
    FastClick.attach(document.body);
};

(function(){
    var app = document.URL.indexOf( 'http://' ) === -1 && document.URL.indexOf( 'https://' ) === -1;
    if ( app ) {
      document.addEventListener("deviceready", onDeviceReady, false);
    } else {
      onDeviceReady();  // Web page
    } 
    setFX(app);
}());

    /*
    var app = document.URL.indexOf( 'http://' ) === -1 && document.URL.indexOf( 'https://' ) === -1;
    if ( app ) {
      document.addEventListener("deviceready", onDeviceReady, false);
    } else {
      onDeviceReady();  // Web page
    } 
    */

function cerrarSesion(){
  localStorage.removeItem("DATA_NAV__APPCAYALTI");
  DATA_NAV = {
    acceso: false,
    usuario : null
  };

  location.href = "#";
 // router.load("inicio");
};
