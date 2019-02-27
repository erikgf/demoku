<?php

require_once '../datos/Conexion.clase.php';

class ObservacionUMD extends Conexion {
    private $idEvaluacion;
    private $descripcion;
    private $idUmd;

    public function listarObservaciones($idUmd, $idEvaluacion)
    {
        try {
            $sql = "SELECT ou.descripcion, too.descripcion as tipo_observacion
                    FROM observacion_umd ou
                    INNER JOIN tipo_observacion too ON too.id_tipo_observacion = ou.id_tipo_observacion WHERE
                    id_umd = :0 AND 
                    id_evaluacion = :1";
            $params = array($idUmd,$idEvaluacion);
            $observaciones = $this->consultarFilas($sql, $params);
            return array("rpt"=>true,"data"=>$observaciones);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function guardarObservacion($JSONCabecera)
    {
        try {
            $this->beginTransaction();

            $cabecera = json_decode($JSONCabecera);

            $idCampaña = $this->consultarValor(
                "SELECT id_campaña FROM evaluacion 
                WHERE id_evaluacion = :0 AND estado  = 'A'", [$cabecera->p_idEvaluacion]);

            $campos_valores = [
                    "id_campaña"=>$idCampaña,
                    "id_umd"=>$cabecera->p_idUmd,                
                    "id_evaluacion"=>$cabecera->p_idEvaluacion,
                    "id_tipo_observacion" => $cabecera->p_idTipoObservacion,
                    "descripcion"=> isset($cabecera->p_descripcion) ? $cabecera->p_descripcion : null
                    ];

            $this->insert("observacion_umd",$campos_valores); 

            $this->commit();
            return array("rpt"=>true, "msj"=>"Observación registrada CORRECTAMENTE");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }
    
}

    