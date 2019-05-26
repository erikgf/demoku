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
                        pa.idcodigo as idpuntoacceso,
                        pa.descripcion
                        FROM tbl_punto_acceso pa";

            $puntosacceso =  $this->consultarFilas($sql);
            $contador_registros += count($puntosacceso);

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
                                                    "puntosacceso"=>$puntosacceso,
                                                        "personal"=>$personal,
                                                            "contador_registros"=>$contador_registros]);

        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
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
                    "movil_id"=>$objCabecera->movil_id,
                    "idpuntoacceso"=> $objCabecera->idpuntoacceso
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

            /*
            //CONSUTLAR ASI LA FECHA DE dia EXISTE
            $sql = "SELECT COUNT(fecha_dia) > 0 FROM tbl_asistencia_dia WHERE fecha_dia = :0";
            $existeDia = $this->consultarValor($sql, [$objCabecera->fecha_dia_envio]);

            if ($existeDia == false){
                $campos_valores = ["fecha_dia" => $objCabecera->fecha_dia_envio];
                $this->insert("tbl_asistencia_dia", $campos_valores);
            } 
           
            //CONSUTLAR ASI LA FECHA DE DIA/TURNO EXISTE
            $sql = "SELECT COUNT(fecha_dia) > 0 FROM tbl_asistencia_dia_turno WHERE fecha_dia = :0 AND cod_turno = :1";
            $existeDiaTurno = $this->consultarValor($sql, [$objCabecera->fecha_dia_envio, $objCabecera->cod_turno_envio]);

            if ($existeDiaTurno == 0){
                $campos_valores = ["fecha_dia" => $objCabecera->fecha_dia_envio, "cod_turno"=> $objCabecera->cod_turno_envio];
                $this->insert("tbl_asistencia_dia_turno", $campos_valores);
            }

            //INSERTAR DETALLES
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
            */
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
