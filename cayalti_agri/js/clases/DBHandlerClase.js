var DBHandlerClase = function(version) {
    /*
    1.- Crear la bbdd
    2.- Crear la estructura

    otros:
    3.- Limpiar informacion
    4.- Consulta y agregar informacion
    5.- Enviar informacion
    */
    //var url;
    var _tables = [
                   {  nombre: "usuario",
                      campos : [
                            { nombre: "id", tipo: "INTEGER",pk : true},
                            { nombre: "cod_usuario", tipo: "INTEGER"},
                            { nombre: "nombres_apellidos", tipo: "TEXT"},
                            { nombre: "cod_rol", tipo: "INTEGER"},
                            { nombre: "usuario", tipo: "TEXT"},
                            { nombre: "clave", tipo: "TEXT"}
                        ]},
                   {  nombre: "campo",
                      campos : [
                            { nombre: "id", tipo: "INTEGER",pk : true},
                            { nombre: "cod_campana", tipo: "INTEGER"},
                            { nombre: "nombre_campo", tipo: "TEXT"},
                            { nombre: "numero_campana", tipo: "INTEGER"},
                            { nombre: "numero_siembra", tipo: "INTEGER"},
                            { nombre: "tipo_riego", tipo: "INTEGER"}
                        ]},
                    {  nombre: "parcela",
                      campos : [
                            { nombre: "id", tipo: "INTEGER",pk : true},
                            { nombre: "cod_parcela", tipo: "INTEGER"},
                            { nombre: "cod_campana", tipo: "INTEGER"},
                            { nombre: "area", tipo: "NUMERIC"},
                            { nombre: "numero_nivel_1", tipo: "TEXT"},
                            { nombre: "numero_nivel_2", tipo: "TEXT"},
                            { nombre: "numero_nivel_3", tipo: "TEXT"},
                            { nombre: "tipo_riego", tipo: "INTEGER"},
                            { nombre: "variedad", tipo: "TEXT"},
                            { nombre: "fecha_inicio", tipo: "TIMESTAMP"}
                        ]},
                    {  nombre: "formulario",
                      campos : [
                            { nombre: "id", tipo: "INTEGER",pk : true},
                            { nombre: "cod_formulario", tipo: "INTEGER"},
                            { nombre: "descripcion", tipo: "TEXT"},
                            { nombre: "nombre_interfaz", tipo: "TEXT"},
                            { nombre: "nombre_tabla", tipo: "TEXT"}
                        ]},
                    {  nombre: "_variables_",
                        campos: [
                            { nombre: "id", tipo: "INTEGER",pk : true},
                            { nombre: "nombre_variable", tipo: "TEXT"},
                            { nombre: "valor", tipo: "TEXT"}
                        ]},
                    {  nombre: "etapa_fenologica",
                        campos: [
                            { nombre: "id", tipo: "INTEGER",pk : true},
                            { nombre: "cod_etapa", tipo: "INTEGER"},
                            { nombre: "nombre", tipo: "TEXT"},
                        ]},
                    {  nombre: "frm",
                        campos: [
                            { nombre: "id", tipo: "INTEGER",pk : true},
                            { nombre: "cod_parcela", tipo: "INTEGER", "notnull": true},
                            { nombre: "item", tipo: "INTEGER"},
                            { nombre: "bio_etapa_fenologica", tipo: "INTEGER"},
                            { nombre: "bio_data_entrenudos", tipo: "TEXT"},
                            { nombre: "bio_volumen_promedio", tipo: "NUMERIC"},
                            { nombre: "bio_largo_promedio", tipo: "NUMERIC"},
                            { nombre: "bio_crecimiento_promedio", tipo: "NUMERIC"},
                            { nombre: "bio_pt_pesos", tipo: "NUMERIC"},
                            { nombre: "bio_pt_tallos", tipo: "INTEGER"},
                            { nombre: "bio_pt_peso_tallos", tipo: "NUMERIC"},
                            { nombre: "bio_pt_toneladas", tipo: "NUMERIC"},
                            { nombre: "bio_ml_metros", tipo: "NUMERIC"},
                            { nombre: "bio_ml_tallos", tipo: "INTEGER"},
                            { nombre: "bio_ml_tallos_metros", tipo: "NUMERIC"},
                            { nombre: "dia_entrenudos", tipo: "INTEGER"},
                            { nombre: "dia_entrenudos_infestados", tipo: "INTEGER"},
                            { nombre: "dia_tallos", tipo: "INTEGER"},
                            { nombre: "dia_tallos_infestados", tipo: "INTEGER"},
                            { nombre: "dia_larvas_estadio_1", tipo: "INTEGER"},
                            { nombre: "dia_larvas_estadio_2", tipo: "INTEGER"},
                            { nombre: "dia_larvas_estadio_3", tipo: "INTEGER"},
                            { nombre: "dia_larvas_estadio_4", tipo: "INTEGER"},
                            { nombre: "dia_larvas_estadio_5", tipo: "INTEGER"},
                            { nombre: "dia_larvas_estadio_6", tipo: "INTEGER"},
                            { nombre: "dia_larvas_indice", tipo: "INTEGER"},
                            { nombre: "dia_crisalidas", tipo: "INTEGER"},
                            { nombre: "dia_larvas_parasitadas", tipo: "INTEGER"},
                            { nombre: "dia_billaea_larvas", tipo: "INTEGER"},
                            { nombre: "dia_billaea_pupas", tipo: "INTEGER"},
                            { nombre: "roy_hojas", tipo: "INTEGER"},
                            { nombre: "roy_hojas_afectadas", tipo: "INTEGER"},
                            { nombre: "roy_porcentaje_afectadas", tipo: "NUMERIC"},
                            { nombre: "car_n_metros", tipo: "INTEGER"},
                            { nombre: "car_tallos", tipo: "INTEGER"},
                            { nombre: "car_tallos_latigo", tipo: "INTEGER"},
                            { nombre: "ela_area_muestreada", tipo: "NUMERIC"},
                            { nombre: "ela_tallos_metro", tipo: "INTEGER"},
                            { nombre: "ela_tallos_infectados", tipo: "INTEGER"},
                            { nombre: "ela_larvas", tipo: "INTEGER"},
                            { nombre: "ela_pupas", tipo: "INTEGER"},
                            { nombre: "ela_larvas_muertas", tipo: "INTEGER"},
                            { nombre: "met_tallos_evaluados", tipo: "INTEGER"},
                            { nombre: "met_tallos_danados", tipo: "INTEGER"},
                            { nombre: "met_entrenudos_evaluados", tipo: "INTEGER"},
                            { nombre: "met_entrenudos_danados", tipo: "INTEGER"},
                            { nombre: "met_larvas", tipo: "INTEGER"},
                            { nombre: "observaciones", tipo: "TEXT"},
                            { nombre: "foto_registro_1", tipo: "TEXT"},
                            { nombre: "foto_registro_2", tipo: "TEXT"},
                            { nombre: "foto_registro_3", tipo: "TEXT"},
                            { nombre: "latitud_coord", tipo: "TEXT"},
                            { nombre: "longitud_coord", tipo: "TEXT"},
                            { nombre: "finalizacion", tipo: "BOOLEAN", default: "false"},
                            { nombre: "registro_app", default: "1", tipo: "INTEGER"},
                            { nombre: "usuario_registro", tipo: "INTEGER"},
                            { nombre: "fecha_hora_registro", default: "(datetime('now','localtime'))", tipo: "TIMESTAMP"},
                            { nombre: "cod_formulario", default: "1", tipo: "INTEGER"}
                        ]}
            ];        

    this.initialize = function(_version) {
        //url = serviceURL ? serviceURL : "http://localhost:5000/sessions";
        try { 

            if (!window.openDatabase) { 
              alert('Este dispositivo no soporta Base de Datos local.'); 
            } else { 
              var shortName = 'bd_cayalti_agri'; 
              var version = _version == null  ? "1" : _version; 
              var displayName = 'BD Agri Local'; 
              var maxSize = 5*1024*1024; // in bytes 
              this.mydb = openDatabase(shortName, version, displayName, maxSize);  
              this.crearEstructura();            
              //console.log(this.mydb);
             }
        } catch(e) { console.error(e); alert(e.message); }

        return this;
        /*
        var deferred = new Deferred();
        var session = null;
        var l = sessions.length;
        for (var i=0; i < l; i++) {
            if (sessions[i].id === id) {
                session = sessions[i];
                break;
            }
        }
        deferred.resolve(session);
        return deferred.promise;
        */
    };


    this.crearEstructura = function(){
       try {
            /*Aqui se va a crear la estructura de la BBDD
            usuario
            campos
            parcelas
            coordenadas_parcelas
            formularios
            */
            for (var i = _tables.length - 1; i >= 0; i--) {
                this.crearTabla(_tables[i]);
            };

       } 
       catch(e){ 
            console.error(e); 
        }
    };

    this.crearTabla = function(objTabla) {
       try {
            var campos = objTabla.campos, 
              l = campos.length,
              sql = "CREATE TABLE IF NOT EXISTS ";
              sql += objTabla.nombre + '('; 
              for (var i = 0; i < l; i++) {
                var objCampo = campos[i];        
                if (i > 0){
                  sql += ', ';
                }
                sql +=  objCampo.nombre+' '+objCampo.tipo+' '+
                        (objCampo.pk ? ' PRIMARY KEY AUTOINCREMENT ' : '' )+
                        (objCampo.notnull ? ' NOT NULL ' : ' NULL ')+
                        (objCampo.default ? (' DEFAULT '+objCampo.default) : '');
              }
              sql += ');';
              
              this.mydb.transaction(
                function(transaction) {
                  transaction.executeSql(sql, [], this.nullDataHandler, this.errorHandler);
                    /* transaction.executeSql(sql, [], 
                      function(transaction_, results_){
                        transaction_.executeSql("INSERT INTO usuario(cod_usuario, nombres_apellidos, cod_rol, usuario, clave) VALUES (-1,'ADMIN',0,'admin','"+md5('123456')+"')", [], this.nullDataHandler, this.errorHandler);
                      }, this.errorHandler); 
                   */
                  });
          } 
          catch(e) { 
            console.error(e); 
          }
    };

    this.dropEstructura = function(){
         try {
            /*Aqui se va a crear la estructura de la BBDD
            usuario
            campos
            parcelas
            coordenadas_parcelas
            formularios
            */
            for (var i = _tables.length - 1; i >= 0; i--) {
                this.dropTabla(_tables[i].nombre);
            };
       } 
       catch(e){ 
            console.error(e); 
        }
    };

    this.dropTabla = function(nombre_tabla) {
      try {
        this.mydb.transaction(
          function(transaction) {
            transaction.executeSql('DROP TABLE '+nombre_tabla, [], this.nullDataHandler, this.errorHandler);
            });
          } catch(e) {}
    };

    this.limpiarEstructura = function(){
         try {
            /*Aqui se va a crear la estructura de la BBDD
            usuario
            campos
            parcelas
            coordenadas_parcelas
            formularios
            */
            for (var i = _tables.length - 1; i >= 0; i--) {
                this.limpiarTabla(_tables[i].nombre);
            };
       } 
       catch(e){ 
            console.error(e); 
        }
    };

    this.limpiarTabla = function(nombre_tabla) {
      try {
        this.mydb.transaction(
          function(transaction) {
            transaction.executeSql('DELETE FROM '+nombre_tabla, [], this.nullDataHandler, this.errorHandler);
            });
        } catch(e) {
            console.error(e);
        }
    };

    this.errorHandler = function (transaction, error) { 
      console.error("Error procesando SQL: "+ error);
      return true;  
    }

    this.nullDataHandler = function (transaction, results) {        
    }

    this.selectData = function(sql, params){
       try {
          var _mydb = this.mydb;
          return $.Deferred(function (d) {
              _mydb.readTransaction(function (tx) {
                   tx.executeSql(sql,
                        params,
                        function(tx, data){ d.resolve(data);},                        
                        function(tx, error){ d.reject(error);}
                   );
              });
            });
        } catch(e) {
            alert("Error processing SQL: "+ e.message);
        }
    };

    this.insertarDatos = function(nombre_tabla, campos_insercion, data_usuarios, limpiarTabla){
      try{
           var _mydb = this.mydb;
           return $.Deferred(function (d) {
              _mydb.transaction(function (tx) {
                /*  multiple rows using (),()
                var fn = function(){
                    var len_campos = campos_insercion.length - 1,
                        sql = " INSERT INTO "+nombre_tabla+" ( ",
                        sqlQMark = "(",
                        len = data_usuarios.length - 1,
                        paramArray = [], 
                        tmpObj;

                    for (var i = len_campos; i >= 0; i--) {
                        sql += campos_insercion[i];
                        sqlQMark += "?";
                        if (i > 0){
                          sql += ", ";
                          sqlQMark += ", ";
                        }
                    };
                    sqlQMark += ") ";

                    sql += ") VALUES ";

                    for (var i = len; i >= 0; i--) {
                      sql += sqlQMark;
                      if (i > 0 ){
                        sql += ', ';
                      }
                    };

                    for (var i = len; i >= 0; i--) {
                      tmpObj = data_usuarios[i];
                      for (var j = len_campos; j >= 0; j--) {
                        paramArray.push( tmpObj[campos_insercion[j]] );
                      };
                    };

                    sql +=";";

                    tx.executeSql(sql,
                      paramArray,
                      function(tx, data){ d.resolve(data);},                        
                      function(tx, error){ d.reject(error);}
                    );   
                  };
                  */
                var fn = function(){
                    var len_campos = campos_insercion.length - 1,
                        sql = " INSERT INTO "+nombre_tabla+" ( ",
                        sqlQMark = " UNION ALL SELECT ",
                        len = data_usuarios.length - 1,
                        paramArray = [], 
                        tmpObj;

                    for (var i = len_campos; i >= 0; i--) {
                        sql += campos_insercion[i];
                        sqlQMark += "?";
                        if (i > 0){
                          sql += ", ";
                          sqlQMark += ", ";
                        }
                    };
                    sqlQMark += " ";

                    sql += ") ";

                    for (var i = len; i >= 0; i--) {
                      if (i == len){
                        sql += "SELECT ";
                        for (var j = len_campos; j >= 0; j--) {
                          sql += "? AS "+campos_insercion[j];
                          if (j > 0){
                            sql += ", ";
                          }
                        };
                      } else {
                        sql += sqlQMark;
                      }
                    };

                    for (var i = len; i >= 0; i--) {
                      tmpObj = data_usuarios[i];
                      for (var j = len_campos; j >= 0; j--) {
                        paramArray.push( tmpObj[campos_insercion[j]] );
                      };
                    };

                    sql +=";";

                    tx.executeSql(sql,
                      paramArray,
                      function(tx, data){ d.resolve(data);},                        
                      function(tx, error){ d.reject(error);}
                    );   
                  };
                if (limpiarTabla){
                  tx.executeSql("DELETE FROM "+nombre_tabla, [], 
                    fn, 
                      function(tx, error){
                        console.error(error);
                      }
                  );
                  return;
                }

                fn();
              });
            });
      } catch(e){
        console.error(e.message);
      }
    };

    this.actualizarDatos = function(nombre_tabla, campos_actualizacion, valores_actualizacion, campos_where, valores_where){
      try{
           var _mydb = this.mydb;
           return $.Deferred(function (d) {
              _mydb.transaction(function (tx) {
                var len_actualizar = campos_actualizacion.length,
                    len_campos = campos_where.length,
                    sql = "UPDATE  "+nombre_tabla+ " SET ",
                    sqlParams = "",
                    sqlWhere = "",
                    sqlArrayParams = [];

                if (len_actualizar > 0){
                  //Existe where.
                   for (var i = len_actualizar - 1; i >= 0; i--) {
                      sqlParams += campos_actualizacion[i]+" = ?";
                      sqlArrayParams.push(valores_actualizacion[i]);
                      if (i > 0 ){
                        sqlParams += ', ';
                      }
                    };
                }

                sql += sqlParams;

                if (len_campos > 0){
                  //Existe where.
                  sqlWhere += " WHERE ";
                   for (var i = len_campos - 1; i >= 0; i--) {
                      sqlWhere += campos_where[i]+" = ?";
                      sqlArrayParams.push(valores_where[i]);
                      if (i > 0 ){
                        sqlWhere += ' AND ';
                      }
                    };
                }

                sql += sqlWhere;

                tx.executeSql(sql,
                      sqlArrayParams,
                      function(tx, data){ d.resolve(data);},                        
                      function(tx, error){ d.reject(error);}
                ); 
              });
            });
      } catch(e){
        console.error(e.message);
      }
    };


    this.eliminarDatos = function(nombre_tabla, campos_where, valores_where){
      try{
           var _mydb = this.mydb;
           return $.Deferred(function (d) {
              _mydb.transaction(function (tx) {

                var len_campos = campos_where.length,
                    sql = "DELETE FROM "+nombre_tabla,
                    sqlWhere = "";

                if (len_campos > 0){
                  //Existe where.
                  sqlWhere += " WHERE ";
                   for (var i = 0; i < len_campos; i++) {
                      sqlWhere += campos_where[i]+" = ?";
                      if (i < len_campos - 1 ){
                        sqlWhere += ' AND ';
                      }
                    };
                }

                sql += sqlWhere;

                tx.executeSql(sql,
                      valores_where,
                      function(tx, data){ d.resolve(data);},                        
                      function(tx, error){ d.reject(error);}
                ); 

              });
            });
      } catch(e){
        console.error(e.message);
      }
    };

    this.eliminarFrmEnviados = function(cod_usuario){
      try{
           var _mydb = this.mydb;
           return $.Deferred(function (d) {
              _mydb.transaction(function (tx) {

                var sql = "DELETE FROM frm  "+
                        " WHERE cod_parcela||cod_formulario IN (SELECT cod_parcela||cod_formulario FROM frm WHERE finalizacion = 'true' AND usuario_registro = ?)";
                ;
                tx.executeSql(sql,
                      [cod_usuario],
                      function(tx, data){ d.resolve(data);},                        
                      function(tx, error){ d.reject(error);}
                ); 

              });
            });
      } catch(e){
        console.error(e.message);
      }
    };

    this.initialize();  
}