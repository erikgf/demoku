<?php

require_once '../datos/Conexion.clase.php';

class Cargo extends Conexion {
    private $id_cargo;
    private $descripcion;
    private $estado_mrcb;

    public function getid_cargo()
    {
        return $this->id_cargo;
    }
    
    public function setid_cargo($id_cargo)
    {
        $this->id_cargo = $id_cargo;
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

    private function verificarRepetidoAgregar(){
        $sql = "SELECT COUNT(descripcion) > 0 FROM cargo WHERE upper(descripcion) = upper(:0)  AND estado_mrcb = 1";
        return $this->consultarValor($sql, [$this->getDescripcion()]);
    }

    private function verificarRepetidoEditar(){
        $sql = "SELECT COUNT(descripcion) > 0 FROM cargo WHERE upper(descripcion) = upper(:0) AND id_cargo <>:1  AND estado_mrcb = 1";
        return $this->consultarValor($sql, [$this->getDescripcion(), $this->getid_cargo()]);
    }

    public function agregar($JSONData) {
        $this->beginTransaction();
        try {            

            $obj = json_decode($JSONData);
            $this->setDescripcion($obj->descripcion);

            if ($this->verificarRepetidoAgregar()){
                return array("rpt"=>false,"msj"=>"Ya existe este cargo");
            }

            $campos_valores = 
            array(  "descripcion"=>$this->getDescripcion());

            $this->insert("cargo", $campos_valores);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha agregado exitosamente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function editar($JSONData) {
        $this->beginTransaction();
        try {            

            $obj = json_decode($JSONData);
            $this->setid_cargo($obj->id);
            $this->setDescripcion($obj->descripcion);
            
            if ($this->verificarRepetidoEditar()){
                return array("rpt"=>false,"msj"=>"Ya existe este cargo");
            }

            $campos_valores = 
            array(  "descripcion"=>$this->getDescripcion());

            $campos_valores_where = 
            array(  "id_cargo"=>$this->getid_cargo());

            $this->update("cargo", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha actualizado exitosamente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }


    public function listar(){
        try {
            $sql = "SELECT * FROM cargo WHERE estado_mrcb = 1";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function leerDatos($id){
        try {
            $sql = "SELECT id_cargo as id, descripcion FROM cargo WHERE id_cargo = :0";
            $resultado = $this->consultarFila($sql,array($id));
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function darBaja($id) {
        $this->beginTransaction();
        try {            
            $this->setid_cargo($id);    

            $campos_valores = 
            array(  "estado_mrcb"=>0);

            $campos_valores_where = 
            array(  "id_cargo"=>$this->getid_cargo());

            $this->update("cargo", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha eliminado exitosamente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function llenarCB(){
        try {
            $sql = "SELECT * FROM cargo WHERE estado_mrcb = 1";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

}