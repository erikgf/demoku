<?php

require_once '../datos/Conexion.clase.php';

class VariableGeneral extends Conexion {
    private $id_variable_general;
    private $nombre;
    private $descripcion;
    private $valor;
    private $estado_mrcb;

    public function getId_variable_general()
    {
        return $this->id_variable_general;
    }
    
    public function setId_variable_general($id_variable_general)
    {
        $this->id_variable_general = $id_variable_general;
        return $this;
    }

    public function getNombre()
    {
        return $this->nombre;
    }
    
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
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
    public function getValor()
    {
        return $this->valor;
    }
    
    public function setValor($valor)
    {
        $this->valor = $valor;
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
        $sql = "SELECT COUNT(descripcion) > 0 FROM variable_general WHERE upper(descripcion) = upper(:0)  AND estado_mrcb = 1";
        return $this->consultarValor($sql, [$this->getDescripcion()]);
    }

    private function verificarRepetidoEditar(){
        $sql = "SELECT COUNT(descripcion) > 0 FROM variable_general WHERE upper(descripcion) = upper(:0) AND id_variable_general <>:1  AND estado_mrcb = 1";
        return $this->consultarValor($sql, [$this->getDescripcion(), $this->getid_variable_general()]);
    }

    public function agregar($JSONData) {
        $this->beginTransaction();
        try {            

            $obj = json_decode($JSONData);
            $this->setNombre($obj->nombre);
            $this->setDescripcion($obj->descripcion);
            $this->setValor($obj->valor);

            if ($this->verificarRepetidoAgregar()){
                return array("rpt"=>false,"msj"=>"Ya existe esta variable general.");
            }

            $campos_valores = 
            array(  "nombre"=>$this->getNombre(),
                    "descripcion"=>$this->getDescripcion(),
                    "valor"=>$this->getValor());

            $this->insert("variable_general", $campos_valores);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se agregado exitosamente.");
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
            $this->setId_variable_general($obj->id);
            $this->setNombre($obj->nombre);
            $this->setDescripcion($obj->descripcion);
            $this->setValor($obj->valor);

            if ($this->verificarRepetidoEditar()){
                return array("rpt"=>false,"msj"=>"Ya existe esta variable general.");
            }

            $campos_valores = 
            array(  "nombre"=>$this->getNombre(),
                    "descripcion"=>$this->getDescripcion(),
                    "valor"=>$this->getValor());

            $campos_valores_where = 
            array(  "id_variable_general"=>$this->getId_variable_general());

            $this->update("variable_general", $campos_valores,$campos_valores_where);

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
            $sql = "SELECT * FROM variable_general WHERE estado_mrcb = 1 ORDER BY nombre";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function leerDatos($id){
        try {
            $sql = "SELECT id_variable_general as id, valor, nombre, descripcion FROM variable_general WHERE id_variable_general = :0";
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
            $this->setId_variable_general($id);    

            $campos_valores = 
            array(  "estado_mrcb"=>0);       

            $campos_valores_where = 
            array(  "id_variable_general"=>$this->getId_variable_general());

            $this->update("variable_general", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha eliminado existosamente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

}