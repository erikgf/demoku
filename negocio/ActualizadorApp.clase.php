<?php

require_once '../datos/Conexion.clase.php';

class ActualizadorApp extends Conexion {

    public function actualizarDatos(){
        try {
            /*Tbl usuario */
            /*Tbl campos */
            /*Tbl parcelas */
            /*Tbl formularios */
            $contador_registros  = 0;

            $sql = "SELECT 
                    u.cod_usuario,
                    CONCAT(c.nombres,' ',c.apellidos) as nombres_apellidos,
                    1 as cod_rol,
                    u.usuario,
                    u.clave
                    FROM usuario u
                    INNER JOIN colaborador c ON c.cod_colaborador = u.cod_colaborador
                    WHERE u.estado_mrcb = 1 AND u.estado = 'A' AND c.estado_baja = 'A'";
            $usuarios =  $this->consultarFilas($sql);
            $contador_registros += count($usuarios);

            $sql = "SELECT 
                        cp.cod_campaña as cod_campana,
                        ca.nombre_campo,
                        si.numero_siembra,
                        si.tipo_riego,
                        --v.nombre as variedad,
                        cp.numero_campaña as numero_campana
                    FROM campo ca
                    INNER JOIN siembra si ON ca.cod_campo = si.cod_campo AND si.estado_activo
                    INNER JOIN campaña cp ON cp.cod_siembra = si.cod_siembra AND cp.estado_activo";
                    /*-- INNER JOIN variedad v ON v.cod_variedad = si.cod_variedad
                    -- INNER JOIN cultivo cu ON cu.cod_cultivo = v.cod_cultivo
                    -- WHERE cu.cod_cultivo = 1"; CAÑA
                    */
            $campos = $this->consultarFilas($sql);          
            $contador_registros += count($campos);  

            $sql = "SELECT 
                        cod_parcela, numero_nivel_1, COALESCE(numero_nivel_2,'0') as numero_nivel_2, numero_nivel_3, 
                        area, cod_campaña as cod_campana, p.tipo_riego,
                        v.nombre as variedad,  fecha_inicio_campaña as fecha_inicio
                    FROM parcela p
                    INNER JOIN variedad v ON v.cod_variedad = p.cod_variedad AND v.estado_mrcb = 1 AND v.cod_cultivo = 1";
            $parcelas =  $this->consultarFilas($sql);
            $contador_registros += count($parcelas);  

            $sql = "SELECT cod_formulario, descripcion, nombre_interfaz, nombre_tabla FROM formulario WHERE estado_mrcb = 1";
            $formularios = $this->consultarFilas($sql);
            $contador_registros += count($formularios);  

            $sql = "SELECT nombre_variable, valor FROM variable_sistema WHERE estado_mrcb = 1";
            $variables = $this->consultarFilas($sql);
            $contador_registros += count($variables);  

            $sql = "SELECT cod_etapa_fenologica as cod_etapa, nombre FROM etapa_fenologica WHERE estado_mrcb = 1";
            $etapas_fenologicas = $this->consultarFilas($sql);
            $contador_registros += count($variables);  

            /*Liberaciones pendeintes*/
            /*
            $sql = "SELECT cod_campana, cod_parcela, cantidad_moscas, cod_usuario_liberador as cod_usuario 
                    FROM liberacion_programacion_detalle WHERE estado_mrcb = 1"; // campaña activar, por ahora solo hay una campaña
            $liberaciones = $this->consultarFilas($sql);*/
            $liberaciones = [];
            $contador_registros += count($liberaciones);  

            return array("rpt"=>true,"data"=>["usuarios"=>$usuarios,
                                                "campos"=>$campos,
                                                    "parcelas"=>$parcelas,
                                                        "formularios"=>$formularios,
                                                            "etapas"=>$etapas_fenologicas,
                                                                "_variables_"=>$variables,
                                                                    "liberaciones"=>$liberaciones,
                                                                    "contador_registros"=>$contador_registros]);

        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }   


    private function setDetalle($objDetalle ,$codFormulario){
        $objRetorno;
        switch ($codFormulario){    
            case 1: 

                $objRetorno = [
                    "bio_data_entrenudos" =>  $objDetalle->bio_data_entrenudos,
                    "bio_etapa_fenologica" =>  $objDetalle->bio_etapa_fenologica,
                    "bio_volumen_promedio" =>  $objDetalle->bio_volumen_promedio,
                    "bio_largo_promedio" =>  $objDetalle->bio_largo_promedio,
                    "bio_diametro_promedio" =>  $objDetalle->bio_diametro_promedio,
                    "bio_entrenudos_promedio" =>  $objDetalle->bio_entrenudos_promedio,
                    "bio_crecimiento_promedio" =>  $objDetalle->bio_crecimiento_promedio,
                    "bio_ml_metros" =>  $objDetalle->bio_ml_metros,
                    "bio_ml_tallos" => $objDetalle->bio_ml_tallos == "" ?  0 : $objDetalle->bio_ml_tallos,
                    "bio_ml_tallos_metros" =>  $objDetalle->bio_ml_tallos_metros,
                    "bio_pt_peso_tallos" =>  $objDetalle->bio_pt_peso_tallos,
                    "bio_pt_pesos" => $objDetalle->bio_pt_pesos == "" ?  0 : $objDetalle->bio_pt_pesos,
                    "bio_pt_tallos" =>  $objDetalle->bio_pt_tallos == "" ?  0 : $objDetalle->bio_pt_tallos,
                    "bio_pt_toneladas" => $objDetalle->bio_pt_toneladas
                ];

                break;

            case 2:
                $objRetorno = [
                    "dia_billaea_larvas" => $objDetalle->dia_billaea_larvas,
                    "dia_billaea_pupas" => $objDetalle->dia_billaea_pupas,
                    "dia_crisalidas" => $objDetalle->dia_crisalidas,
                    "dia_entrenudos" => $objDetalle->dia_entrenudos,
                    "dia_entrenudos_infestados" => $objDetalle->dia_entrenudos_infestados,
                    "dia_larvas_estadio_1" => $objDetalle->dia_larvas_estadio_1,
                    "dia_larvas_estadio_2" => $objDetalle->dia_larvas_estadio_2,
                    "dia_larvas_estadio_3" => $objDetalle->dia_larvas_estadio_3,
                    "dia_larvas_estadio_4" => $objDetalle->dia_larvas_estadio_4,
                    "dia_larvas_estadio_5" => $objDetalle->dia_larvas_estadio_5,
                    "dia_larvas_estadio_6" => $objDetalle->dia_larvas_estadio_6,
                    "dia_larvas_indice" => $objDetalle->dia_larvas_indice,
                    "dia_larvas_parasitadas" => $objDetalle->dia_larvas_parasitadas,
                    "dia_tallos" => $objDetalle->dia_tallos,
                    "dia_tallos_infestados" => $objDetalle->dia_tallos_infestados
                ];
                break;

            case 3:
                $objRetorno = [
                    "ela_area_muestreada" => $objDetalle->ela_area_muestreada,
                    "ela_larvas" => $objDetalle->ela_larvas,
                    "ela_larvas_muertas" => $objDetalle->ela_larvas_muertas,
                    "ela_pupas" => $objDetalle->ela_pupas,
                    "ela_tallos_infectados" => $objDetalle->ela_tallos_infectados,
                    "ela_tallos_metro" => $objDetalle->ela_tallos_metro
                ];
            break;

            case 4:
                $objRetorno = [
                    "car_tallos" => $objDetalle->car_tallos,
                    "car_tallos_latigo" => $objDetalle->car_tallos_latigo
                ];
            break;

            case 5:
                $objRetorno = [
                    "met_entrenudos_evaluados" => $objDetalle->met_entrenudos_evaluados,
                    "met_entrenudos_danados" => $objDetalle->met_entrenudos_danados,
                    "met_tallos_danados" => $objDetalle->met_tallos_danados,
                    "met_tallos_evaluados" => $objDetalle->met_tallos_evaluados,
                    "met_larvas"=> $objDetalle->met_larvas
                ];
            break;

            case 6:
                $objRetorno = [
                    "roy_hojas" => $objDetalle->roy_hojas,
                    "roy_hojas_afectadas" => $objDetalle->roy_hojas_afectadas,
                    "roy_porcentaje_afectadas" => $objDetalle->roy_porcentaje_afectadas
                ];
            break;

        }

        $objRetorno["foto_registro_1"]  = $objDetalle->foto_registro_1;
        $objRetorno["foto_registro_2"]  = $objDetalle->foto_registro_2;
        $objRetorno["foto_registro_3"]  = $objDetalle->foto_registro_3;
        $objRetorno["longitud_coord"]  = $objDetalle->longitud_coord;
        $objRetorno["latitud_coord"]  = $objDetalle->latitud_coord;

        return $objRetorno;
    }

    public function sincronizarDatos($JSONData){
        try {
            $objDecoded = json_decode($JSONData);

            //obj decoded contiene todas las plagas (6) mas la cabecera
            $objCabecera = $objDecoded->cabecera;
            $cabecera = [];
            
            foreach ($objDecoded as $rotulo => $grupo_formulario) {
                if ($rotulo != "cabecera"){
                   foreach ($grupo_formulario as $key => $value) {
                        $obj = [    "cod_parcela"=>$value->cod_parcela,
                                    "cod_formulario_evaluacion"=>$value->cod_formulario,
                                    "fecha_registro"=> $value->fecha_registro,
                                    "cod_evaluador"=> $objCabecera->cod_evaluador,
                                    "movil_id"=>$objCabecera->cod_movil,
                                    "detalle"=>$value->detalles
                                    ];

                        array_push($cabecera, $obj);
                    }     
                }
            }

            $this->beginTransaction();
            $codRegistro = $this->consultarValor("SELECT COALESCE(MAX(cod_registro)+1, 1) FROM registros_cabecera");
            foreach ($cabecera as $key => $value) {
                $campos_valores = [
                    "cod_registro"=>$codRegistro,
                    "fecha_registro"=>$value["fecha_registro"],
                    "fecha_evaluacion"=>$value["fecha_registro"],
                    "cod_evaluador"=>$value["cod_evaluador"],
                    "movil_id"=>$value["movil_id"],
                    "cod_formulario_evaluacion"=>$value["cod_formulario_evaluacion"],
                    "cod_parcela"=>$value["cod_parcela"]
                ];
                $this->insert("registros_cabecera", $campos_valores);

                $codRegistroDetalle = $this->consultarValor("SELECT COALESCE(MAX(cod_registro_detalle)+1, 1) FROM registros_detalle");

                $item = 1;
                foreach ($value["detalle"] as $key_ => $detalle) {
                    $campos_valores_detalle = $this->setDetalle($detalle, $value["cod_formulario_evaluacion"]);
                    $campos_valores_detalle["cod_registro_detalle"] = $codRegistroDetalle;
                    $campos_valores_detalle["cod_registro"] = $codRegistro;
                    $campos_valores_detalle["item"] = $item;

                    $this->insert("registros_detalle", $campos_valores_detalle);
                    $codRegistroDetalle++;
                    $item++;
                }
                $codRegistro++;
            }

            $this->commit();

            return array("rpt"=>true,"msj"=>"Data resincronizada correctamente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }

    public function masterKey(){
        try {
            $masterKey = md5('123456');
            return array("rpt"=>true,"data"=>$masterKey);

        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }
}
