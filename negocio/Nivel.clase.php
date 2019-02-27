<?php

require_once '../datos/Conexion.clase.php';

class Nivel extends Conexion {
    private $id_nivel;
    private $descripcion;
    private $estado_mrcb;

    public function getId_nivel()
    {
        return $this->id_nivel;
    }
    
    public function setId_nivel($id_nivel)
    {
        $this->id_nivel = $id_nivel;
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

            $this->insert("nivel", $campos_valores);

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
            array(  "descripcion"=>$this->getDescripcion() );

            $campos_valores_where = 
            array(  "id_nivel"=>$this->getId_nivel());

            $this->update("nivel", $campos_valores,$campos_valores_where);

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
            $sql = "SELECT * FROM nivel WHERE estado_mrcb = 1";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT * FROM nivel WHERE estado_mrcb = 1 AND id_nivel = :0";
            $resultado = $this->consultarFila($sql,array($this->getId_nivel()));
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
            array(  "id_nivel"=>$this->getId_nivel());



            $this->update("nivel", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se inactivado existosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

}