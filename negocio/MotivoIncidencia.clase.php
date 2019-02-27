<?php

require_once '../datos/Conexion.clase.php';

class MotivoIncidencia extends Conexion {
    private $id_motivo_incidencia;
    private $descripcion;
    private $estado_mrcb;

    public function getid_motivo_incidencia()
    {
        return $this->id_motivo_incidencia;
    }
    
    public function setid_motivo_incidencia($id_motivo_incidencia)
    {
        $this->id_motivo_incidencia = $id_motivo_incidencia;
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
        $sql = "SELECT COUNT(descripcion) > 0 FROM motivo_incidencia WHERE upper(descripcion) = upper(:0)  AND estado_mrcb = 1";
        return $this->consultarValor($sql, [$this->getDescripcion()]);
    }

    private function verificarRepetidoEditar(){
        $sql = "SELECT COUNT(descripcion) > 0 FROM motivo_incidencia WHERE upper(descripcion) = upper(:0) AND id_motivo_incidencia <>:1  AND estado_mrcb = 1";
        return $this->consultarValor($sql, [$this->getDescripcion(), $this->getid_motivo_incidencia()]);
    }

    public function agregar($JSONData) {
        $this->beginTransaction();
        try {            

            $obj = json_decode($JSONData);
            $this->setDescripcion($obj->descripcion);

            if ($this->verificarRepetidoAgregar()){
                return array("rpt"=>false,"msj"=>"Ya existe este motivo de incidencia");
            }      

            $campos_valores = 
            array(  "descripcion"=>$this->getDescripcion());

            $this->insert("motivo_incidencia", $campos_valores);

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
            $this->setid_motivo_incidencia($obj->id);
            $this->setDescripcion($obj->descripcion);

            if ($this->verificarRepetidoEditar()){
                return array("rpt"=>false,"msj"=>"Ya existe este motivo de incidencia");
            }  

            $campos_valores = 
            array(  "descripcion"=>$this->getDescripcion() );

            $campos_valores_where = 
            array(  "id_motivo_incidencia"=>$this->getid_motivo_incidencia());

            $this->update("motivo_incidencia", $campos_valores,$campos_valores_where);

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
            $sql = "SELECT id_motivo_incidencia, descripcion FROM motivo_incidencia WHERE estado_mrcb = 1";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function leerDatos($id){
        try {
            $sql = "SELECT id_motivo_incidencia as id, descripcion FROM motivo_incidencia WHERE id_motivo_incidencia = :0";
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

            $this->setid_motivo_incidencia($id);

            $campos_valores = 
            array(  "estado_mrcb"=> 0 );

            $campos_valores_where = 
            array(  "id_motivo_incidencia"=>$this->getid_motivo_incidencia());

            $this->update("motivo_incidencia", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha eliminado exitosamente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

}