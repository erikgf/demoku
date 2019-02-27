<?php

require_once '../datos/Conexion.clase.php';

class Personal extends Conexion {
    private $id_personal;
    private $nombre_apellidos;
    private $dni;
    private $numero_celular;
    private $fecha_ingreso;
    private $id_cargo;
    private $estado_mrcb;

    public function getId_personal()
    {
        return $this->id_personal;
    }
    
    public function setId_personal($id_personal)
    {
        $this->id_personal = $id_personal;
        return $this;
    }

    public function getNombre_apellidos()
    {
        return $this->nombre_apellidos;
    }
    
    public function setNombre_apellidos($nombre_apellidos)
    {
        $this->nombre_apellidos = $nombre_apellidos;
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

    public function getNumero_celular()
    {
        return $this->numero_celular;
    }
    
    public function setNumero_celular($numero_celular)
    {
        $this->numero_celular = $numero_celular;
        return $this;
    }

    public function getFecha_ingreso()
    {
        return $this->fecha_ingreso;
    }
    
    public function setFecha_ingreso($fecha_ingreso)
    {
        $this->fecha_ingreso = $fecha_ingreso;
        return $this;
    }

    public function getId_cargo()
    {
        return $this->id_cargo;
    }
    
    public function setId_cargo($id_cargo)
    {
        $this->id_cargo = $id_cargo;
        return $this;
    }

    public function getEstado_mrcb()
    {
        return $this->estado_mrcb;
    }
    
    public function setEstado_mrcb($estado_mrcb)
    {
        $this->estado_mrcb = $estado_mrcb;
        return $this;
    }

    public function agregar() {
        $this->beginTransaction();
        try {            
            $campos_valores = 
            array(  "dni"=>$this->getDni(),
                    "nombre_apellidos"=>$this->getNombre_apellidos(),
                    "numero_celular"=>$this->getNumero_celular(),
                    "fecha_ingreso"=>$this->getFecha_ingreso(),
                    "id_cargo"=>$this->getId_cargo());

            $this->insert("personal", $campos_valores);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se agregado exitosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function editar() {
        $this->beginTransaction();
        try { 
            $campos_valores = 
            array(  "dni"=>$this->getDni(),
                    "nombre_apellidos"=>$this->getNombre_apellidos(),
                    "numero_celular"=>$this->getNumero_celular(),
                    "fecha_ingreso"=>$this->getFecha_ingreso(),
                    "id_cargo"=>$this->getId_cargo());

            $campos_valores_where = 
            array(  "id_personal"=>$this->getId_personal());

            $this->update("personal", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se actualizado correctamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }


    public function listar(){
        try {
            $sql = "SELECT
                    p.id_personal,
                    p.dni,
                    p.nombre_apellidos,
                    p.numero_celular,
                    c.descripcion as cargo,
                    p.estado_mrcb
                    FROM personal p INNER JOIN cargo c ON p.id_cargo = c.id_cargo
                    WHERE p.estado_mrcb = 1";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT * FROM personal WHERE estado_mrcb = 1 AND id_personal = :0";
            $resultado = $this->consultarFila($sql,array($this->getId_personal()));
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
            array(  "estado_mrcb"=>$this->getEstado_mrcb());

            $campos_valores_where = 
            array(  "id_personal"=>$this->getId_personal());

            $this->update("personal", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se inactivado existosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

}