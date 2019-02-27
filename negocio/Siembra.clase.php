<?php

require_once '../datos/Conexion.clase.php';

class Siembra extends Conexion {
    private $idCampo;
    private $idSiembra;

    public function getIdCampo()
    {
        return $this->idCampo;
    }
    
    
    public function setIdCampo($idCampo)
    {
        $this->idCampo = $idCampo;
        return $this;
    }

    public function getIdSiembra()
    {
        return $this->idSiembra;
    }
    
    
    public function setIdSiembra($idSiembra)
    {
        $this->idSiembra = $idSiembra;
        return $this;
    }

    public function verDetalle()
    {
        try {

            $sql = "SELECT * FROM fn_ver_siembra(:0)";
            $JSONCampo = $this->consultarValor($sql, [$this->getIdSiembra()]); 

            return array("rpt"=>true,"data"=>$JSONCampo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }
    public function registrarSiembra($fechaInicio, $variedadCaña, $tipoRiego)
    {
        try {
            $this->beginTransaction();
            $ar = [$fechaInicio, $variedadCaña, $tipoRiego, $this->getIdCampo()];
            $data = $this->consultarValor("SELECT fn_registrar_siembra(:0,:1,:2,:3,99)",$ar);

            $this->commit();
            return array("rpt"=>true, "data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }
    
}

    