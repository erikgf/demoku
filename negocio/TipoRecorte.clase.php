<?php

require_once '../datos/Conexion.clase.php';

class TipoRecorte extends Conexion {
    private $id_tipo_recorte;
    private $descripcion;
    private $estado_mrcb;

    public function getid_tipo_recorte()
    {
        return $this->id_tipo_recorte;
    }
    
    public function setid_tipo_recorte($id_tipo_recorte)
    {
        $this->id_tipo_recorte = $id_tipo_recorte;
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
        $sql = "SELECT COUNT(descripcion) > 0 FROM tipo_recorte WHERE upper(descripcion) = upper(:0)  AND estado_mrcb = 1";
        return $this->consultarValor($sql, [$this->getDescripcion()]);
    }

    private function verificarRepetidoEditar(){
        $sql = "SELECT COUNT(descripcion) > 0 FROM tipo_recorte WHERE upper(descripcion) = upper(:0) AND id_tipo_recorte <>:1  AND estado_mrcb = 1";
        return $this->consultarValor($sql, [$this->getDescripcion(), $this->getid_tipo_recorte()]);
    }

    public function agregar($JSONData) {
        $this->beginTransaction();
        try {            

            $obj = json_decode($JSONData); 
            $this->setDescripcion($obj->descripcion);

            if ($this->verificarRepetidoAgregar()){
                return array("rpt"=>false,"msj"=>"Ya existe este tipo de recorte");
            }

            $campos_valores = 
            array(  "descripcion"=>$this->getDescripcion());

            $this->insert("tipo_recorte", $campos_valores);

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
            $this->setid_tipo_recorte($obj->id);
            $this->setDescripcion($obj->descripcion);

            if ($this->verificarRepetidoEditar()){
                return array("rpt"=>false,"msj"=>"Ya existe este tipo de recorte");
            }  

            $campos_valores = 
            array(  "descripcion"=>$this->getDescripcion() );

            $campos_valores_where = 
            array(  "id_tipo_recorte"=>$this->getid_tipo_recorte());

            $this->update("tipo_recorte", $campos_valores,$campos_valores_where);

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
            $sql = "SELECT id_tipo_recorte, descripcion FROM tipo_recorte WHERE estado_mrcb = 1";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function leerDatos($id){
        try {
            $sql = "SELECT descripcion, id_tipo_recorte as id FROM tipo_recorte WHERE id_tipo_recorte = :0";
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
            $this->setid_tipo_recorte($id);

            $campos_valores = 
            array(  "estado_mrcb"=>0);

            $campos_valores_where = 
            array(  "id_tipo_recorte"=>$this->getid_tipo_recorte());

            $this->update("tipo_recorte", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha eliminado exitosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

}