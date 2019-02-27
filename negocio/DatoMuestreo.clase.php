<?php

require_once '../datos/Conexion.clase.php';

class DatoMuestreo extends Conexion {
    private $idDatoMuestreo;
    private $descripcion;
    private $estado;

    private $tbl = "dato_muestreo";

    public function getIdDatoMuestreo()
    {
        return $this->idDatoMuestreo;
    }
    
    public function setIdDatoMuestreo($idDatoMuestreo)
    {
        $this->idDatoMuestreo = $idDatoMuestreo;
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

    public function getEstado()
    {
        return $this->estado;
    }
    
    
    public function setEstado($estado)
    {
        $this->estado = $estado;
        return $this;
    }
    
    public function agregar() {      
        $this->beginTransaction();
        try {
            
            $campos_valores = 
            array(  "nombres"=>strtoupper($this->getNombres()),
                    "direccion" =>strtoupper($this->getDireccion()),
                    "telefono"=>strtoupper($this->getTelefono()),
                    "referencia"=>strtoupper($this->getReferencia()));

            $this->insert($this->tbl, $campos_valores);
            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha agregado el cliente correctamente.");
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
            array(  "nombres"=>strtoupper($this->getNombres()),
                    "direccion" =>strtoupper($this->getDireccion()),
                    "telefono"=>strtoupper($this->getTelefono()),
                    "referencia"=>strtoupper($this->getReferencia()));

            $campos_valores_where = 
            array(  "id_cliente"=>$this->getIdCliente());

            $this->update($this->tbl, $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha actualizado el cliente correctamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function eliminar() {
        $this->beginTransaction();
        try {            
            $campos_valores = 
            array(  "estado_mrcb"=>$this->getEstado_mrcb());

            $campos_valores_where = 
            array(  "cod_actividad"=>$this->getCod_actividad());

            $this->update($this->tb, $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se anulado existosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT * FROM $this->tbl WHERE id_cliente = :0";
            $resultado = $this->consultarFila($sql, array($this->getIdCliente()));
            return array("rpt"=>true,"data"=>$resultado);

        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function listar($buscando){
        try {
            $sql = "SELECT * FROM $this->tbl  WHERE telefono LIKE '%".$buscando."%' OR nombres LIKE '%".$buscando."%'";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function listarAppMovil(){
        try {
            $sql = "SELECT id_dato_muestreo, descripcion FROM $this->tbl  
                    WHERE estado = 'A'";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    
}