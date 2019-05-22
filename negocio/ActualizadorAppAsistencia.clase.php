<?php

require_once '../datos/Conexion.clase.php';

class ActualizadorAppAsistencia extends Conexion {

    public function actualizarDatos(){
        try {
            /*Tbl usuario */
            $contador_registros  = 0;

            $sql = "SELECT 
                    u.usuario as nombres_apellidos,
                    u.usuario,
                    u.clave
                    FROM tbl_usuario u
                    INNER JOIN usuario_aplicacion ua ON ua.usuario = u.usuario AND ua.tipo_aplicacion = 'A'
                    WHERE u.estado_mrcb = 1 AND u.estado = 'A'";
            $usuarios =  $this->consultarFilas($sql);
            $contador_registros += count($usuarios);


            $sql = "SELECT 
                        pg.idcodigogeneral as dni,
                        CONCAT(a_paterno,' ',a_materno,', ',nombres) as nombres_apellidos,
                        cp.descripcion as rol
                        FROM tbl_personal_general pg
                        INNER JOIN tbl_personal p ON p.idempresa = pg.idempresa AND p.idcodigogeneral = pg.idcodigogeneral
                        LEFT JOIN tbl_cargo_personal cp ON cp.idcargo = p.idcargo
                        LEFT JOIN tbl_planilla pl ON pl.idplanilla = p.idplanilla
                        WHERE activado_en_estaplani = '1'";

            $personal =  $this->consultarFilas($sql);
            $contador_registros += count($personal);

            $sql = "SELECT 
                    idturnotrabajo as cod_turno,
                    descripcion,
                    desde as hora_entrada,
                    hasta as hora_salida
                    FROM turno_trabajo                    
                    ORDER BY desde";
            $turnos =  $this->consultarFilas($sql);
            $contador_registros += count($turnos);

            return array("rpt"=>true,"data"=>["usuarios"=>$usuarios,
                                                "turnos"=>$turnos,
                                                    "personal"=>$personal,
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

    public function enviarDatos($JSONData){
        try {
            $objDecoded = json_decode($JSONData);

            $objCabecera = $objDecoded->cabecera;
            $objDetalle = $objDecoded->detalle;

            $this->beginTransaction();

            $codRegistro = $this->consultarValor("SELECT COALESCE(MAX(cod_envio_cabecera)+1, 1) FROM tbl_asistencia_envio_cabecera");

            $campos_valores = [
                    "cod_envio_cabecera"=>$codRegistro,
                    "fecha_dia"=>$objCabecera->fecha_dia_envio,
                    "cod_turno"=>$objCabecera->cod_turno_envio,
                    "usuario_envio"=>$objCabecera->usuario_envio,
                    "movil_id"=>$objCabecera->movil_id
                ];

            $this->insert("tbl_asistencia_envio_cabecera", $campos_valores);
            $codRegistroDetalle = $this->consultarValor("SELECT COALESCE(MAX(cod_envio_detalle)+1, 1) FROM tbl_asistencia_envio_detalle");

            foreach ($objDetalle as $key => $detalle) {
                $campos_valores_detalle = [
                    "cod_envio_detalle" => $codRegistroDetalle,
                    "cod_envio_cabecera" => $codRegistro,
                    "dni_asistencia" => $detalle->dni_asistencia,
                    "numero_asistencia" => $detalle->numero_asistencia,
                    "tipo_registro" => $detalle->tipo_registro,
                    "fecha_hora_registro" => $detalle->hora_registro
                ];

                $this->insert("tbl_asistencia_envio_detalle", $campos_valores_detalle);
                $codRegistroDetalle++;
            }

            /*CONSUTLAR ASI LA FECHA DE dia EXISTE*/
            $sql = "SELECT COUNT(fecha_dia) > 0 FROM tbl_asistencia_dia WHERE fecha_dia = :0";
            $existeDia = $this->consultarValor($sql, [$objCabecera->fecha_dia_envio]);

            if ($existeDia == false){
                $campos_valores = ["fecha_dia" => $objCabecera->fecha_dia_envio];
                $this->insert("tbl_asistencia_dia", $campos_valores);
            } 
           
            /*CONSUTLAR ASI LA FECHA DE DIA/TURNO EXISTE*/
            $sql = "SELECT COUNT(fecha_dia) > 0 FROM tbl_asistencia_dia_turno WHERE fecha_dia = :0 AND cod_turno = :1";
            $existeDiaTurno = $this->consultarValor($sql, [$objCabecera->fecha_dia_envio, $objCabecera->cod_turno_envio]);

            if ($existeDiaTurno == 0){
                $campos_valores = ["fecha_dia" => $objCabecera->fecha_dia_envio, "cod_turno"=> $objCabecera->cod_turno_envio];
                $this->insert("tbl_asistencia_dia_turno", $campos_valores);
            }

            /*INSERTAR DETALLES*/
            foreach ($objDetalle as $key => $detalle) {
                $campos_valores = [
                            "fecha_dia" => $objCabecera->fecha_dia_envio, 
                            "cod_turno"=> $objCabecera->cod_turno_envio,
                            "dni_asistencia"=> $detalle->dni_asistencia,
                            "numero_asistencia" => $detalle->numero_asistencia,
                            "tipo_registro" => $detalle->tipo_registro,
                            "fecha_hora_registro" => $detalle->hora_registro
                        ];

                $this->insert("tbl_asistencia_dia_turno_detalle", $campos_valores);
            }

            $this->commit();
            return array("rpt"=>true,"msj"=>"Data recibida correctamente.");
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
