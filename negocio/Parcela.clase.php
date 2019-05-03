<?php

require_once '../datos/Conexion.clase.php';

class Parcela extends Conexion {
    private $cod_parcela;
    private $rotulo_parcela;
    private $numero_nivel_1;
    private $numero_nivel_2;
    private $numero_nivel_3;
    private $cod_campaña;
    private $fecha_inicio_campaña;
    private $fecha_fin_campaña;
    private $tipo_riego;
    private $cod_variedad;
    private $area;
    private $cod_grupo_coordenadas;
    private $estado_activo;
    private $rotulo;

    private $cambio_coordenadas;
    private $coordenadas;
    private $tbl = "parcela";

    public function getRotulo()
    {
        return $this->rotulo;
    }
    
    
    public function setRotulo($rotulo)
    {
        $this->rotulo = $rotulo;
        return $this;
    }

    public function getCodParcela()
    {
        return $this->cod_parcela;
    }
    
    public function setCodParcela($cod_parcela)
    {
        $this->cod_parcela = $cod_parcela;
        return $this;
    }

    public function getRotuloParcela()
    {
        return $this->rotulo_parcela;
    }
    
    public function setRotuloParcela($rotulo_parcela)
    {
        $this->rotulo_parcela = $rotulo_parcela;
        return $this;
    }

    public function getNumeroNivel1()
    {
        return $this->numero_nivel_1;
    }
    
    public function setNumeroNivel1($numero_nivel_1)
    {
        $this->numero_nivel_1 = $numero_nivel_1;
        return $this;
    }

    public function getNumeroNivel2()
    {
        return $this->numero_nivel_2;
    }
    
    
    public function setNumeroNivel2($numero_nivel_2)
    {
        $this->numero_nivel_2 = $numero_nivel_2;
        return $this;
    }

    public function getNumeroNivel3()
    {
        return $this->numero_nivel_3;
    }
    
    
    public function setNumeroNivel3($numero_nivel_3)
    {
        $this->numero_nivel_3 = $numero_nivel_3;
        return $this;
    }

    public function getCodCampaña()
    {
        return $this->cod_campaña;
    }
    
    
    public function setCodCampaña($cod_campaña)
    {
        $this->cod_campaña = $cod_campaña;
        return $this;
    }

    public function getFechaInicioCampaña()
    {
        return $this->fecha_inicio_campaña;
    }
    
    
    public function setFechaInicioCampaña($fecha_inicio_campaña)
    {
        $this->fecha_inicio_campaña = $fecha_inicio_campaña;
        return $this;
    }

    public function getFechaFinCampaña()
    {
        return $this->fecha_fin_campaña;
    }
    
    public function setFechaFinCampaña($fecha_fin_campaña)
    {
        $this->fecha_fin_campaña = $fecha_fin_campaña;
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

    public function getCodVariedad()
    {
        return $this->cod_variedad;
    }
    
    
    public function setCodVariedad($cod_variedad)
    {
        $this->cod_variedad = $cod_variedad;
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

    public function getCoordenadas()
    {
        return $this->coordenadas;
    }
    
    public function setCoordenadas($coordenadas)
    {
        $this->coordenadas = $coordenadas;
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

    public function getCodGrupoCoordenadas()
    {
        return $this->cod_grupo_coordenadas;
    }
    
    
    public function setCodGrupoCoordenadas($cod_grupo_coordenadas)
    {
        $this->cod_grupo_coordenadas = $cod_grupo_coordenadas;
        return $this;
    }

    public function getCambioCoordenadas()
    {
        return $this->cambio_coordenadas;
    }
    
    
    public function setCambioCoordenadas($cambio_coordenadas)
    {
        $this->cambio_coordenadas = $cambio_coordenadas;
        return $this;
    }

    public function obtenerDatosBase(){
        try {

            $sql  =" SELECT cod_campo as codigo, nombre_campo as descripcion FROM campo WHERE estado_mrcb ORDER BY nombre_campo";
            $campos = $this->consultarFilas($sql);

            $sql  =" SELECT cod_cultivo as codigo, nombre as descripcion FROM cultivo WHERE estado_mrcb = 1 ORDER BY nombre";
            $cultivos = $this->consultarFilas($sql);

            return ["rpt"=>true, "data"=>["campos"=>$campos,"cultivos"=>$cultivos]];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function darBaja(){
        try {

            $campos_valores = ["estado_mrcb"=>"false"];
            $campos_valores_where = ["cod_parcela"=>$this->getCodParcela()];

            $this->update($this->tbl,$campos_valores, $campos_valores_where);

            return ["rpt"=>true, "msj"=>"Parcela dada de baja correctamente."];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function finalizar(){
        try {

            $campos_valores = ["estado_activo"=>"false"];
            $campos_valores_where = ["cod_parcela"=>$this->getCodParcela()];

            $this->update($this->tbl,$campos_valores, $campos_valores_where);

            return ["rpt"=>true, "msj"=>"Parcela finalizada correctamente."];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerEditar(){
        try {
             $sql = "SELECT c.cod_campo, 
                        s.cod_siembra,
                        cp.cod_campaña,
                        p.fecha_inicio_campaña as fecha_inicio,
                        p.fecha_fin_campaña  as fecha_fin,
                        p.tipo_riego,
                        v.cod_cultivo,
                        p.cod_variedad,
                        p.area,
                        p.numero_nivel_1 as nn1,
                        p.numero_nivel_2 as nn2,
                        p.numero_nivel_3 as nn3,
                        p.cod_grupo_coordenadas,
                        p.rotulo_parcela
                        FROM parcela p
                        INNER JOIN campaña cp ON cp.cod_campaña = p.cod_campaña
                        INNER JOIN siembra s ON s.cod_siembra = cp.cod_siembra
                        INNER JOIN campo c ON c.cod_campo = s.cod_campo
                        LEFT JOIN variedad v ON v.cod_variedad = p.cod_variedad
                        WHERE p.estado_mrcb AND p.cod_parcela = :0";

            $data = $this->consultarFila($sql, $this->getCodParcela());

            if ($data["cod_grupo_coordenadas"] != null && $data["cod_grupo_coordenadas"] != ""){
                $sql = "SELECT coord_latitud as lat, coord_longitud as lng FROM parcela_coordenada WHERE cod_grupo_coordenadas = :0 ORDER BY cod_coordenada";
                $coordenadas = $this->consultarFilas($sql, $data["cod_grupo_coordenadas"]);    
            } else {
                $coordenadas = [];
            }
            
            $data["coordenadas"] = $coordenadas;

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
            $campos_valores = ["cod_campaña"=>$this->getCodCampaña(),
                                "rotulo_parcela"=>$this->getRotuloParcela(),
                                "numero_nivel_1"=>$this->getNumeroNivel1(),
                                "numero_nivel_2"=>$this->getNumeroNivel2(),
                                "numero_nivel_3"=>$this->getNumeroNivel3(),
                                "rotulo_parcela"=>$this->getRotuloParcela(),
                                "area"=>$this->getArea(),
                                "fecha_inicio_campaña"=>$this->getFechaInicioCampaña(),
                                "fecha_fin_campaña"=>$this->getFechaFinCampaña(),
                                "tipo_riego"=>$this->getTipoRiego(),
                                "cod_variedad"=>$this->getCodVariedad(),
                                "cod_grupo_coordenadas"=>$this->getCodGrupoCoordenadas()
                                ];      

            if ($tipoAccion == "+"){
                $campos_valores["cod_parcela"] = $this->getCodParcela();
                $this->setCambioCoordenadas(true);
            }

            if ($this->getCambioCoordenadas()){
                if ($this->getCoordenadas() != ""){
                    $objCoord =  json_decode($this->getCoordenadas());

                    $arCoords = $objCoord->coordenadas;

                    $fila = $this->consultarFila("SELECT COALESCE(MAX(cod_coordenada)+1, 1) as max_cod_coordenada,
                                                         COALESCE(MAX(cod_grupo_coordenadas)+1, 1) as max_cod_grupo_coord FROM parcela_coordenada");

                    $codCoordenada = $fila["max_cod_coordenada"];
                    $codGrupoCoordenadas = $fila["max_cod_grupo_coord"];

                    $sql  = "INSERT INTO parcela_coordenada(cod_coordenada, cod_grupo_coordenadas, coord_latitud, coord_longitud) VALUES ";
                    foreach ($arCoords as $key => $value) {
                        $sql .= "(".$codCoordenada.",".$codGrupoCoordenadas.",".$value->lat.",".$value->lng."),";
                        $codCoordenada++;
                    }

                    if (count($arCoords) > 0){
                        $sql = substr($sql, 0, -1);
                    }

                    $this->consultaRaw($sql);
                    $campos_valores["cod_grupo_coordenadas"] = $codGrupoCoordenadas;
                }
            }
            
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_parcela"=>$this->getCodParcela()];

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

            $this->setCodParcela($this->consultarValor("SELECT COALESCE(MAX(cod_parcela)+1, 1) FROM parcela"));

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
        //fechas dentro de campaña (si hay fechas)
        //cod_campaña y nn1 nn2 nn3 no repetido
        /*
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
        */
        return ["r"=>true, "msj"=>""];
    }

    private function verificarRepetidoEditar(){
/*
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
*/
        return ["r"=>true, "msj"=>""];
    }
}

    