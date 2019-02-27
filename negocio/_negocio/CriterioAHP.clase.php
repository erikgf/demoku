<?php

require_once '../datos/Conexion.clase.php';

class CriterioAHP extends Conexion {
    private $id_criterio_ahp;
    private $nombre_criterio;
    private $estado_mrcb;

    public function getid_criterio_ahp()
    {
        return $this->id_criterio_ahp;
    }
    
    public function setid_criterio_ahp($id_criterio_ahp)
    {
        $this->id_criterio_ahp = $id_criterio_ahp;
        return $this;
    }

    public function getnombre_criterio()
    {
        return $this->nombre_criterio;
    }
    
    public function setnombre_criterio($nombre_criterio)
    {
        $this->nombre_criterio = $nombre_criterio;
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
            array(  "nombre_criterio"=>$this->getnombre_criterio());

            $this->insert("criterio_ahp", $campos_valores);

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
            array(  "nombre_criterio"=>$this->getnombre_criterio() );

            $campos_valores_where = 
            array(  "id_criterio_ahp"=>$this->getid_criterio_ahp());

            $this->update("criterio_ahp", $campos_valores,$campos_valores_where);

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
            $sql = "SELECT * FROM criterio_ahp WHERE estado_mrcb = 1";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT * FROM criterio_ahp WHERE estado_mrcb = 1 AND id_criterio_ahp = :0";
            $resultado = $this->consultarFila($sql,array($this->getid_criterio_ahp()));
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
            array(  "id_criterio_ahp"=>$this->getid_criterio_ahp());

            $this->update("criterio_ahp", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se inactivado existosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

}