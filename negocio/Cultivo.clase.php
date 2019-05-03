<?php

require_once '../datos/Conexion.clase.php';

class Cultivo extends Conexion {
    private $cod_cultivo;
    private $nombre;
    private $estado_mrcb;

    public function getCodCultivo()
    {
        return $this->cod_cultivo;
    }
    
    
    public function setCodCultivo($cod_cultivo)
    {
        $this->cod_cultivo = $cod_cultivo;
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

    public function getEstadoMrcb()
    {
        return $this->estado_mrcb;
    }
    
    
    public function setEstadoMrcb($estado_mrcb)
    {
        $this->estado_mrcb = $estado_mrcb;
        return $this;
    }

    public function darBaja(){
        try {

            $campos_valores = ["estado_mrcb"=>"0"];
            $campos_valores_where = ["cod_cultivo"=>$this->getCodCultivo()];

            $this->update($campos_valores, $campos_valores_where);

            return ["rpt"=>true, "msj"=>"Registro dado de baja correctamente."];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerEditar(){
        try {

             $sql = "SELECT cod_cultivo, 
                        nombre
                        FROM cultivo
                        WHERE estado_mrcb AND ca.cod_cultivo = :0";

            $data = $this->consultarFila($sql, $this->getCodCultivo());

            return ["rpt"=>true, "data"=>$data];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    private function seter($tipoAccion){
        //TipoAccion => + agregar, * editar, - eliminar
        $campos_valores = []; 
        $campos_valores_where = [];

        if ($tipoAccion != "-"){
            $campos_valores = ["nombre"=>$this->getNombre()];

            if ($tipoAccion == "+"){
                $campos_valores["cod_cultivo"] = $this->getCodCultivo();
            }
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_cultivo"=>$this->getCodCultivo()];
            if ($tipoAccion == "-"){
                $campos_valores = ["estado_mrcb"=>"0"];
            }
        }

        $campos = ["valores"=>$campos_valores,"valores_where"=>$campos_valores_where];
        return $campos;
    }

    public function agregar() {
        $this->beginTransaction();
        try {        
            $objVerificar = $this->verificarRepetidoAgregar(); 
            if (!$objVerificar["r"]){
                return $objVerificar;
            }

            $this->setCodCultivo($this->consultarValor("SELECT COALESCE(MAX(cod_cultivo)+1, 1) FROM cultivo"));

            $campos = $this->seter("+");

            $this->insert($this->tbl, $campos["valores"]);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha registrado exitosamente");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function editar() {
        $this->beginTransaction();
        try { 

            $objVerificar = $this->verificarRepetidoEditar(); 
            if (!$objVerificar["r"]){
                return $objVerificar;
            }

            $campos = $this->seter("*");
            $this->update($this->tbl, $campos["valores"], $campos["valores_where"]);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha editado exitosamente");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

     private function verificarRepetidoAgregar(){
        if ($this->getNombre() != NULL && $this->getNombre() != ""){
            $sql = "SELECT COUNT(descripcion) > 0 FROM ".$this->tbl." WHERE descripcion = :0 AND estado_mrcb = 1";
            $repetido = $this->consultarValor($sql, [$this->getNombre()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Descripción de cultivo ya existente."];
            }
        }

        return ["r"=>true, "msj"=>""];
    }

    private function verificarRepetidoEditar(){

        if ($this->getNombre() != NULL && $this->getNombre() != ""){
            $sql = "SELECT COUNT(nombre) > 0 FROM ".$this->tbl." WHERE nombre = :0 AND estado_mrcb = 1  AND cod_cultivo <>:1";
            $repetido = $this->consultarValor($sql, [$this->getNombre(),$this->getCodCultivo()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Descripción de cultivo ya existente."];
            }
        }

        return ["r"=>true, "msj"=>""];
    }

    public function llenarCB(){
        try {
            $sql = "SELECT cod_cultivo as codigo, nombre as descripcion FROM cultivo WHERE estado_mrcb = 1";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerVariedad(){
        try {
            $sql = "SELECT cod_variedad as codigo, nombre as descripcion FROM variedad WHERE cod_cultivo = :0 AND estado_mrcb = 1";
            $resultado = $this->consultarFilas($sql, [$this->getCodCultivo()]);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    
}