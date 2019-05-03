<?php

require_once '../datos/Conexion.clase.php';

class Campo extends Conexion {
    private $cod_campo;
    private $cod_nisira;
    private $descripcion;
    private $area;
    private $estado_mrcb;
    private $cod_region;

    public $tbl = "campo";

    public function getCodCampo()
    {
        return $this->cod_campo;
    }
    
    
    public function setCodCampo($cod_campo)
    {
        $this->cod_campo = $cod_campo;
        return $this;
    }

    public function getCodNisira()
    {
        return $this->cod_nisira;
    }
    
    
    public function setCodNisira($cod_nisira)
    {
        $this->cod_nisira = $cod_nisira;
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

    public function getEstadoMrcb()
    {
        return $this->estado_mrcb;
    }
    
    
    public function setEstadoMrcb($estado_mrcb)
    {
        $this->estado_mrcb = $estado_mrcb;
        return $this;
    }

    public function getArea()
    {
        return $this->area;
    }
    
    
    public function setArea($area)
    {
        $this->area = $area;
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

    public function obtenerCampos(){
        try {

            $sql  =" SELECT cod_campo as codigo, nombre_campo as descripcion FROM campo WHERE estado_mrcb AND cod_region = :0";
            $res = $this->consultarFilas($sql, $this->getCodRegion());

            return ["rpt"=>true, "data"=>$res];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function darBaja(){
        try {

            $campos_valores = ["estado_mrcb"=>"false"];
            $campos_valores_where = ["cod_campo"=>$this->getCodCampo()];

            $this->update("campo",$campos_valores, $campos_valores_where);

            return ["rpt"=>true, "msj"=>"Registro dado de baja correctamente."];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerEditar(){
        try {
             $sql = "SELECT cod_campo, 
                        cod_nisira as idconsumidor,
                        nombre_campo as descripcion,
                        cod_region,
                        area
                        FROM campo ca
                        WHERE estado_mrcb AND ca.cod_campo = :0";

            $data = $this->consultarFila($sql, $this->getCodCampo());

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
            $campos_valores = ["cod_nisira"=>$this->getCodNisira(),
                                "nombre_campo"=>$this->getDescripcion(),
                                "cod_region"=>$this->getCodRegion(),
                                "area"=>$this->getArea()];      

            if ($tipoAccion == "+"){
                $campos_valores["cod_campo"] = $this->getCodCampo();
            }
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_campo"=>$this->getCodCampo()];

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

            $this->setCodCampo($this->consultarValor("SELECT COALESCE(MAX(cod_campo)+1, 1) FROM campo"));

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
        if ($this->getCodNisira() != NULL && $this->getCodNisira() != ""){
            $sql = "SELECT COUNT(cod_nisira) > 0 FROM ".$this->tbl." WHERE cod_nisira = :0 AND estado_mrcb";
            $repetido = $this->consultarValor($sql, [$this->getCodNisira()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"ID Consumidor ya existente."];
            }
        }
        
        if ($this->getDescripcion() != NULL && $this->getDescripcion() != ""){
            $sql = "SELECT COUNT(nombre_campo) > 0 FROM ".$this->tbl." WHERE nombre_campo = :0 AND estado_mrcb";
            $repetido = $this->consultarValor($sql, [$this->getDescripcion()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Nombre de campo ya existente."];
            }
        }

        return ["r"=>true, "msj"=>""];
    }

    private function verificarRepetidoEditar(){

        if ($this->getCodNisira() != NULL && $this->getCodNisira() != ""){
            $sql = "SELECT COUNT(cod_nisira) > 0 FROM ".$this->tbl." WHERE cod_nisira = :0 AND estado_mrcb  AND cod_campo <>:1";
            $repetido = $this->consultarValor($sql, [$this->getCodNisira(),$this->getCodCampo()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"ID Consumidor ya existente."];
            }
        }
        
        if ($this->getDescripcion() != NULL && $this->getDescripcion() != ""){
            $sql = "SELECT COUNT(nombre_campo) > 0 FROM ".$this->tbl." WHERE nombre_campo = :0 AND estado_mrcb  AND cod_campo <>:1";
            $repetido = $this->consultarValor($sql, [$this->getDescripcion(),$this->getCodCampo()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Nombre de campo ya existente."];
            }
        }

        return ["r"=>true, "msj"=>""];
    }

    public function obtenerDatosCampo(){
        try {

            $sql  ="SELECT cod_nisira as idconsumidor, nombre_campo, area FROM campo WHERE cod_campo = :0";
            $cabecera = $this->consultarFila($sql, $this->getCodCampo());

            $sql = "SELECT s.cod_siembra, 
                        s.cod_nisira as idsiembra,
                        ca.cod_nisira as idconsumidor,
                        to_char(fecha_inicio_siembra,'DD/MM/YYYY')  as inicio_siembra,
                        to_char(fecha_fin_siembra,'DD/MM/YYYY') as  fin_siembra, 
                        v.nombre as variedad,
                        c.nombre as cultivo,
                        (CASE tipo_riego WHEN 1 THEN 'GRAVEDAD' ELSE 'GOTEO' END) as tipo_riego,
                        numero_plantas, camas, rayas,
                        (CASE s.estado_activo WHEN true THEN 'ACTIVO' ELSE 'INACTIVO' END) as estado
                        FROM siembra s
                        LEFT JOIN variedad v  ON v.cod_variedad = s.cod_variedad
                        LEFT JOIN cultivo c  ON c.cod_cultivo = v.cod_cultivo 
                        INNER JOIN campo ca ON ca.cod_campo = s.cod_campo
                        WHERE s.estado_mrcb AND ca.cod_campo = :0
                        ORDER BY s.cod_siembra";

            $siembras = $this->consultarFilas($sql, $this->getCodCampo());

            $sql = "SELECT 
                            c.cod_campaña,
                            c.cod_nisira as idcampaña,
                            s.cod_nisira as idsiembra,
                            cp.cod_nisira as idconsumidor,
                            to_char(fecha_campaña_inicio,'DD/MM/YYYY')  as inicio_campaña,
                            to_char(fecha_campaña_fin,'DD/MM/YYYY') as  fin_campaña,
                            año,
                            descripcion,
                            c.area,
                            (CASE c.estado_activo WHEN true THEN 'ACTIVO' ELSE 'INACTIVO' END) as estado
                            FROM campaña c
                            INNER JOIN siembra s ON s.cod_siembra = c.cod_siembra
                            INNER JOIN campo cp ON s.cod_campo = cp.cod_campo
                            WHERE c.estado_mrcb AND cp.cod_campo = :0
                            ORDER BY c.cod_campaña";

            $campanas = $this->consultarFilas($sql, $this->getCodCampo());

            return ["rpt"=>true, "data"=>["cabecera"=>$cabecera, "siembras"=>$siembras, "campanas"=>$campanas]];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerDatosCampoParcela(){
        try {
            /*otebernb siembras 
                    data
                    -campañas
                    */
            $sql = "SELECT s.cod_siembra as codigo, 
                        s.cod_siembra,
                        s.cod_nisira as descripcion,
                        v.cod_cultivo,
                        v.cod_variedad,
                        tipo_riego
                        FROM siembra s
                        LEFT JOIN variedad v  ON v.cod_variedad = s.cod_variedad
                        WHERE s.estado_mrcb AND s.cod_campo = :0
                        ORDER BY s.cod_siembra";

            $siembras = $this->consultarFilas($sql, $this->getCodCampo());

            foreach ($siembras as $key => $value) {
                $sql = "SELECT 
                            c.cod_campaña as codigo,
                            c.cod_campaña,
                            c.cod_nisira as descripcion,    
                            c.fecha_campaña_inicio as fecha_inicio,
                            c.fecha_campaña_fin as  fecha_fin,
                            c.area
                            FROM campaña c
                            WHERE c.estado_mrcb AND c.cod_siembra = :0
                            ORDER BY c.cod_campaña";

                $campanas = $this->consultarFilas($sql, $this->getCodCampo()); 
                $siembras[$key]["campañas"]   = $campanas;
            }
        

            $sql = "SELECT COALESCE(MAX(cod_siembra), 0) as cod_siembra FROM siembra WHERE estado_mrcb AND estado_activo AND cod_campo = :0";
            $last_cod_siembra = $this->consultarValor($sql, [$this->getCodCampo()]);
            if ($last_cod_siembra == "0"){
                $last_cod_siembra = "";
                $last_cod_campaña = "";
            } else {
                $sql = "SELECT COALESCE(MAX(cod_campaña), 0) as cod_campaña FROM campaña WHERE estado_mrcb AND estado_activo AND cod_siembra = :0";
                $last_cod_campaña = $this->consultarValor($sql, $last_cod_siembra);
            }

            return ["rpt"=>true, "data"=>["siembras"=>$siembras, "last_cod_siembra"=>$last_cod_siembra, "last_cod_campaña"=>$last_cod_campaña]];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }
}

    