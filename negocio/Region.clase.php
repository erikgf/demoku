<?php

require_once '../datos/Conexion.clase.php';

class Region extends Conexion {
    private $cod_region;
    private $descripcion;
    private $estado;

    private $tbl = "region";

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

    public function getCodRegion()
    {
        return $this->cod_region;
    }
    
    
    public function setCodRegion($cod_region)
    {
        $this->cod_region = $cod_region;
        return $this;
    }

    public function darBaja(){
        try {

            $campos_valores = ["estado_activo"=>"false"];
            $campos_valores_where = ["cod_region"=>$this->getCodRegion()];

            $this->update($this->tbl, $campos_valores, $campos_valores_where);

            return ["rpt"=>true, "msj"=>"Registro dado de baja correctamente."];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerEditar(){
        try {

             $sql = "SELECT cod_region, 
                        descripcion
                        FROM region
                        WHERE estado_activo AND  cod_region = :0";

            $data = $this->consultarFila($sql, $this->getCodRegion());

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
            $campos_valores = ["descripcion"=>$this->getDescripcion()];

            if ($tipoAccion == "+"){
                $campos_valores["cod_region"] = $this->getCodRegion();
            }
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_region"=>$this->getCodRegion()];
            if ($tipoAccion == "-"){
                $campos_valores = ["estado_activo"=>"false"];
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

            $this->setCodRegion($this->consultarValor("SELECT COALESCE(MAX(cod_region)+1, 1) FROM region"));

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
        if ($this->getDescripcion() != NULL && $this->getDescripcion() != ""){
            $sql = "SELECT COUNT(descripcion) > 0 FROM ".$this->tbl." WHERE descripcion = :0 AND estado_activo";
            $repetido = $this->consultarValor($sql, [$this->getDescripcion()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Descripción de región ya existente."];
            }
        }

        return ["r"=>true, "msj"=>""];
    }

    private function verificarRepetidoEditar(){

        if ($this->getDescripcion() != NULL && $this->getDescripcion() != ""){
            $sql = "SELECT COUNT(descripcion) > 0 FROM ".$this->tbl." WHERE descripcion = :0 AND estado_activo  AND cod_region <>:1";
            $repetido = $this->consultarValor($sql, [$this->getDescripcion(),$this->getCodRegion()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Descripción de región ya existente."];
            }
        }

        return ["r"=>true, "msj"=>""];
    }

    public function obtenerRegiones(){
        try {

            $sql  =" SELECT cod_region as codigo, descripcion FROM region WHERE estado_activo ORDER BY descripcion";
            $res = $this->consultarFilas($sql);

            return ["rpt"=>true, "data"=>$res];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerDatosBase(){
        try {

            $sql  =" SELECT cod_region as codigo, descripcion FROM region WHERE estado_activo ORDER BY descripcion";
            $regiones = $this->consultarFilas($sql);

            $sql  =" SELECT cod_cultivo as codigo, nombre as descripcion FROM cultivo WHERE estado_mrcb = 1 ORDER BY nombre";
            $cultivos = $this->consultarFilas($sql);

            return ["rpt"=>true, "data"=>["regiones"=>$regiones,"cultivos"=>$cultivos]];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }


     public function listar(){
        try {

            $sql  =" SELECT cod_region, descripcion FROM region WHERE estado_activo ORDER BY descripcion";
            $res = $this->consultarFilas($sql);

            return ["rpt"=>true, "data"=>$res];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }
    


}

    