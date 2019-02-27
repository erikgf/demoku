<?php

require_once '../datos/Conexion.clase.php';

class PermisoRol extends Conexion {
    private $id_rol;
    private $id_permiso;

    public function getId_rol()
    {
        return $this->id_rol;
    }
    
    public function setId_rol($id_rol)
    {
        $this->id_rol = $id_rol;
        return $this;
    }
    
    public function listarPermisoActivos(){
        try {
            $sql = "SELECT 
                        p.id_permiso,
                        p.titulo_interfaz,
                        (SELECT titulo_interfaz FROM permiso WHERE id_permiso = p.padre ) as superior 
                    FROM 
                        permiso_rol pr INNER JOIN permiso p ON pr.id_permiso = p.id_permiso 
                    WHERE pr.id_rol = :0 AND p.padre IS NOT NULL
                    ORDER BY 3 DESC";
            $resultado = $this->consultarFilas($sql,array($this->getId_rol()));
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function listarPermisoInactivos(){
        try {
            $sql = "SELECT 
                        id_permiso,
                        titulo_interfaz,
                        (SELECT titulo_interfaz FROM permiso WHERE id_permiso = p.padre)  as superior  
                    FROM permiso p 
                    WHERE 
                        p.padre  IS NOT NULL AND 
                        p.id_permiso NOT IN (SELECT id_permiso FROM permiso_rol WHERE id_rol = :0)";
            $resultado = $this->consultarFilas($sql,array($this->getId_rol()));
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function agregar($p1,$p2) {
        $this->beginTransaction();
        try {            
            $sql = "SELECT COUNT(*) FROM permiso_rol
                    WHERE id_permiso = 
                    (
                        SELECT padre FROM permiso WHERE titulo_interfaz = :0
                    ) AND id_rol = :1";
            $cantidad = intval($this->consultarValor($sql,array($p1,$p2)));

            if ( $cantidad == 0 ) {
                $sql = "SELECT padre FROM permiso WHERE titulo_interfaz = :0";
                $padre  = $this->consultarValor($sql,array($p1));

                $campos_valores = 
                array(  "id_permiso"=>$padre,
                        "id_rol"=>$p2,
                        "estado"=>'A');
                $this->insert("permiso_rol", $campos_valores);
            }

            $sql = "SELECT id_permiso FROM permiso WHERE titulo_interfaz = :0";
            $hijo  = $this->consultarValor($sql,array($p1));

            $campos_valores = 
            array(  "id_permiso"=>$hijo,
                    "id_rol"=>$p2,
                    "estado"=>'A');
            $this->insert("permiso_rol", $campos_valores);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se agregado exitosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function quitar($p1,$p2) {
        $this->beginTransaction();
        try {
            $sql = "SELECT COUNT(*) FROM permiso_rol
                    WHERE id_permiso = 
                    (
                        SELECT padre FROM permiso WHERE titulo_interfaz = :0
                    ) AND id_rol = :1";
            $cantidad = intval($this->consultarValor($sql,array($p1,$p2)));

            if ( $cantidad == 1 ) {
                $sql = "SELECT padre FROM permiso WHERE titulo_interfaz = :0";
                $padre  = $this->consultarValor($sql,array($p1));

                $campos_valores = 
                array(  "id_permiso"=>$padre,
                        "id_rol"=>$p2);

                $this->delete("permiso_rol", $campos_valores);
            }
            

            $sql = "SELECT id_permiso FROM permiso WHERE titulo_interfaz = :0";
            $hijo  = $this->consultarValor($sql,array($p1));

            $campos_valores = 
            array(  "id_permiso"=>$hijo,
                    "id_rol"=>$p2);

            $this->delete("permiso_rol", $campos_valores);      

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se quitado exitosamente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

}
