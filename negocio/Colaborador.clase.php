<?php

require_once '../datos/Conexion.clase.php';

class Colaborador extends Conexion {
    private $cod_colaborador;
    private $nombres;
    private $apellidos;
    private $dni;
    private $correo;
    private $celular;
    private $usuario;
    private $clave;
    private $cod_perfil;
    private $estado_baja;
    private $estado_mrcb;

    private $tbl = "colaborador";

    public function getCodColaborador()
    {
        return $this->cod_colaborador;
    }
    
    public function setCodColaborador($cod_colaborador)
    {
        $this->cod_colaborador = $cod_colaborador;
        return $this;
    }

    public function getNombres()
    {
        return $this->nombres;
    }
    
    
    public function setNombres($nombres)
    {
        $this->nombres = $nombres;
        return $this;
    }

    public function getApellidos()
    {
        return $this->apellidos;
    }
    
    
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;
        return $this;
    }

    public function getDni()
    {
        return $this->dni;
    }
    
    
    public function setDni($dni)
    {
        $this->dni = $dni;
        return $this;
    }

    public function getCorreo()
    {
        return $this->correo;
    }
    
    
    public function setCorreo($correo)
    {
        $this->correo = $correo;
        return $this;
    }

    public function getCelular()
    {
        return $this->celular;
    }
    
    
    public function setCelular($celular)
    {
        $this->celular = $celular;
        return $this;
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

    public function getClave()
    {
        return $this->clave;
    }
    
    
    public function setClave($clave)
    {
        $this->clave = $clave;
        return $this;
    }

    public function getCodPerfil()
    {
        return $this->cod_perfil;
    }
    
    
    public function setCodPerfil($cod_perfil)
    {
        $this->cod_perfil = $cod_perfil;
        return $this;
    }

    public function getEstadoBaja()
    {
        return $this->estado_baja;
    }
    
    
    public function setEstadoBaja($estado_baja)
    {
        $this->estado_baja = $estado_baja;
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
            $campos_valores_where = ["cod_colaborador"=>$this->getCodColaborador()];

            $this->update($this->tbl, $campos_valores, $campos_valores_where);

            return ["rpt"=>true, "msj"=>"Registro dado de baja correctamente."];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerEditar(){
        try {

             $sql = "SELECT cod_colaborador, 
                        dni,
                        nombres,
                        apellidos,
                        correo,
                        celular,
                        usuario,
                        cod_perfil,
                        estado_baja
                        FROM colaborador
                        WHERE estado_mrcb AND  cod_colaborador = :0";

            $data = $this->consultarFila($sql, $this->getCodColaborador());

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
            $campos_valores = ["dni"=>$this->getDni(),
                                "nombres"=>$this->getNombres(),
                                "apellidos"=>$this->getApellidos(),
                                "correo"=>$this->getCorreo(),
                                "celular"=>$this->getCelular(),
                                "usuario"=>$this->getUsuario(),
                                "cod_perfil"=>$this->getCodPerfil(),
                                "estado_baja"=>$this->getEstadoBaja()];

            if ($tipoAccion == "+"){
                $campos_valores["cod_colaborador"] = $this->getCodColaborador();
            }
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_colaborador"=>$this->getCodColaborador()];
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

            $this->setCodColaborador($this->consultarValor("SELECT COALESCE(MAX(cod_colaborador)+1, 1) FROM colaborador"));

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
        if ($this->getDni() != NULL && $this->getDni() != ""){
            $sql = "SELECT COUNT(dni) > 0 FROM ".$this->tbl." WHERE dni = :0 AND estado_mrcb";
            $repetido = $this->consultarValor($sql, [$this->getDni()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"DNI de colaborador ya existente."];
            }
        }

        if ($this->getCorreo() != NULL && $this->getCorreo() != ""){
            $sql = "SELECT COUNT(correo) > 0 FROM ".$this->tbl." WHERE correo = :0 AND estado_mrcb";
            $repetido = $this->consultarValor($sql, [$this->getCorreo()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Correo de colaborador ya existente."];
            }
        }

        if ($this->getCelular() != NULL && $this->getCelular() != ""){
            $sql = "SELECT COUNT(celular) > 0 FROM ".$this->tbl." WHERE celular = :0 AND estado_mrcb";
            $repetido = $this->consultarValor($sql, [$this->getCelular()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Celular de colaborador ya existente."];
            }
        }

        if ($this->getUsuario() != NULL && $this->getUsuario() != ""){
            $sql = "SELECT COUNT(usuario) > 0 FROM ".$this->tbl." WHERE usuario = :0 AND estado_mrcb";
            $repetido = $this->consultarValor($sql, [$this->getUsuario()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Nombre de usuario de colaborador ya existente."];
            }
        }

        return ["r"=>true, "msj"=>""];
    }

    private function verificarRepetidoEditar(){
        if ($this->getDni() != NULL && $this->getDni() != ""){
            $sql = "SELECT COUNT(dni) > 0 FROM ".$this->tbl." WHERE dni = :0 AND estado_mrcb AND cod_colaborador <>:1";
            $repetido = $this->consultarValor($sql, [$this->getDni(),$this->getCodColaborador()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"DNI de colaborador ya existente."];
            }
        }

        if ($this->getCorreo() != NULL && $this->getCorreo() != ""){
            $sql = "SELECT COUNT(correo) > 0 FROM ".$this->tbl." WHERE correo = :0 AND estado_mrcb AND cod_colaborador <>:1";
            $repetido = $this->consultarValor($sql, [$this->getCorreo()],$this->getCodColaborador());

            if ($repetido){
                return ["r"=>false, "msj"=>"Correo de colaborador ya existente."];
            }
        }

        if ($this->getCelular() != NULL && $this->getCelular() != ""){
            $sql = "SELECT COUNT(celular) > 0 FROM ".$this->tbl." WHERE celular = :0 AND estado_mrcb AND cod_colaborador <>:1";
            $repetido = $this->consultarValor($sql, [$this->getCelular(),$this->getCodColaborador()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Celular de colaborador ya existente."];
            }
        }

        if ($this->getUsuario() != NULL && $this->getUsuario() != ""){
            $sql = "SELECT COUNT(usuario) > 0 FROM ".$this->tbl." WHERE usuario = :0 AND estado_mrcb AND cod_colaborador <>:1";
            $repetido = $this->consultarValor($sql, [$this->getUsuario(),$this->getCodColaborador()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Nombre de usuario de colaborador ya existente."];
            }
        }

        return ["r"=>true, "msj"=>""];
    }

    public function listar(){
        try {

            $sql  ="SELECT cod_colaborador,  dni, nombres, apellidos,correo, celular, 
                            (CASE estado_baja WHEN 'A' THEN 'ACTIVO' ELSE 'INACTIVO' END) as estado_baja, usuario, 
                             p.descripcion as perfil
                             FROM colaborador c
                             INNER JOIN perfil p ON c.cod_perfil = p.cod_perfil
                             WHERE c.estado_mrcb ORDER BY apellidos, nombres";
            $res = $this->consultarFilas($sql);

            return ["rpt"=>true, "data"=>$res];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }
    
    public function cambiarClave($nuevaClave, $confirmarClave){
        try {

            if ($nuevaClave != $confirmarClave){
               return ["rpt"=>false, "msj"=>"Las claves no son iguales."];
            }

            $campos_valores = ["clave"=>md5($confirmarClave)];
            $campos_valores_where = ["cod_colaborador"=>$this->getCodColaborador()];

            $this->update($this->tbl, $campos_valores, $campos_valores_where);

            return ["rpt"=>true, "msj"=>"Clave cambiada correctamente."];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }


}

    