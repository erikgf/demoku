<?php

require_once '../datos/Conexion.clase.php';

class Siembra extends Conexion {
    private $cod_siembra;
    private $cod_campo;
    private $cod_nisira;
    private $tipo_riego;
    private $area;
    private $cod_variedad;
    private $fecha_inicio_siembra;
    private $fecha_fin_siembra;
    private $estado_activo;
    private $estado_mrcb;

    public $tbl = "siembra";

    public function getCodSiembra()
    {
        return $this->cod_siembra;
    }
    
    
    public function setCodSiembra($cod_siembra)
    {
        $this->cod_siembra = $cod_siembra;
        return $this;
    }

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

    public function getTipoRiego()
    {
        return $this->tipo_riego;
    }
    
    
    public function setTipoRiego($tipo_riego)
    {
        $this->tipo_riego = $tipo_riego;
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

    public function getFechaInicioSiembra()
    {
        return $this->fecha_inicio_siembra;
    }
    
    
    public function setFechaInicioSiembra($fecha_inicio_siembra)
    {
        $this->fecha_inicio_siembra = $fecha_inicio_siembra;
        return $this;
    }

    public function getFechaFinSiembra()
    {
        return $this->fecha_fin_siembra;
    }
    
    
    public function setFechaFinSiembra($fecha_fin_siembra)
    {
        $this->fecha_fin_siembra = $fecha_fin_siembra;
        return $this;
    }

    public function getCodVariedad()
    {
        return $this->cod_variedad;
    }
    
    
    public function setCodVariedad($cod_variedad)
    {
        $this->cod_variedad = $cod_variedad;
        return $this;
    }

    public function getEstadoActivo()
    {
        return $this->estado_activo;
    }
    
    
    public function setEstadoActivo($estado_activo)
    {
        $this->estado_activo = $estado_activo;
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

    public function listarSiembras(){
        try {
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
            return ["rpt"=>true, "data"=>$siembras];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function darBaja(){
        try {

            $campos_valores = ["estado_mrcb"=>"false"];
            $campos_valores_where = ["cod_siembra"=>$this->getCodSiembra()];
            $this->update("siembra", $campos_valores, $campos_valores_where);

            $objSiembras = $this->obtenerCodCampoYListar();
            if ($objSiembras["rpt"] == false){
                return $objSiembras;
            }
            $siembras = $objSiembras["data"];

            return ["rpt"=>true, "msj"=>"Registro dado de baja correctamente.", "data"=>$siembras];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function finalizar(){
        try {

            $campos_valores = ["estado_activo"=>"false"];
            $campos_valores_where = ["cod_siembra"=>$this->getCodSiembra()];

            $this->update("siembra", $campos_valores, $campos_valores_where);

            $objSiembras = $this->btenerCodCampoYListar();
            if ($objSiembras["rpt"] == false){
                return $objSiembras;
            }
            $siembras = $objSiembras["data"];

            return ["rpt"=>true, "msj"=>"Registro dado de baja correctamente.", "data"=>$siembras];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerEditar(){
        try {
             $sql = "SELECT 
                        cod_siembra,
                        cod_nisira as idsiembra,
                        cod_campo, 
                        tipo_riego,
                        area,
                        fecha_inicio_siembra as inicio_siembra,
                        fecha_fin_siembra as fin_siembra,
                        v.cod_cultivo,
                        si.cod_variedad                
                        FROM siembra si
                        LEFT JOIN variedad v ON v.cod_variedad = si.cod_variedad
                        WHERE si.estado_mrcb AND si.cod_siembra = :0";
            $data = $this->consultarFila($sql, $this->getCodSiembra());

            $lista_variedad = [];
            if ($data != false){                
                $sql = "SELECT cod_variedad as codigo, nombre as descripcion FROm variedad WHERE cod_cultivo = :0 AND estado_mrcb = 1";
                $lista_variedad =  $this->consultarFilas($sql, [$data["cod_cultivo"]]);
            }

            return ["rpt"=>true, "data"=>$data, "lista_variedad"=>$lista_variedad];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    private function seter($tipoAccion){
        //TipoAccion => + agregar, * editar, - eliminar       
        $campos_valores = []; 
        $campos_valores_where = [];

        if ($tipoAccion != "-"){
            $campos_valores = [ "cod_nisira"=>$this->getCodNisira(),
                                "tipo_riego"=>$this->getTipoRiego(),
                                "cod_campo"=>$this->getCodCampo(),
                                "area"=>$this->getArea(),
                                "fecha_inicio_siembra"=>$this->getFechaInicioSiembra(),
                                "fecha_fin_siembra"=>$this->getFechaFinSiembra(),
                                "cod_variedad"=>$this->getCodVariedad()
                                ];      

            if ($tipoAccion == "+"){
                $campos_valores["cod_siembra"] = $this->getCodCampo();
            }
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_siembra"=>$this->getCodCampo()];

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

            $this->setCodCampo($this->consultarValor("SELECT COALESCE(MAX(cod_siembra)+1, 1) FROM siembra"));

            $campos = $this->seter("+");

            $this->insert($this->tbl, $campos["valores"]);

            $this->commit();

            $objSiembras = $this->obtenerCodCampoYListar();
            if ($objSiembras["rpt"] == false){
                return $objSiembras;
            }
            $siembras = $objSiembras["data"];

            return array("rpt"=>true,"msj"=>"Se ha registrado exitosamente", "siembras"=>$siembras);
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

            $objSiembras = $this->obtenerCodCampoYListar();
            if ($objSiembras["rpt"] == false){
                return $objSiembras;
            }
            $siembras = $objSiembras["data"];

            return array("rpt"=>true,"msj"=>"Se ha editado exitosamente", "data"=>$siembras);
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    private function verificarRepetidoAgregar(){
        if ($this->getCodNisira() != NULL && $this->getCodNisira() != ""){
            $sql = "SELECT COUNT(cod_nisira) > 0 FROM ".$this->tbl." WHERE cod_nisira = :0 AND cod_campo = :1 AND estado_mrcb";
            $repetido = $this->consultarValor($sql, [$this->getCodNisira(), $this->getCodCampo()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"ID Siembra ya existente."];
            }
        }    

        if ($this->getFechaFinSiembra() != NULL){
              $rangoFechaCorrecta = $this->consultarValor("SELECT DATE(:0) <= DATE(:1)", [$this->getFechaInicioSiembra(), $this->getFechaFinSiembra()]);
                if ($rangoFechaCorrecta == false){
                      return ["r"=>false, "msj"=>"La fecha final debe ser MENOR que la fecha inicio."];
                }
 
        }
      
        return ["r"=>true, "msj"=>""];
    }

    private function verificarRepetidoEditar(){

        if ($this->getCodNisira() != NULL && $this->getCodNisira() != ""){
            $sql = "SELECT COUNT(cod_nisira) > 0 FROM ".$this->tbl." WHERE cod_nisira = :0  AND cod_campo = :1 AND estado_mrcb  AND cod_siembra <>:2";
            $repetido = $this->consultarValor($sql, [$this->getCodNisira(),$this->getCodCampo(), $this->getCodSiembra()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"ID Siembra ya existente."];
            }
        }

        if ($this->getFechaFinSiembra() != NULL){
              $rangoFechaCorrecta = $this->consultarValor("SELECT DATE(:0) <= DATE(:1)", [$this->getFechaInicioSiembra(), $this->getFechaFinSiembra()]);
                if ($rangoFechaCorrecta == false){
                      return ["r"=>false, "msj"=>"La fecha final debe ser MENOR que la fecha inicio."];
                }
 
        }
      

        return ["r"=>true, "msj"=>""];
    }

    private function obtenerCodCampoYListar(){
        $codCampo  = $this->consultarValor("SELECT cod_campo FROM siembra WHERE cod_siembra = :0", [$this->getCodSiembra()]);
        $this->setCodCampo($codCampo);

        return $this->listarSiembras();
    }

    public function obtenerPreFormulario($codCampo) {
        try { 
            /*ultima siembra, ultima campaña de esa siembra y ultima rea de esa campaña*/
             $sql  = "SELECT  si.cod_siembra, ca.area,LPAD((si.cod_nisira::integer + 1)::text,6,'0') as idsiembra_siguiente
                        FROM campo ca
                        LEFT JOIN siembra si ON ca.cod_campo = si.cod_campo
                        WHERE ca.cod_campo = :0
                        LIMIT 1";

             $dataSiembra = $this->consultarFila($sql, $codCampo);

             if ($dataSiembra["cod_siembra"] == NULL){
                $dataSiembra["cod_siembra"] = "";
                $dataSiembra["idsiembra_siguiente"] = "000001";
             }

            return ["rpt"=>true, "data"=>$dataSiembra];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }
}

    