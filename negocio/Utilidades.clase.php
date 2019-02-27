<?php

require_once '../datos/Conexion.clase.php';

class Utilidades extends Conexion {
    public function iniciarSesionMovil()
    {
        try {

            $sql = "SELECT id_usuario, login, clave, u.estado, r.descripcion as rol,
                            p.nombres as nombres_usuario
                            FROM usuario u 
                            INNER JOIN  rol r ON u.id_rol = r.id_cargo 
                            INNER JOIN personal p ON p.id_personal = u.id_personal
                            WHERE login = :0";
            $res = $this->consultarFila($sql, array($this->getLogin()));

            if ($res != false){

                if ($res["estado"] == 'A'){
                    if (md5($res["clave"]) == md5($this->getClave())){
                        return array("rpt"=>true, "msj"=>"Acceso permitido.");
                    }    

                    return array("rpt"=>false, "msj"=>"Clave incorrecta");
                }

                return array("rpt"=>false, "msj"=>"Usuario inactivo.");                
                

            } else{
                return array("rpt"=>false, "msj"=>"Usuario inexistente.")
            }

            
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }
}