<?php

require_once '../datos/Conexion.clase.php';

class ReporteadorFormulario extends Conexion {

    public function obtenerReporte($fi, $ff){
        try {
            //la evaluacion debe TENER un fecha_inicio_Evalaucion_temporal y Estado Confirmado.
            $data = [];
            $sql = "SELECT * FROM v_registros_resumen_diatraea WHERE raw_fecha_evaluacion::date BETWEEN date(:0) AND date(:1) 
            ORDER BY nombre_campo,
                numero_nivel_1, 
	            numero_nivel_2,
	              NULLIF(regexp_replace(numero_nivel_3, '\D', '', 'g'), '')::integer" ;
            $data["diatraea"] = $this->consultarFilas($sql, [$fi, $ff]);
	
	/*
            $sql = "SELECT * FROM v_full_resumen_diatraea 
                    WHERE fecha_evaluacion::date BETWEEN date(:0) AND date(:1) ORDER BY nombre_campo";

            $data["diatraea_resumen"] = $this->consultarFilas($sql, [$fi, $ff]);

            $sql = "SELECT 
                    r.* , p.area
                    FROM v_registros_carbon r
                    INNER JOIN parcela p ON p.cod_parcela = r.cod_parcela
                    WHERE date(raw_fecha_evaluacion) BETWEEN date(:0) AND date(:1) ORDER BY 
                    nombre_campo,
                    p.numero_nivel_1, 
	            p.numero_nivel_2,
	              NULLIF(regexp_replace(p.numero_nivel_3, '\D', '', 'g'), '')::integer" ;
            $data["carbon"] = $this->consultarFilas($sql, [$fi, $ff]);


            $sql = " SELECT * FROM v_full_resumen_carbon
                     WHERE date(fecha_evaluacion) BETWEEN date(:0) AND date(:1) ORDER BY nombre_campo";
	
            $data["carbon_resumen"] = $this->consultarFilas($sql, [$fi, $ff]);
	*/
            return ["rpt"=>true,"data"=>$data];

        } catch (Exception $exc) {            
            return ["rpt"=>false, "msj"=>$exc->getMessage()];
        }
    }
    
    
}

    
