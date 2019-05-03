<?php

require_once '../datos/Conexion.clase.php';

class Perfil extends Conexion {
    private $cod_perfil;
    private $descripcion;
    private $estado_acceso;
    private $estado_mrcb;

    private $tbl = "perfil";

    public function getCodPerfil()
    {
        return $this->cod_perfil;
    }
    
    public function setCodPerfil($cod_perfil)
    {
        $this->cod_perfil = $cod_perfil;
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

    public function getEstadoAcceso()
    {
        return $this->estado_acceso;
    }
    
    
    public function setEstadoAcceso($estado_acceso)
    {
        $this->estado_acceso = $estado_acceso;
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

            $campos_valores = ["estado_mrcb"=>"false"];
            $campos_valores_where = ["cod_perfil"=>$this->getCodPerfil()];

            $this->update($this->tbl, $campos_valores, $campos_valores_where);

            return ["rpt"=>true, "msj"=>"Registro dado de baja correctamente."];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerEditar(){
        try {

             $sql = "SELECT cod_perfil, 
                        descripcion,
                        estado_acceso
                        FROM perfil
                        WHERE estado_mrcb AND  cod_perfil = :0";

            $data = $this->consultarFila($sql, $this->getCodPerfil());

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
            $campos_valores = ["descripcion"=>$this->getDescripcion(),
                                "estado_acceso"=>$this->getEstadoAcceso()];

            if ($tipoAccion == "+"){
                $campos_valores["cod_perfil"] = $this->getCodPerfil();
            }
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_perfil"=>$this->getCodPerfil()];
            if ($tipoAccion == "-"){
                $campos_valores = ["estado_mrcb"=>"false"];
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

            $this->setCodPerfil($this->consultarValor("SELECT COALESCE(MAX(cod_perfil)+1, 1) FROM perfil"));

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
            $sql = "SELECT COUNT(descripcion) > 0 FROM ".$this->tbl." WHERE descripcion = :0 AND estado_mrcb";
            $repetido = $this->consultarValor($sql, [$this->getDescripcion()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Perfil ya existente."];
            }
        }

        return ["r"=>true, "msj"=>""];
    }

    private function verificarRepetidoEditar(){
        if ($this->getDescripcion() != NULL && $this->getDescripcion() != ""){
            $sql = "SELECT COUNT(descripcion) > 0 FROM ".$this->tbl." WHERE descripcion = :0 AND estado_mrcb AND cod_perfil <>:1";
            $repetido = $this->consultarValor($sql, [$this->getDescripcion(),$this->getCodPerfil()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Perfil ya existente."];
            }
        }

        return ["r"=>true, "msj"=>""];
    }

    public function listar(){
        try {

            $sql  ="SELECT cod_perfil,  descripcion,
                            (CASE estado_acceso WHEN '0' THEN 'NO ACCESO' WHEN '1' THEN 'APP MÓVIL' ELSE 'MÓVIL + WEB' END) as estado_acceso
                             FROM perfil 
                             WHERE estado_mrcb ORDER BY descripcion";
            $res = $this->consultarFilas($sql);

            return ["rpt"=>true, "data"=>$res];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerPerfiles(){
        try {

            $sql  ="SELECT cod_perfil as codigo,  descripcion
                             FROM perfil 
                             WHERE estado_mrcb ORDER BY descripcion";
            $res = $this->consultarFilas($sql);

            return ["rpt"=>true, "data"=>$res];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }
}

    