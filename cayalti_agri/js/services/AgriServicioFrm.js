var AgriServicioFrm = function() {
	var _db;

    this.initialize = function(db) {
        var deferred = $.Deferred();        
        _db = db;
        deferred.resolve();
        return deferred.promise();
    };

    this.obtenerUIBiometria = function(codParcela) {
        //MTV = GOTEO, JC = GRAVEDAD
        return _db.selectData("SELECT (CASE c.tipo_riego WHEN '1' THEN 'M'||p.numero_nivel_1||'-T'||p.numero_nivel_2||'-V'||p.numero_nivel_3||' - '||c.nombre_campo  ELSE 'J'||p.numero_nivel_1||'-C'||p.numero_nivel_3||' - '||c.nombre_campo END) as rotulo_parcela, "+
                    " p.variedad as cultivo, "+
                    " 300 as dias_campana, "+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = (CASE c.tipo_riego WHEN '1' THEN 'factor_goteo_biometria' ELSE 'factor_gravedad_biometria' END)) as factor, "+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'tasa_crecimiento_base_biometria') as tasa_crecimiento, "+
                    " 'ETAPA DE BROTACIÓN' as ultima_etapa, "+
                    " 1 as cod_ultima_etapa," +
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'biometria_numero_metros') as numero_metros, "+
                    " (SELECT COUNT(id) + 1 FROM frm WHERE cod_parcela = p.cod_parcela AND cod_formulario = 1) as numero_muestra_actual"+
                    " FROM campo c, parcela p WHERE c.cod_campana = p.cod_campana"+
                    " AND p.cod_parcela = ?",
                    [codParcela]);
    };

    this.agregarMuestraBiometria = function(objMuestra){
        objMuestra.cod_formulario = 1;
        return _db.insertarDatos("frm",  
                                    ["cod_parcela", "bio_volumen_promedio","bio_largo_promedio","bio_crecimiento_promedio",
                                        "bio_etapa_fenologica","bio_data_entrenudos",
                                        "bio_volumen_promedio","bio_largo_promedio","bio_crecimiento_promedio",
                                        "bio_ml_metros","bio_ml_tallos","bio_ml_tallos_metros",
                                        "bio_pt_pesos","bio_pt_tallos","bio_pt_peso_tallos","bio_pt_toneladas",
                                        "usuario_registro","cod_formulario"],
                                        [objMuestra]);
    };
    /*
    this.obtenerUIDiatraea = function(codParcela) {
        return _db.selectData("SELECT (CASE c.tipo_riego WHEN '1' THEN 'M'||p.numero_nivel_1||'-T'||p.numero_nivel_2||'-V'||p.numero_nivel_3||' - '||c.nombre_campo  ELSE 'J'||p.numero_nivel_1||'-C'||p.numero_nivel_3||' - '||c.nombre_campo END) as rotulo_parcela, "+
                    " p.area, p.variedad as cultivo, "+
                    " (SELECT round((CAST(valor AS NUMERIC) * p.area), 0) FROM _variables_ WHERE nombre_variable = 'muestrasxarea_diatraea') as muestras_recomendadas, "+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'tallos_diatraea') as tallos_muestreados,"+
                    " (SELECT COUNT(id) + 1 FROM frm_diatraea WHERE cod_parcela = p.cod_parcela) as numero_muestra_actual,"+
                    " (SELECT COUNT(id) FROM frm_diatraea WHERE cod_parcela = p.cod_parcela AND finalizacion = 'true') as muestras_finalizadas"+
                    " FROM campo c, parcela p WHERE c.cod_campana = p.cod_campana"+
                    " AND p.cod_parcela = ?",
                    [codParcela]);
    };
    */
    this.obtenerUIDiatraea = function(codParcela) {
        return _db.selectData("SELECT (CASE c.tipo_riego WHEN '1' THEN 'M'||p.numero_nivel_1||'-T'||p.numero_nivel_2||'-V'||p.numero_nivel_3||' - '||c.nombre_campo  ELSE 'J'||p.numero_nivel_1||'-C'||p.numero_nivel_3||' - '||c.nombre_campo END) as rotulo_parcela, "+
                    " p.area, p.variedad as cultivo, "+
                    " (SELECT round((CAST(valor AS NUMERIC) * p.area), 0) FROM _variables_ WHERE nombre_variable = 'muestrasxarea_diatraea') as muestras_recomendadas, "+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'tallos_diatraea') as tallos_muestreados,"+
                    " (SELECT COUNT(id) + 1 FROM frm WHERE cod_parcela = p.cod_parcela AND cod_formulario = 2) as numero_muestra_actual,"+
                    " (SELECT COUNT(id) FROM frm WHERE cod_parcela = p.cod_parcela AND finalizacion = 'true' AND cod_formulario = 2) as muestras_finalizadas"+
                    " FROM campo c, parcela p WHERE c.cod_campana = p.cod_campana"+
                    " AND p.cod_parcela = ?",
                    [codParcela]);
    };
    /*
    this.obtenerResumenDiatraea = function(codParcela){
        return _db.selectData("SELECT entrenudos, entrenudos_infestados, tallos, tallos_infestados, larvas_estadio_1, larvas_estadio_2"+
                    " , larvas_estadio_3, larvas_estadio_4, larvas_estadio_5, larvas_estadio_6, larvas_indice, crisalidas, "+
                    " larvas_parasitadas, billaea_larvas, billaea_pupas FROM frm_diatraea WHERE cod_parcela = ?",[codParcela]);
    };
    */
    this.obtenerResumenDiatraea = function(codParcela){
        return _db.selectData("SELECT dia_entrenudos, dia_entrenudos_infestados, dia_tallos, dia_tallos_infestados, dia_larvas_estadio_1, dia_larvas_estadio_2"+
                    " , dia_larvas_estadio_3, dia_larvas_estadio_4, dia_larvas_estadio_5, dia_larvas_estadio_6, dia_larvas_indice, dia_crisalidas, "+
                    " dia_larvas_parasitadas, dia_billaea_larvas, dia_billaea_pupas FROM frm WHERE cod_formulario = 2 AND cod_parcela = ?",[codParcela]);
    };


    /*
    this.agregarMuestraDiatraea = function(objMuestra){
        return _db.insertarDatos("frm_diatraea",  
                                    ["cod_parcela", "entrenudos","entrenudos_infestados","tallos","tallos_infestados",
                                        "larvas_estadio_1", "larvas_estadio_2","larvas_estadio_3","larvas_estadio_4","larvas_estadio_5","larvas_estadio_6","larvas_indice",
                                        "crisalidas", "larvas_parasitadas","billaea_larvas","billaea_pupas","finalizacion","usuario_registro"
                                        ],
                                        [objMuestra]);
    };
    */
    this.agregarMuestraDiatraea = function(objMuestra){
        objMuestra.cod_formulario = 2;
        return _db.insertarDatos("frm",  
                                    ["cod_parcela", "dia_entrenudos","dia_entrenudos_infestados","dia_tallos","dia_tallos_infestados",
                                        "dia_larvas_estadio_1", "dia_larvas_estadio_2","dia_larvas_estadio_3","dia_larvas_estadio_4","dia_larvas_estadio_5","dia_larvas_estadio_6","dia_larvas_indice",
                                        "dia_crisalidas", "dia_larvas_parasitadas","dia_billaea_larvas","dia_billaea_pupas","finalizacion","usuario_registro","cod_formulario"
                                        ],
                                        [objMuestra]);
    };
    /*
    this.obtenerUIRoya = function(codParcela) {
        return _db.selectData("SELECT (CASE c.tipo_riego WHEN '1' THEN 'M'||p.numero_nivel_1||'-T'||p.numero_nivel_2||'-V'||p.numero_nivel_3||' - '||c.nombre_campo  ELSE 'J'||p.numero_nivel_1||'-C'||p.numero_nivel_3||' - '||c.nombre_campo END) as rotulo_parcela, "+
                    " p.area, p.variedad as cultivo, "+
                    " (SELECT round((CAST(valor AS NUMERIC) * p.area), 0) FROM _variables_ WHERE nombre_variable = 'muestrasxarea_roya') as muestras_recomendadas, "+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'hojas_roya') as hojas_muestreadas,"+
                    " (SELECT COUNT(id) + 1 FROM frm_roya WHERE cod_parcela = p.cod_parcela) as numero_muestra_actual,"+
                    " (SELECT COUNT(id) FROM frm_roya WHERE cod_parcela = p.cod_parcela AND finalizacion = 'true') as muestras_finalizadas"+
                    " FROM campo c, parcela p WHERE c.cod_campana = p.cod_campana"+
                    " AND p.cod_parcela = ?",
                    [codParcela]);
    };
    */
    this.obtenerUIRoya = function(codParcela) {
        return _db.selectData("SELECT (CASE c.tipo_riego WHEN '1' THEN 'M'||p.numero_nivel_1||'-T'||p.numero_nivel_2||'-V'||p.numero_nivel_3||' - '||c.nombre_campo  ELSE 'J'||p.numero_nivel_1||'-C'||p.numero_nivel_3||' - '||c.nombre_campo END) as rotulo_parcela, "+
                    " p.area, p.variedad as cultivo, "+
                    " (SELECT round((CAST(valor AS NUMERIC) * p.area), 0) FROM _variables_ WHERE nombre_variable = 'muestrasxarea_roya') as muestras_recomendadas, "+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'hojas_roya') as hojas_muestreadas,"+
                    " (SELECT COUNT(id) + 1 FROM frm WHERE cod_parcela = p.cod_parcela AND cod_formulario = 6) as numero_muestra_actual,"+
                    " (SELECT COUNT(id) FROM frm WHERE cod_parcela = p.cod_parcela AND finalizacion = 'true' AND cod_formulario = 6) as muestras_finalizadas"+
                    " FROM campo c, parcela p WHERE c.cod_campana = p.cod_campana"+
                    " AND p.cod_parcela = ?",
                    [codParcela]);
    };
    /*
    this.obtenerResumenRoya = function(codParcela){
        return _db.selectData("SELECT hojas, hojas_afectadas, porcentaje_afectadas FROM frm_roya WHERE cod_parcela = ?",[codParcela]);
    };

    this.agregarMuestraRoya = function(objMuestra){
        return _db.insertarDatos("frm_roya",  
                                    ["cod_parcela", "hojas","hojas_afectadas","porcentaje_afectadas","finalizacion","usuario_registro"],
                                        [objMuestra]);
    };
    */
    this.obtenerResumenRoya = function(codParcela){
        return _db.selectData("SELECT roy_hojas, roy_hojas_afectadas, roy_porcentaje_afectadas FROM frm WHERE cod_formulario = 6 AND cod_parcela = ?",[codParcela]);
    };

    this.agregarMuestraRoya = function(objMuestra){
        objMuestra.cod_formulario = 6;
        return _db.insertarDatos("frm",  
                                    ["cod_parcela", "roy_hojas","roy_hojas_afectadas","roy_porcentaje_afectadas","finalizacion","usuario_registro","cod_formulario"],
                                        [objMuestra]);
    };

    /*

    this.obtenerUICarbon = function(codParcela) {
        return _db.selectData("SELECT (CASE c.tipo_riego WHEN '1' THEN 'M'||p.numero_nivel_1||'-T'||p.numero_nivel_2||'-V'||p.numero_nivel_3||' - '||c.nombre_campo  ELSE 'J'||p.numero_nivel_1||'-C'||p.numero_nivel_3||' - '||c.nombre_campo END) as rotulo_parcela, "+
                    " p.area, p.variedad as cultivo, "+
                    " (SELECT round((CAST(valor AS NUMERIC) * p.area), 0) FROM _variables_ WHERE nombre_variable = 'muestrasxarea_carbon') as muestras_recomendadas, "+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'n_metros_carbon') as n_metros,"+
                    " (SELECT COUNT(id) + 1 FROM frm_carbon WHERE cod_parcela = p.cod_parcela) as numero_muestra_actual,"+
                    " (SELECT COUNT(id) FROM frm_carbon WHERE cod_parcela = p.cod_parcela AND finalizacion = 'true') as muestras_finalizadas"+
                    " FROM campo c, parcela p WHERE c.cod_campana = p.cod_campana"+
                    " AND p.cod_parcela = ?",
                    [codParcela]);
    };

    this.agregarMuestraCarbon = function(objMuestra){
        return _db.insertarDatos("frm_carbon",  
                                    ["cod_parcela", "n_metros","tallos","tallos_latigo","finalizacion","usuario_registro"],
                                        [objMuestra]);
    };
    */
    this.obtenerUICarbon = function(codParcela) {
        return _db.selectData("SELECT (CASE c.tipo_riego WHEN '1' THEN 'M'||p.numero_nivel_1||'-T'||p.numero_nivel_2||'-V'||p.numero_nivel_3||' - '||c.nombre_campo  ELSE 'J'||p.numero_nivel_1||'-C'||p.numero_nivel_3||' - '||c.nombre_campo END) as rotulo_parcela, "+
                    " p.area, p.variedad as cultivo, "+
                    " (SELECT round((CAST(valor AS NUMERIC) * p.area), 0) FROM _variables_ WHERE nombre_variable = 'muestrasxarea_carbon') as muestras_recomendadas, "+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'n_metros_carbon') as n_metros,"+
                    " (SELECT COUNT(id) + 1 FROM frm WHERE cod_parcela = p.cod_parcela AND cod_formulario = 4) as numero_muestra_actual,"+
                    " (SELECT COUNT(id) FROM frm WHERE cod_parcela = p.cod_parcela AND finalizacion = 'true' AND cod_formulario = 4) as muestras_finalizadas"+
                    " FROM campo c, parcela p WHERE c.cod_campana = p.cod_campana"+
                    " AND p.cod_parcela = ?",
                    [codParcela]);
    };

    this.agregarMuestraCarbon = function(objMuestra){
        objMuestra.cod_formulario = 4;
        return _db.insertarDatos("frm",  
                                    ["cod_parcela", "car_n_metros","car_tallos","car_tallos_latigo","finalizacion","usuario_registro","cod_formulario"],
                                        [objMuestra]);
    };

    /*
    this.obtenerUIElasmopalpus = function(codParcela) {
        return _db.selectData("SELECT (CASE c.tipo_riego WHEN '1' THEN 'M'||p.numero_nivel_1||'-T'||p.numero_nivel_2||'-V'||p.numero_nivel_3||' - '||c.nombre_campo  ELSE 'J'||p.numero_nivel_1||'-C'||p.numero_nivel_3||' - '||c.nombre_campo END) as rotulo_parcela, "+
                    " p.area, p.variedad as cultivo, "+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'muestras_elasmopalpus') as muestras_recomendadas, "+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'area_muestreada_elasmopalpus') as area_muestreada_base,"+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'tallos_metro_elasmopalpus') as tallos_metro_base,"+
                    " (SELECT COUNT(id) + 1 FROM frm_elasmopalpus WHERE cod_parcela = p.cod_parcela) as numero_muestra_actual,"+
                    " (SELECT COUNT(id) FROM frm_elasmopalpus WHERE cod_parcela = p.cod_parcela AND finalizacion = 'true') as muestras_finalizadas"+
                    " FROM campo c, parcela p WHERE c.cod_campana = p.cod_campana"+
                    " AND p.cod_parcela = ?",
                    [codParcela]);
    };

    this.agregarMuestraElasmopalpus = function(objMuestra){
        return _db.insertarDatos("frm_elasmopalpus",  
                                    ["cod_parcela", "area_muestreada","tallos_metro","tallos_infectados","larvas","pupas","larvas_muertas","finalizacion","usuario_registro"],
                                        [objMuestra]);
    };
    */

    this.obtenerUIElasmopalpus = function(codParcela) {
        return _db.selectData("SELECT (CASE c.tipo_riego WHEN '1' THEN 'M'||p.numero_nivel_1||'-T'||p.numero_nivel_2||'-V'||p.numero_nivel_3||' - '||c.nombre_campo  ELSE 'J'||p.numero_nivel_1||'-C'||p.numero_nivel_3||' - '||c.nombre_campo END) as rotulo_parcela, "+
                    " p.area, p.variedad as cultivo, "+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'muestras_elasmopalpus') as muestras_recomendadas, "+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'area_muestreada_elasmopalpus') as area_muestreada_base,"+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'tallos_metro_elasmopalpus') as tallos_metro_base,"+
                    " (SELECT COUNT(id) + 1 FROM frm WHERE cod_parcela = p.cod_parcela AND cod_formulario = 3) as numero_muestra_actual,"+
                    " (SELECT COUNT(id) FROM frm WHERE cod_parcela = p.cod_parcela AND finalizacion = 'true' AND cod_formulario = 3) as muestras_finalizadas"+
                    " FROM campo c, parcela p WHERE c.cod_campana = p.cod_campana"+
                    " AND p.cod_parcela = ?",
                    [codParcela]);
    };

    this.agregarMuestraElasmopalpus = function(objMuestra){
        objMuestra.cod_formulario = 3;
        return _db.insertarDatos("frm",  
                                    ["cod_parcela", "ela_area_muestreada","ela_tallos_metro","ela_tallos_infectados","ela_larvas","ela_pupas","ela_larvas_muertas","finalizacion","usuario_registro","cod_formulario"],
                                        [objMuestra]);
    };
    /*
    this.obtenerUIMetamasius = function(codParcela) {
        return _db.selectData("SELECT (CASE c.tipo_riego WHEN '1' THEN 'M'||p.numero_nivel_1||'-T'||p.numero_nivel_2||'-V'||p.numero_nivel_3||' - '||c.nombre_campo  ELSE 'J'||p.numero_nivel_1||'-C'||p.numero_nivel_3||' - '||c.nombre_campo END) as rotulo_parcela, "+
                    " p.area, p.variedad as cultivo, "+
                    " (SELECT round((CAST(valor AS NUMERIC) * p.area), 0) FROM _variables_ WHERE nombre_variable = 'muestrasxarea_metamasius') as muestras_recomendadas, "+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'tallos_metamasius') as tallos_evaluar,"+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'entrenudos_metamasius') as entrenudos_evaluar,"+
                    " (SELECT COUNT(id)+1 FROM frm_metamasius WHERE cod_parcela = p.cod_parcela) as numero_muestra_actual"+
                    " FROM campo c, parcela p WHERE c.cod_campana = p.cod_campana"+
                    " AND p.cod_parcela = ?",
                    [codParcela]);
    };

    this.agregarMuestraMetamasius = function(objMuestra){
        return _db.insertarDatos("frm_metamasius",  
                                    ["cod_parcela", "tallos_evaluados","tallos_dañados","entrenudos_evaluados","entrenudos_dañados","finalizacion","usuario_registro"],
                                        [objMuestra]);
    };
    */

    this.obtenerUIMetamasius = function(codParcela) {
        return _db.selectData("SELECT (CASE c.tipo_riego WHEN '1' THEN 'M'||p.numero_nivel_1||'-T'||p.numero_nivel_2||'-V'||p.numero_nivel_3||' - '||c.nombre_campo  ELSE 'J'||p.numero_nivel_1||'-C'||p.numero_nivel_3||' - '||c.nombre_campo END) as rotulo_parcela, "+
                    " p.area, p.variedad as cultivo, "+
                    " (SELECT round((CAST(valor AS NUMERIC) * p.area), 0) FROM _variables_ WHERE nombre_variable = 'muestrasxarea_metamasius') as muestras_recomendadas, "+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'tallos_metamasius') as tallos_evaluar,"+
                    " (SELECT valor FROM _variables_ WHERE nombre_variable = 'entrenudos_metamasius') as entrenudos_evaluar,"+
                    " (SELECT COUNT(id)+1 FROM frm WHERE cod_parcela = p.cod_parcela AND cod_formulario = 5) as numero_muestra_actual"+
                    " FROM campo c, parcela p WHERE c.cod_campana = p.cod_campana"+
                    " AND p.cod_parcela = ?",
                    [codParcela]);
    };

    this.agregarMuestraMetamasius = function(objMuestra){
        objMuestra.cod_formulario = 5;
        return _db.insertarDatos("frm",  
                                    ["cod_parcela", "met_tallos_evaluados","met_tallos_danados","met_entrenudos_evaluados","met_entrenudos_danados","finalizacion","usuario_registro","cod_formulario"],
                                        [objMuestra]);
    };    

};

/*
CREATE TABLE frm_diatraea(
    id INTEGER AUTOINCREMENTAL PRIMARY KEY,
    cod_parcela INTEGER,
    entrenudos_muestreados INTEGER,
    numero_nudos_infestados INTEGER,
    tallos_muestreados INTEGER,
    tallos_infestados INTEGER,
    larvas_estadio_1 INTEGER,
    larvas_estadio_2 INTEGER,
    larvas_estadio_3 INTEGER,
    larvas_estadio_4 INTEGER,
    larvas_estadio_5 INTEGER,
    larvas_estadio_6 INTEGER,
    crisalidas_individuos INTEGER,
    larvas_parasitadas INTEGER,
    billaea_larvas INTEGER,
    billaea_pupas INTEGER,
    finalizacion BOOLEAN DEFAULT,
    registro_app INTEGER DEFAULT 1
)
CREATE TABLE frm_roya(
    id INTEGER AUTOINCREMENTAL PRIMARY KEY,
    cod_parcela INTEGER,
    hojas INTEGER,
    hojas_afectadas INTEGER,
    porcentaje_afectadas NUMERIC,
    finalizacion BOOLEAN DEFAULT,
    registro_app INTEGER DEFAULT 1
)

CREATE TABLE frm_carbon(
    id INTEGER AUTOINCREMENTAL PRIMARY KEY,
    cod_parcela INTEGER,
    n_metros INTEGER,
    tallos INTEGER,
    tallos_latigo INTEGER,
    finalizacion BOOLEAN DEFAULT,
    registro_app INTEGER DEFAULT 1
)

CREATE TABLE frm_elasmopalpus(
    id INTEGER AUTOINCREMENTAL PRIMARY KEY,
    cod_parcela INTEGER,
    area_muestreada NUMERIC,
    tallos_metro INTEGER,
    tallos_infectados INTEGER,
    larvas INTEGER,
    pupas INTEGER,
    larvas_muertas INTEGER,
    finalizacion BOOLEAN DEFAULT,
    registro_app INTEGER DEFAULT 1
)

*/