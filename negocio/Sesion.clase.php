<?php

require_once '../datos/Conexion.clase.php';

class Sesion extends Conexion {
    private $usuario;
    private $clave;
    private $recordar;
    
    public function getRecordar() {
        return $this->recordar;
    }

    public function setRecordar($recordar) {
        $this->recordar = $recordar;
    }

    public function getClave() {
        return $this->clave;
    }

    public function setClave($clave) {
        $this->clave = $clave;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }
    
    
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function iniciarSesion()
    {
        try {            
            $sql = " SELECT c.cod_colaborador,
                        CONCAT(c.apellidos,', ',c.nombres) as nombres_usuario, 
                        p.descripcion as perfil, 
                        p.cod_perfil, clave, estado_baja
                        FROM colaborador c 
                        INNER JOIN perfil p ON p.cod_perfil = c.cod_perfil
                        WHERE usuario = :0 AND estado_acceso = 2 AND c.estado_mrcb";

            $res = $this->consultarFila($sql, $this->getUsuario());

            if ($res != false){
                if ($res["estado_baja"] == 'A'){
                    if ($res["clave"] == md5($this->getClave())){
                        $duracion = time() + (1000 * 3600 * 24);
                        if ($this->getRecordar() == "true"){
                            setcookie('usuario', $this->getUsuario(), $duracion, "/");
                        } else {
                            setcookie("usuario", "", $duracion,"/");
                        }
                        $codColaborador = $res["cod_colaborador"];
                        setcookie("codusuario",$codColaborador, $duracion,"/");

                        $_SESSION["usuario"] =  array(
                                    "cod_usuario"=> $codColaborador,
                                    "nombres_usuario"=> $res["nombres_usuario"],
                                    "perfil"=>  $res["perfil"],
                                    "cod_perfil"=>$res["cod_perfil"]
                                    );

                        return array("rpt"=>true, "msj"=>"Acceso permitido.",
                                    "usuario" => $_SESSION["usuario"]);
                    }    
                    return array("rpt"=>false, "msj"=>"Clave incorrecta.");
                }
                return array("rpt"=>false, "msj"=>"Usuario inactivo.");                
            }
            
            return array("rpt"=>false, "msj"=>"Usuario inexistente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function cerrarSesion()
    {
        try {
            if (isset($_COOKIE["codusuario"]) && $_COOKIE["codusuario"] != null){
                setcookie("codusuario","",0,"/");
            }
            session_destroy();
            return array("rpt"=>true,"msj"=>"Sesión cerrada.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }
        
}
