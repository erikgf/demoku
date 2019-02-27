<?php

require_once '../datos/Conexion.clase.php';

class Personal extends Conexion {
    private $id_personal;
    private $nombres;
    private $apellidos;
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

            /*Validar dni*/
            $repetido = $this->consultarValor("SELECT COUNT(*) > 0 FROM personal WHERE dni = :0", [$this->getDni()]);

            if ($repetido == true){
                return array("rpt"=>false,"msj"=>"El DNI ingresado ya EXISTE.");    
            }

            /*Validar Celular*/
            $repetido = $this->consultarValor("SELECT COUNT(*) > 0 FROM personal WHERE numero_celular = :0", [$this->getNumero_celular()]);

            if ($repetido == true){
                return array("rpt"=>false,"msj"=>"El número de celular ingresado ya EXISTE.");    
            }


            $campos_valores = 
            array(  "dni"=>$this->getDni(),
                    "nombres"=>$this->getNombres(),
                    "apellidos"=>$this->getApellidos(),
                    "numero_celular"=>$this->getNumero_celular(),
                    "fecha_ingreso"=>$this->getFecha_ingreso(),
                    "id_cargo"=>$this->getId_cargo());

            $this->insert("personal", $campos_valores);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha agregado exitosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function editar() {
        $this->beginTransaction();
        try { 

            $repetido = 
            $this->consultarValor("SELECT COUNT(*) > 0 FROM personal WHERE dni = :0 AND id_personal <> :1", [$this->getDni(), $this->getId_personal()]);

            if ($repetido == true){
                return array("rpt"=>false,"msj"=>"El DNI ingresado ya EXISTE.");    
            }

            $repetido = 
            $this->consultarValor("SELECT COUNT(*) > 0 FROM personal WHERE numero_celular = :0 AND id_personal <> :1", [$this->getNumero_celular(), $this->getId_personal()]);

            if ($repetido == true){
                return array("rpt"=>false,"msj"=>"El número de celular ingresado ya EXISTE.");    
            }

            $campos_valores = 
            array(  "dni"=>$this->getDni(),
                    "nombres"=>$this->getNombres(),
                    "apellidos"=>$this->getApellidos(),
                    "numero_celular"=>$this->getNumero_celular(),
                    "fecha_ingreso"=>$this->getFecha_ingreso(),
                    "id_cargo"=>$this->getId_cargo());    

            $campos_valores_where = array(  "id_personal"=>$this->getId_personal());

            $this->update("personal", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha actualizado correctamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }


    public function listar($filtro_cargo, $filtro_apellido, $filtro_estado){
        try {

            if ($filtro_estado == 1){
                $filtro_estado = " AND p.estado_mrcb ";
            } else if ($filtro_estado == 0){
                $filtro_estado = " AND NOT p.estado_mrcb ";
            } else {
                $filtro_estado = "";
            }

            $sql = "SELECT
                    p.id_personal,
                    p.dni,
                    p.nombres,
                    p.apellidos,
                    p.numero_celular,
                    c.descripcion as cargo,
                    p.estado_mrcb
                    FROM personal p 
                    INNER JOIN cargo c ON p.id_cargo = c.id_cargo 
                    AND upper(c.descripcion) LIKE '%".strtoupper($filtro_cargo)."%'
                    AND upper(p.apellidos) LIKE '%".strtoupper($filtro_apellido)."%'
                     ".$filtro_estado."
                    ORDER BY p.apellidos";

            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT * FROM personal WHERE id_personal = :0";
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
            return array("rpt"=>true,"msj"=>"Se ha actualizado existosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function llenarCB(){
        try {
            $sql = "SELECT id_personal, CONCAT(nombres,' ',apellidos) as descripcion FROM personal WHERE estado_mrcb";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    } 

    public function llenarCBEvaluadorSupervisor (){
        try {
            $sql = "SELECT u.id_usuario, CONCAT(p.nombres,' ',p.apellidos) as personal FROM personal p
                    INNER JOIN usuario u ON p.id_personal = u.id_personal
                    INNER JOIN rol r ON r.id_rol = u.id_rol
                    WHERE p.estado_activo = 'A' AND u.estado ='A'
                    AND r.estado_mrcb = 1 AND r.descripcion IN ('SUPERVISOR EVALUADOR');";
            $supervisores = $this->consultarFilas($sql);

            $sql = "SELECT u.id_usuario, CONCAT(p.nombres,' ',p.apellidos) as personal FROM personal p
                    INNER JOIN usuario u ON p.id_personal = u.id_personal
                    INNER JOIN rol r ON r.id_rol = u.id_rol
                    WHERE p.estado_activo = 'A' AND u.estado ='A' 
                    AND r.estado_mrcb = 1 AND r.descripcion IN ('EVALUADOR','SUPERVISOR EVALUADOR');";
            $evaluadores = $this->consultarFilas($sql);        

            return array("rpt"=>true,"msj"=>["supervisores"=>$supervisores,"evaluadores"=>$evaluadores]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    } 

     public function obtenerLiberadores (){
        try {
            $sql = "SELECT u.id_usuario, CONCAT(p.nombres,' ',p.apellidos) as personal FROM personal p
                    INNER JOIN usuario u ON p.id_personal = u.id_personal
                    INNER JOIN rol r ON r.id_rol = u.id_rol
                    WHERE p.estado_activo = 'A' AND u.estado ='A'
                    AND r.estado_mrcb = 1 AND r.descripcion IN ('LIBERADOR');";
            $data = $this->consultarFilas($sql);

            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    } 


          

}