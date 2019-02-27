<?php

require_once '../datos/Conexion.clase.php';

class Usuario extends Conexion {
    private $idUsuario;
    private $login;
    private $clave;
    private $estado;
    private $idPersonal;
    private $idRol;

    private $tbl = "usuario";

    
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }
    
    
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
        return $this;
    }

    public function getLogin()
    {
        return $this->login;
    }
    
    
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    public function getClave()
    {
        return $this->clave;
    }
    
    
    public function setClave($clave)
    {
        $this->clave = $clave;
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


    public function getIdPersonal()
    {
        return $this->idPersonal;
    }
    
    
    public function setIdPersonal($idPersonal)
    {
        $this->idPersonal = $idPersonal;
        return $this;
    }

    public function getIdRol()
    {
        return $this->idRol;
    }
    
    
    public function setIdRol($idRol)
    {
        $this->idRol = $idRol;
        return $this;
    }

    public function agregar() {      
        $this->beginTransaction();
        try {
            
            $data = $this->consultarValor("SELECT fn_registrar_usuario(:0,:1,:2,:3)", [$this->getLogin(),md5($this->getClave()), $this->getIdRol(), $this->getIdPersonal()]);

            $this->commit();
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function editar() {
        $this->beginTransaction();
        try {

            $ar = [$this->getLogin(),$this->getIdRol()];
            $sql = "SELECT fn_editar_usuario(:0,:1,";

            if ($this->getClave() != null || $this->getClave() != ""){                
                $sql .= ":2,:3)";
                array_push($ar,md5($this->getClave()),$this->getIdUsuario());
            } else {
                $sql .= "NULL,:2)";
                array_push($ar,$this->getIdUsuario());
            }

            $data = $this->consultarValor($sql,$ar);

            $this->commit();
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT id_usuario, login, id_personal, id_rol FROM usuario WHERE id_usuario = :0";
            $resultado = $this->consultarFila($sql,array($this->getIdUsuario()));
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function darBaja() {
        $this->beginTransaction();
        try {            
            $campos_valores = 
            array(  "estado"=>$this->getEstado() == '1' ? 'A' : 'I');

            $campos_valores_where = 
            array(  "id_usuario"=>$this->getIdUsuario());

            $this->update("usuario", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha actualizado exitosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

     public function listar($filtro_rol, $filtro_apellido, $filtro_usuario, $filtro_estado){
        try {

            if ($filtro_estado == "1"){
                $filtro_estado = "AND u.estado = 'A'";
            } else if ($filtro_estado == "0"){
                $filtro_estado = "AND u.estado = 'I'";
            } else {
                $filtro_estado = "";
            }

            $sql = "SELECT
                    u.id_usuario,
                    u.login,
                    p.nombres,
                    p.apellidos,
                    r.descripcion as rol,
                    u.estado
                    FROM usuario u
                    INNER JOIN personal p ON p.id_personal = u.id_personal
                    INNER JOIN rol r ON r.id_rol = u.id_rol
                    WHERE upper(r.descripcion) LIKE '%".strtoupper($filtro_rol)."%'
                    AND upper(p.apellidos) LIKE '%".strtoupper($filtro_apellido)."%'
                    AND upper(u.login) LIKE '%".strtoupper($filtro_usuario)."%' ".$filtro_estado."
                    ORDER BY p.apellidos";

            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function iniciarSesionMovil()
    {
        try {

            $sql = "SELECT id_usuario, login, clave, u.estado, r.descripcion as rol,
                            CONCAT(p.nombres,' ',p.apellidos) as nombres_usuario
                            FROM usuario u 
                            INNER JOIN  rol r ON u.id_rol = r.id_rol
                            INNER JOIN personal p ON p.id_personal = u.id_personal 
                            WHERE upper(login) = upper(:0) AND (tipo_acceso = 0 OR tipo_acceso = 2)";
            $res = $this->consultarFila($sql, array($this->getLogin()));

            if ($res != false){

                if ($res["estado"] == 'A'){
                    if ($res["clave"] == md5($this->getClave())){
                        return array("rpt"=>true, "msj"=>"Acceso permitido.",
                                "usuario" => array(
                                    "nombres_usuario"=> $res["nombres_usuario"],
                                    "rol" => $res["rol"],
                                    "id_usuario" => $res["id_usuario"])
                                );
                    }    

                    return array("rpt"=>false, "msj"=>"Clave incorrecta");
                }

                return array("rpt"=>false, "msj"=>"Usuario inactivo.");                
                

            } else{
                return array("rpt"=>false, "msj"=>"Usuario inexistente.");
            }

            
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function iniciarSesionWeb($JSONFrm)
    {
        try {
            parse_str($JSONFrm , $datosFormularioArray);       
            $this->setLogin($datosFormularioArray["username"]);
            $this->setClave($datosFormularioArray["password"]);

            $sql = "SELECT u.id_usuario, u.login, u.clave, u.estado, r.id_rol, r.descripcion as rol,
                            CONCAT(p.nombres,' ',p.apellidos) as nombres_usuario, p.correo
                            FROM usuario u 
                            INNER JOIN  rol r ON u.id_rol = r.id_rol
                            INNER JOIN personal p ON p.id_personal = u.id_personal
                            WHERE login = :0 AND tipo_acceso >= 1";

            $res = $this->consultarFila($sql, array($this->getLogin()));

            if ($res != false){
                if ($res["estado"] == 'A'){
                    if ($res["clave"] == md5($this->getClave())) {
                        $objUsuario = array(
                                    "nombres_usuario"=> $res["nombres_usuario"],
                                    "id_rol"=>$res["id_rol"],
                                    "rol" => $res["rol"],
                                    "id_usuario" => $res["id_usuario"],
                                    "correo"=>$res["correo"]
                                    );
                        $_SESSION["obj_usuario"] = $objUsuario;
                        return array("rpt"=>true, 
                                "msj"=>"Acceso permitido. Ingresando...",
                                "usuario" => $objUsuario);
                    }    
                    return array("rpt"=>false, "msj"=>"Clave incorrecta...");
                }

                return array("rpt"=>false, "msj"=>"Usuario inactivo...");                
            } else{
                return array("rpt"=>false, "msj"=>"Usuario inexistente...");
            }

            
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function cerrarSesionWeb()
    {
        session_unset("obj_usuario");            
        return array("rpt"=>true, "msj"=>"Sesión finalizada.");                
    }

    public function obtenerDataSincronizacion()
    {
        try {

        $this->getIdUsuario();        
            
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }
}