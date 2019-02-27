var AgriServicio = function() {
	var _db;

    this.initialize = function(db) {
      //  var deferred = $.Deferred();        
        _db = db;
     //   deferred.resolve();
        return this.compilar();//deferred.promise();
    };

    this.compilar = function(){
        return $.get("template.compiler.php");
        //return $.get("template.master.hbs");
    };

    this.iniciarSesion = function(_login, _clave){
    	return _db.selectData(
    				"SELECT cod_usuario, nombres_apellidos as nombre_usuario, cod_rol as rol FROM usuario WHERE usuario = ? AND clave = ?",
    				[_login,_clave]);
    };

    this.insertarUsuarios = function(usuarios){
    	return _db.insertarDatos("usuario",  
    								["cod_usuario", "nombres_apellidos", "cod_rol", "usuario", "clave"],
			    						usuarios, 
			    							true);
    };

    this.insertarCampos = function(campos){
    	return _db.insertarDatos("campo",  
    								["cod_campana", "nombre_campo", "numero_campana", "numero_siembra", "tipo_riego"], //, "tipo_riego", "variedad"],
			    						campos, 
			    							true);

    };


    this.insertarParcelas = function(parcelas){
        //Cada insertar datos impñica un Deffered, estos referede serán pasados al whenAll, cantidad de ferede serán
        // calculados en caliente.
        // Los refered vienen a ser un conjunto de objetos.
        var MAXIMUM_QMARKS = 999,
            objReq = {},
            nombreTabla = "parcela",
            campos = ["cod_parcela", "cod_campana", "area", "numero_nivel_1", "numero_nivel_2", "numero_nivel_3","tipo_riego","variedad","fecha_inicio"],
            lenCampos = campos.length,
            lenDataTotal = parcelas.length,
            registrosMaximos = Math.round(MAXIMUM_QMARKS / lenCampos),
            saltos = Math.ceil(lenDataTotal / registrosMaximos),
            dataTemp = [],
            minPuntero = 0,
            maxPuntero = minPuntero + registrosMaximos;

        for (var i = 0; i < saltos; i++) {
            if (maxPuntero > lenDataTotal){
                maxPuntero = lenDataTotal;
            }
            dataTemp = parcelas.slice(minPuntero, maxPuntero);
            minPuntero = maxPuntero;
            maxPuntero = minPuntero + registrosMaximos - 1;

            objReq["data_"+i] = _db.insertarDatos(nombreTabla,campos,dataTemp,(i==0));
        };

    	return objReq;
    };

    this.insertarFormularios = function(formularios){
    	return _db.insertarDatos("formulario",  
    								["cod_formulario", "descripcion","nombre_interfaz","nombre_tabla"],
			    						formularios, 
			    							true);

    };

    this.insertarVariables = function(variables){
        return _db.insertarDatos("_variables_",  
                                    ["nombre_variable", "valor"],
                                        variables, 
                                            true);

    };

    this.insertarEtapas = function(etapas){
        return _db.insertarDatos("etapa_fenologica",  
                                    ["cod_etapa", "nombre"],
                                        etapas, 
                                            true);

    };
    

    this.consultarTipoRiego = function(cod_campana) {
        return _db.selectData(
                    "SELECT nombre_campo,tipo_riego FROM campo WHERE cod_campana = ?",
                    [cod_campana]);
    };

    this.consultarCampos = function() {
    	return _db.selectData(
    				"SELECT cod_campana, nombre_campo FROM campo ORDER BY nombre_campo",
    				[]);
    };


    this.consultarNivelUno = function(cod_campana) {
        return _db.selectData(
                    "SELECT distinct numero_nivel_1 as n1 FROM parcela WHERE cod_campana = ? ORDER BY numero_nivel_1",
                    [cod_campana]);
    };


    this.consultarNivelDos = function(cod_campana, numero_nivel_1) {
        return _db.selectData(
                    "SELECT distinct numero_nivel_2 as n2 FROM parcela WHERE cod_campana = ? AND numero_nivel_1 = ? ORDER BY numero_nivel_2",
                    [cod_campana, numero_nivel_1]);
    };

    this.consultarParcelas = function(data_consulta) {
        var paramConsulta = [data_consulta.codCampana],
            sql = "",
            sqlRotulo = "",
            sqlWhere = " p.cod_campana = ? ",
            sqlOrder = "";
        //Tipo riego == 0 => JC
        //Tipo riego == 1 => MTV

        if (data_consulta.tipoRiego == "0"){
            sqlRotulo = "'J'||p.numero_nivel_1||'-C'||p.numero_nivel_3||' - '||p.variedad";
            sqlOrder = "CAST(numero_nivel_1 AS INTEGER), CAST(numero_nivel_3 AS INTEGER) ";
        } else {
            sqlRotulo = "'M'||p.numero_nivel_1||'-T'||p.numero_nivel_2||'-V'||p.numero_nivel_3||' - '||p.variedad";
            sqlOrder = "CAST(numero_nivel_3 AS INTEGER)";
        }

        if (data_consulta.nivelUno != "0"){
            paramConsulta.push(data_consulta.nivelUno);
            sqlWhere += " AND numero_nivel_1 = ? ";

            if (data_consulta.nivelDos != null &&  data_consulta.nivelDos != "0"){
                paramConsulta.push(data_consulta.nivelDos);
               sqlWhere += " AND numero_nivel_2 = ? ";
            }
        }

        sql = "SELECT cod_parcela, ("+sqlRotulo+") as rotulo FROM parcela p, campo c WHERE p.cod_campana = c.cod_campana AND "+sqlWhere+" ORDER BY "+sqlOrder;

        return _db.selectData( sql, paramConsulta);
    };

    this.consultarNombreParcela = function(cod_parcela) {
        return _db.selectData(
                    "SELECT (CASE c.tipo_riego WHEN '1' THEN 'M'||p.numero_nivel_1||'-T'||p.numero_nivel_2||'-V'||p.numero_nivel_3||' - '||p.variedad  ELSE 'J'||p.numero_nivel_1||'-C'||p.numero_nivel_3||' - '||p.variedad END) as rotulo_parcela FROM parcela p, campo c WHERE p.cod_campana = c.cod_campana AND cod_parcela = ?",
                    [cod_parcela]);

    };

    this.consultarRegistrosPendientes = function(cod_usuario) {
        return _db.selectData(
                    "SELECT SUM(CASE usuario_registro WHEN ? THEN 1 ELSE 0 END) as propios, COUNT(id) as totales FROM frm",
                    [cod_usuario]);

    };


    this.eliminarRegistrosEnviados = function (cod_usuario) {
        return _db.eliminarDatos("frm", ["usuario_registro"], [cod_usuario]);
    };

    this.consultarFormularios = function(cod_parcela) {
        return _db.selectData(
                    "SELECT f.cod_formulario, f.descripcion, f.nombre_interfaz,"+
                    "(SELECT COUNT(id) FROM frm WHERE cod_formulario = f.cod_formulario AND cod_parcela = ?) as registros_hechos"+
                    " FROM formulario f ORDER BY f.descripcion",
                    [cod_parcela]);
    };

    this.obtenerFrmRegistros = function(nombre_formulario, cod_usuario){
        /*consultar todos los datos de los formularios*/
        return _db.selectData(
                    "SELECT * FROM frm WHERE usuario_registro = ? AND cod_formulario = (SELECT cod_formulario FROM formulario WHERE nombre_interfaz = ?) ORDER BY cod_parcela",
                    [cod_usuario, nombre_formulario]);
    };

};