<?php

trait UtilidadesExtra {
    private function obtenerTipoRiegoUmd($idUmd){
        return $this->consultarValor("SELECT * FROM fn_get_tipo_riego_umd(:0)",[$idUmd]);
    }

    private function obtenerSQLCabeceraPuntoMuestra($idUmd)
    {
            $boolRiego =  $this->obtenerTipoRiegoUmd($idUmd) == 1;
            if ($boolRiego){
                $sqlConcat = " CONCAT('VÁLVULA: ',ca_u.numero_nivel_tres) as numero_valvula ";           
            } else {
                $sqlConcat = "CONCAT('JIRÓN: ', ca_u.numero_nivel_uno) as numero_jiron,
                    CONCAT('CUARTEL: ', ca_u.numero_nivel_dos) as numero_cuartel "; 
            }

            $sqlCabecera = "SELECT cp.nombre_campo, 
                    ".$sqlConcat."
                    ,si.id_tipo_riego as tipo_riego,
                    (SELECT COUNT(distinct(numero_punto)) 
                    FROM punto p
                    WHERE p.id_campaña = ca.id_campaña AND 
                    id_umd = ev_u.id_umd AND id_evaluacion =  e.id_evaluacion
                    ) as puntos_muestreados,
                    (
                    SELECT COUNT(*)/(SELECT COUNT(*) FROM dato_muestreo WHERE estado_mrcb = 1) FROM muestra
                    WHERE id_umd = ev_u.id_umd AND id_evaluacion = e.id_evaluacion 
                    AND numero_punto = :2 AND id_campaña = ca.id_campaña
                    ) as muestras_completadas
                    FROM
                    evaluacion_umd ev_u
                    LEFT JOIN campaña_umd ca_u ON ca_u.id_campaña = ev_u.id_campaña AND ca_u.id_umd = ev_u.id_umd
                    LEFT JOIN evaluacion e ON e.id_evaluacion = ev_u.id_evaluacion
                    LEFT JOIN campaña ca ON ca.id_campaña = ca_u.id_campaña AND ca.estado = 'A' 
                    LEFT JOIN siembra si ON si.id_siembra = ca.id_siembra AND si.estado = 'A'
                    LEFT JOIN campo cp ON cp.id_campo = si.id_campo AND cp.estado ='A'
                    WHERE 
                    ca_u.id_umd = :1 AND e.id_evaluacion = :0";

          return $sqlCabecera;
    }
}