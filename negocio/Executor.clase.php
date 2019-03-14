<?php

require_once '../datos/Conexion.clase.php';

class Executor extends Conexion {


    public function consultaSQL($sql){
        try {

            //la evaluacion debe TENER un fecha_inicio_Evalaucion_temporal y Estado Confirmado.
            $rpt = $this->consulta($sql);
            return ["rpt"=>true,"data"=>$rpt];

        } catch (Exception $exc) {            
            return ["rpt"=>false, "msj"=>$exc->getMessage()];
        }
    }
    
}

    