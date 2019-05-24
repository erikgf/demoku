<?php

require_once '../datos/Conexion.clase.php';

class Asistencia extends Conexion {

    public function listarFechas($fechaInicio, $fechaFin){
        try {
            

            $sql = "SELECT distinct(fecha_dia) as fecha_raw, TO_CHAR(fecha_dia,'DD-MM-YYYY') as fecha FROM tbl_asistencia_envio_cabecera WHERE fecha_dia BETWEEN :0 AND :1 ORDER BY fecha_dia;";
            $listaFechas = $this->consultarFilas($sql, [$fechaInicio, $fechaFin]);

            return array("rpt"=>true,"data"=>$listaFechas);

        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }  

    public function listarFechasDetalle($fecha){
        try {
            

           $sql = "SELECT
                distinct ad.dni_asistencia as dni_asistencia,
                CONCAT(pgene.a_paterno,' ',pgene.a_materno,', ',pgene.nombres) as apellidos_nombres,
                tu.descripcion as turno,
                (SELECT to_char(fecha_hora_registro,'HH:MM:SS') FROM tbl_asistencia_envio_detalle WHERE cod_envio_cabecera = ac.cod_envio_cabecera AND dni_asistencia = ad.dni_asistencia AND tipo_registro = 'E' LIMIT 1) as ingreso,
                (SELECT to_char(fecha_hora_registro,'HH:MM:SS') FROM tbl_asistencia_envio_detalle WHERE cod_envio_cabecera = ac.cod_envio_cabecera AND dni_asistencia = ad.dni_asistencia AND tipo_registro = 'S' LIMIT 1) as salida,
                ac.idpuntoacceso, 
                pacc.descripcion as puntoacceso, 
                u.idcodigogeneral as idresponsable,
                u.apellidos_nombres as responsable
                FROM tbl_asistencia_envio_detalle ad
                LEFT JOIN tbl_asistencia_envio_cabecera ac ON ad.cod_envio_cabecera = ac.cod_envio_cabecera
                LEFT JOIN tbl_punto_acceso pacc ON pacc.idcodigo = ac.idpuntoacceso
                LEFT JOIN tbl_usuario u ON u.usuario = ac.usuario_envio
                LEFT JOIN turno_trabajo tu ON tu.idturnotrabajo = ac.cod_turno
                LEFT JOIN tbl_personal_general pgene ON pgene.idcodigogeneral = ad.dni_asistencia
                WHERE ac.fecha_dia = :0";
            $lista = $this->consultarFilas($sql, $fecha);

            return array("rpt"=>true,"data"=>$lista);

        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }  
 
 

 
}
