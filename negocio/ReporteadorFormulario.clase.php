<?php

require_once '../datos/Conexion.clase.php';

class ReporteadorFormulario extends Conexion {


    public function obtenerReporte($fi, $ff){
        try {

            //la evaluacion debe TENER un fecha_inicio_Evalaucion_temporal y Estado Confirmado.
            $data = [];
            $sql = "SELECT * FROM v_registros_resumen_diatraea WHERE date(raw_fecha_evaluacion) BETWEEN date(:0) AND date(:1) 
            ORDER BY nombre_campo,
                numero_nivel_1, 
	            numero_nivel_2,
	            numero_nivel_3::integer" ;
            $data["diatraea"] = $this->consultarFilas($sql, [$fi, $ff]);

            return ["rpt"=>true,"data"=>$data];

        } catch (Exception $exc) {            
            return ["rpt"=>false, "msj"=>$exc->getMessage()];
        }
    }
    
    
}

    
