<?php

require_once '../datos/Conexion.clase.php';

class Permiso extends Conexion {
    private $id_permiso;
    private $menu;
    private $titulo;
    private $url;
    private $icono;
    private $padre;
    private $estado;

    public function getId_permiso()
    {
        return $this->id_permiso;
    }
    
    public function setId_permiso($id_permiso)
    {
        $this->id_permiso = $id_permiso;
        return $this;
    }

    public function getMenu()
    {
        return $this->menu;
    }
    
    public function setMenu($menu)
    {
        $this->menu = $menu;
        return $this;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }
    
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }
    
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getIcono()
    {
        return $this->icono;
    }
    
    public function setIcono($icono)
    {
        $this->icono = $icono;
        return $this;
    }

    public function getPadre()
    {
        return $this->padre;
    }
    
    public function setPadre($padre)
    {
        $this->padre = $padre;
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

    public function agregar() {
        $this->beginTransaction();
        try {
            $sql = "SELECT id_permiso FROM permiso WHERE titulo_interfaz = :0";
            $padre = $this->consultarValor($sql,array($this->getPadre()));

            $campos_valores = 
            array(  "es_menu_interfaz"=>$this->getMenu(),
                    "titulo_interfaz"=>$this->getTitulo(),
                    "url"=>($this->getMenu()== "false" ? NULL : '../'.$this->getUrl()),
                    "estado"=>'A',
                    "icono_interfaz"=>$this->getIcono(),
                    "padre"=> $padre );
            // EN EL "padre"=> ($this->getMenu() == "true" ? true : NULL ) cuando es verdad poner el numero de superior
            $this->insert("permiso", $campos_valores);
            $this->commit();
            return array("rpt"=>true,"estado"=>true,"msj"=>"Se agregado exitosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function editar() {
        $this->beginTransaction();
        try {
            $sql = "SELECT id_permiso FROM permiso WHERE titulo_interfaz = :0";
            $padre = $this->consultarValor($sql,array($this->getPadre()));

            $campos_valores = 
            array(  "es_menu_interfaz"=>$this->getMenu(),
                    "titulo_interfaz"=>$this->getTitulo(),
                    "url"=>($this->getMenu()== "false" ? NULL : '../'.$this->getUrl()),
                    "estado"=>'A',
                    "icono_interfaz"=>$this->getIcono(),
                    "padre"=> $padre );
            // EN EL "padre"=> ($this->getMenu() == "true" ? true : NULL ) cuando es verdad poner el numero de superior 
            $campos_valores_where = 
            array(  "id_permiso"=>$this->getId_permiso());

            $this->update("permiso", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"estado"=>true,"msj"=>"Se actualizado exitosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }


    public function listar(){
        try {
            $sql = "SELECT * FROM permiso WHERE estado = 'A'";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT p.*,(SELECT titulo_interfaz FROM permiso WHERE id_permiso = p.padre) as superior FROM permiso p 
                    WHERE  p.estado = 'A' AND p.id_permiso = :0";
            $resultado = $this->consultarFila($sql,array($this->getId_permiso()));         


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
            array(  "estado"=>$this->getEstado());

            $campos_valores_where = 
            array(  "id_permiso"=>$this->getId_permiso());

            $this->update("permiso", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se inactivado existosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    

    public function raiz($padre = NULL){   
        $sql = "SELECT id_permiso,titulo_interfaz as text FROM permiso WHERE padre";    
        if ($padre == NULL){
            $sql .= " IS NULL";
            $hijos = $this->consultarFilas($sql);
            $padre = array("id_permiso"=>0);
        } else {
            $sql .= " = :0";
            $hijos = $this->consultarFilas($sql, [$padre["id_permiso"]]);
        }

        if (count($hijos) > 0){
            $padre["children"] = array();
            foreach ($hijos as $key => $value) {   
                array_push($padre["children"], $this->raiz($value));
            }

        }
        return array_splice($padre, 1);
    }

    public function menu(){
        return $this->raiz()["children"];
    }

}