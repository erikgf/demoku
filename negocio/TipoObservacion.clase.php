<?php

require_once '../datos/Conexion.clase.php';

class TipoObservacion extends Conexion {
    private $id_tipo_observacion;
    private $descripcion;
    private $estado_activacion;

    public function getid_tipo_observacion()
    {
        return $this->id_tipo_observacion;
    }
    
    public function setid_tipo_observacion($id_tipo_observacion)
    {
        $this->id_tipo_observacion = $id_tipo_observacion;
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

    public function getestado_activacion()
    {
        return $this->estado_activacion;
    }
    
    public function setestado_activacion($estado_activacion)
    {
        $this->estado_activacion = $estado_activacion;
        return $this;
    }

    private function verificarRepetidoAgregar(){
        $sql = "SELECT COUNT(descripcion) > 0 FROM tipo_observacion WHERE upper(descripcion) = upper(:0) AND estado_mrcb = 1";
        return $this->consultarValor($sql, [$this->getDescripcion()]);
    }

    private function verificarRepetidoEditar(){
        $sql = "SELECT COUNT(descripcion) > 0 FROM tipo_observacion WHERE upper(descripcion) = upper(:0) AND id_tipo_observacion <>:1  AND estado_mrcb = 1";
        return $this->consultarValor($sql, [$this->getDescripcion(), $this->getid_tipo_observacion()]);
    }

    public function agregar($JSONData) {
        $this->beginTransaction();
        try {            

            $obj = json_decode($JSONData);
            $this->setDescripcion($obj->descripcion);   

            if ($this->verificarRepetidoAgregar()){
                return array("rpt"=>false,"msj"=>"Ya existe este tipo de observación");
            }

            $campos_valores = 
            array(  "descripcion"=>$this->getDescripcion());

            $this->insert("tipo_observacion", $campos_valores);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha agregado exitosamente");
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
            $this->setid_tipo_observacion($obj->id);
            $this->setDescripcion($obj->descripcion);

            if ($this->verificarRepetidoEditar()){
                return array("rpt"=>false,"msj"=>"Ya existe este tipo de observación");
            }

            $campos_valores = 
            array(  "descripcion"=>$this->getDescripcion() );

            $campos_valores_where = 
            array(  "id_tipo_observacion"=>$this->getid_tipo_observacion());

            $this->update("tipo_observacion", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha actualizado exitosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function listar(){
        try {
            $sql = "SELECT id_tipo_observacion, descripcion FROM tipo_observacion WHERE estado_mrcb = 1 ORDER BY 1";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function leerDatos($id){
        try {
            $sql = "SELECT id_tipo_observacion as id, descripcion FROM tipo_observacion 
                WHERE id_tipo_observacion = :0";
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
            $this->setid_tipo_observacion($id);

            $campos_valores = 
            array(  "estado_mrcb"=>0);

            $campos_valores_where = 
            array(  "id_tipo_observacion"=>$this->getid_tipo_observacion());

            $this->update("tipo_observacion", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha eliminado exitosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function cargarCombo()
    {
        try {            
            $data = $this->consultarFilas("SELECT id_tipo_observacion, descripcion FROM tipo_observacion WHERE estado_activacion = 'A'");
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

}