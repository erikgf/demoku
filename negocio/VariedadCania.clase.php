<?php

require_once '../datos/Conexion.clase.php';

class VariedadCania extends Conexion {
    private $id_variedad_cania;
    private $descripcion;
    private $estado_mrcb;

    public function getId_variedad_cania()
    {
        return $this->id_variedad_cania;
    }
    
    public function setId_variedad_cania($id_variedad_cania)
    {
        $this->id_variedad_cania = $id_variedad_cania;
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

            $this->insert("variedad_caña", $campos_valores);

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
            array(  "id_variedad_caña"=>$this->getId_variedad_cania());

            $this->update("variedad_caña", $campos_valores,$campos_valores_where);

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
            $sql = "SELECT * FROM variedad_caña WHERE estado_mrcb = 1 ORDER BY 1";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function leerDatos($id){
        try {
            $sql = "SELECT id_variedad_caña as id, descripcion FROM variedad_caña WHERE id_variedad_caña = :0";
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
            array("estado_mrcb"=>$this->getEstado_mrcb());

            $campos_valores_where = 
            array("id_variedad_caña"=>$this->getId_variedad_cania());

            $this->update("variedad_caña", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se inactivado existosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function llenarCB(){
        try {
            $sql = "SELECT id_variedad_caña, descripcion FROM variedad_caña WHERE estado_mrcb = 1";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

}