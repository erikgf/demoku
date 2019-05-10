<?php

require_once '../datos/Conexion.clase.php';

class ReporteadorFormulario extends Conexion {


    public function obtenerReporte($fi, $ff){
        try {

            //la evaluacion debe TENER un fecha_inicio_Evalaucion_temporal y Estado Confirmado.
            $data = [];
          
            $sql = "SELECT * FROM v_registros_resumen_diatraea 
                    WHERE raw_fecha_evaluacion BETWEEN date(:0) AND date(:1) ORDER BY nombre_campo,
                    numero_nivel_1, 
                    numero_nivel_2,
                    NULLIF(regexp_replace(numero_nivel_3, '\D', '', 'g'), '')::integer" ;
            $data["diatraea"] = $this->consultarFilas($sql, [$fi, $ff]);

            $sql = "SELECT *, 
                     to_char(raw_fecha_evaluacion, 'DD-MM-YYYY'::text) as fecha_evaluacion
                     FROM 
                     (SELECT v_registros_resumen_diatraea.nombre_campo,
                        NULL::text AS fecha_inicio_campaña,
                        NULL::text AS numero_campaña,
                        v_registros_resumen_diatraea.numero_evaluacion,
                        v_registros_resumen_diatraea.raw_fecha_evaluacion::date,
                        sum(v_registros_resumen_diatraea.dia_tallos) AS dia_tallos,
                        sum(v_registros_resumen_diatraea.dia_tallos_infestados) AS dia_tallos_infestados,
                        sum(v_registros_resumen_diatraea.dia_entrenudos) AS dia_entrenudos,
                        sum(v_registros_resumen_diatraea.dia_entrenudos_infestados) AS dia_entrenudos_infestados,
                        sum(v_registros_resumen_diatraea.dia_larvas_estadio_1) + sum(v_registros_resumen_diatraea.dia_larvas_estadio_2) + sum(v_registros_resumen_diatraea.dia_larvas_estadio_3) + sum(v_registros_resumen_diatraea.dia_larvas_estadio_4) + sum(v_registros_resumen_diatraea.dia_larvas_estadio_5) + sum(v_registros_resumen_diatraea.dia_larvas_estadio_6) AS larvas_totales,
                        sum(v_registros_resumen_diatraea.dia_crisalidas) AS dia_crisalidas,
                        sum(v_registros_resumen_diatraea.dia_larvas_parasitadas) AS dia_larvas_parasitadas,
                        sum(v_registros_resumen_diatraea.dia_billaea_larvas) AS dia_billaea_larvas,
                        sum(v_registros_resumen_diatraea.dia_billaea_pupas) AS dia_billaea_pupas
                       FROM v_registros_resumen_diatraea
                         JOIN campaña c ON c.cod_campaña = v_registros_resumen_diatraea.cod_campaña
                      GROUP BY v_registros_resumen_diatraea.nombre_campo, 
                                v_registros_resumen_diatraea.raw_fecha_evaluacion::date,
                                v_registros_resumen_diatraea.numero_evaluacion) tx
                     WHERE  raw_fecha_evaluacion BETWEEN date(:0) AND date(:1) ORDER BY nombre_campo";

            $data["diatraea_resumen"] = $this->consultarFilas($sql, [$fi, $ff]);

            $sql = "SELECT 
                    r.* , p.area
                    FROM v_registros_carbon r
                    INNER JOIN parcela p ON p.cod_parcela = r.cod_parcela
                    WHERE raw_fecha_evaluacion::date BETWEEN date(:0) AND date(:1) ORDER BY 
                    nombre_campo,
                    p.numero_nivel_1, 
                    p.numero_nivel_2,
                    p.numero_nivel_3" ;
            $data["carbon"] = $this->consultarFilas($sql, [$fi, $ff]);


            $sql = " SELECT * FROM v_full_resumen_carbon
                     WHERE fecha_evaluacion::date  BETWEEN date(:0) AND date(:1) ORDER BY nombre_campo";

            $data["carbon_resumen"] = $this->consultarFilas($sql, [$fi, $ff]);
          
            /*VARIABLES EXTRA*/
            $dataExtra = [];

            $sql = "SELECT distinct(nivel) as nivel, limite_inferior, limite_superior, numero_parejas FROM variable_moscas_liberacion WHERE fecha_fin IS NULL ORDER BY nivel ";
            $dataExtra["numero_parejas"] = $this->consultarFilas($sql);

            $sql = "SELECT distinct(nivel) as nivel, limite_inferior, limite_superior, descripcion as riesgo FROM variable_indice_infestacion WHERE fecha_fin IS NULL ORDER BY nivel ";
            $dataExtra["indice_infestacion"] = $this->consultarFilas($sql);

            $data["data_extra"] = $dataExtra;

            return ["rpt"=>true,"data"=>$data];

        } catch (Exception $exc) {            
            return ["rpt"=>false, "msj"=>$exc->getMessage()];
        }
    }
    
    
}

    
