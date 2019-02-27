<?php

require_once '../datos/Conexion.clase.php';

class TipoRiego extends Conexion {
    private $id_tipo_riego;
    private $descripcion;
    private $estado_mrcb;

    public function getid_tipo_riego()
    {
        return $this->id_tipo_riego;
    }
    
    public function setid_tipo_riego($id_tipo_riego)
    {
        $this->id_tipo_riego = $id_tipo_riego;
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

    public function getEstado_mrcb()
    {
        return $this->estado_mrcb;
    }
    
    public function setEstado_mrcb($estado_mrcb)
    {
        $this->estado_mrcb = $estado_mrcb;
        return $this;
    }

    public function agregar() {
        $this->beginTransaction();
        try {            
            $campos_valores = 
            array(  "descripcion"=>$this->getDescripcion());
            

            $this->insert("tipo_riego", $campos_valores);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se agregado exitosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function editar() {
        $this->beginTransaction();
        try { 
            $campos_valores = 
            array(  "descripcion"=>$this->getDescripcion());

            $campos_valores_where = 
            array(  "id_tipo_riego"=>$this->getid_tipo_riego());

            $this->update("tipo_riego", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se actualizado exitosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }


    public function listar(){
        try {
            $sql = "SELECT * FROM tipo_riego WHERE estado_mrcb = 1 ORDER BY id_tipo_riego";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT id_tipo_riego as id, descripcion FROM tipo_riego WHERE id_tipo_riego = :0";
            $resultado = $this->consultarFila($sql,array($this->getid_tipo_riego()));
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
            array(  "id_tipo_riego"=>$this->getid_tipo_riego());

            $this->update("tipo_riego", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se inactivado existosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

}