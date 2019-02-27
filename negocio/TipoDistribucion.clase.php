<?php

require_once '../datos/Conexion.clase.php';

class TipoDistribucion extends Conexion {

    public function listar(){
        try {
            $sql = "SELECT  id_tipo_distribucion,
                descripcion_nivel_uno,
                descripcion_nivel_dos,
                tr.descripcion as tipo_riego,
                COALESCE(descripcion_nivel_tres,'-') as descripcion_nivel_tres
                FROM tipo_distribucion td
                INNER JOIN tipo_riego tr ON tr.id_tipo_riego = td.id_tipo_riego
            ORDER BY 1";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }
}