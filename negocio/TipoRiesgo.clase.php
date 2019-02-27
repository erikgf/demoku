<?php

require_once '../datos/Conexion.clase.php';

class TipoRiesgo extends Conexion {
    private $id_tipo_riesgo;
    private $descripcion;
    private $minimo;
    private $maximo;
    private $estado_mrcb;
    private $fechaVigencia;

    public function getId_tipo_riesgo()
    {
        return $this->id_tipo_riesgo;
    }
    
    public function setId_tipo_riesgo($id_tipo_riesgo)
    {
        $this->id_tipo_riesgo = $id_tipo_riesgo;
        return $this;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }
    
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getMinimo()
    {
        return $this->minimo;
    }
    
    public function setMinimo($minimo)
    {
        $this->minimo = $minimo;
        return $this;
    }

    public function getMaximo()
    {
        return $this->maximo;
    }
    
    public function setMaximo($maximo)
    {
        $this->maximo = $maximo;
        return $this;
    }

    public function getEstado_mrcb()
    {
        return $this->estado_mrcb;
    }
    
    public function setEstado_mrcb($estado_mrcb)
    {
        $this->estado_mrcb = $estado_mrcb;
        return $this;
    }

    public function getFechaVigencia()
    {
        return $this->fechaVigencia;
    }
    
    
    public function setFechaVigencia($fechaVigencia)
    {
        $this->fechaVigencia = $fechaVigencia;
        return $this;
    }

    public function editar($JSONData) {
        $this->beginTransaction();
        try { 

            $obj = json_decode($JSONData);
            $this->setId_tipo_riesgo($obj->id);
            $this->setMaximo($obj->maximo);
            $this->setMinimo($obj->minimo);
            $this->setFechaVigencia($obj->fecha_vigencia);

            $campos_valores = 
            array( "minimo"=>$this->getMinimo(),
                    "maximo"=>$this->getMaximo(),
                    "fecha_vigencia"=>$this->getFechaVigencia());

            $campos_valores_where = 
            array(  "id_tipo_riesgo"=>$this->getId_tipo_riesgo());

            $this->update("tipo_riesgo", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se hs actualizado exitosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }


    public function listar(){
        try {
            $sql = "SELECT id_tipo_riesgo, COALESCE(maximo, '1.000') as maximo, minimo,fn_fecha(fecha_vigencia::date) as fecha_vigencia, descripcion, indicador_color as color FROM tipo_riesgo WHERE estado_mrcb = 1 ORDER BY id_tipo_riesgo";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function leerDatos($id){
        try {
            $sql = "SELECT id_tipo_riesgo as id, descripcion, minimo,  COALESCE(maximo, '1.000') as maximo, fecha_vigencia::date as fecha_Vigencia 
                    FROM tipo_riesgo WHERE id_tipo_riesgo = :0";
            $resultado = $this->consultarFila($sql,array($id));
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function darBaja() {
        $this->beginTransaction();
        try {            
            $campos_valores = 
            array(  "estado_mrcb"=>$this->getEstado_mrcb());

            $campos_valores_where = 
            array(  "id_tipo_riesgo"=>$this->getId_tipo_riesgo());

            $this->update("tipo_riesgo", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha eliminado exitosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

}