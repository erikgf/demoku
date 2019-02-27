<?php

require_once '../datos/Conexion.clase.php';

class CriterioAHP extends Conexion {
    private $id_criterio_ahp;
    private $nombre_criterio;
    private $estado_mrcb;

    public function getid_criterio_ahp()
    {
        return $this->id_criterio_ahp;
    }
    
    public function setid_criterio_ahp($id_criterio_ahp)
    {
        $this->id_criterio_ahp = $id_criterio_ahp;
        return $this;
    }

    public function getnombre_criterio()
    {
        return $this->nombre_criterio;
    }
    
    public function setnombre_criterio($nombre_criterio)
    {
        $this->nombre_criterio = $nombre_criterio;
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
            array(  "nombre_criterio"=>$this->getnombre_criterio());

            $this->insert("criterio_ahp", $campos_valores);

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
            array(  "nombre_criterio"=>$this->getnombre_criterio() );

            $campos_valores_where = 
            array(  "id_criterio_ahp"=>$this->getid_criterio_ahp());

            $this->update("criterio_ahp", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se actualizado exitosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }


  public function habilitarCriterio($id_criterio,$tipo)
  {
      try {

          $sql = "SELECT valor FROM variable_general WHERE nombre = 'criterios_activos_minimos'";
          $cantidadMinimaCriterioActivo = $this->consultarValor($sql);

          $sql = "SELECT COUNT(*) FROM criterio_ahp WHERE estado_activacion = 'A'";
          $cantidadCriteriosActivos = $this->consultarValor($sql);

          if ($cantidadCriteriosActivos <= $cantidadMinimaCriterioActivo && $tipo == 'I'){
            return array("rpt"=>false, "reactivar"=>true, "msj"=>"No puede tener menos de ".$cantidadMinimaCriterioActivo." criterios activos.");
          }

          $campos_valores =["estado_activacion" => $tipo];
          $campos_valores_where = ["id_criterio_ahp" => $id_criterio];
          $this->update("criterio_ahp",$campos_valores,$campos_valores_where);

          $sql = "SELECT nombre_criterio FROM criterio_ahp WHERE id_criterio_ahp = :0";
          $nombre_criterio = $this->consultarValor($sql, [$id_criterio]);

          return array("rpt"=>true, "msj"=>"Criterio ".strtoupper($nombre_criterio).": <b>".($tipo == 'I' ? "DESACTIVADO" : "ACTIVADO"."</b>"));
        } catch (Exception $exc) {
          return array("rpt"=>false,"msj"=>$exc);
          throw $exc;
        }
  }

  public function guardarCambiosMatriz($JSONArregloCambios)
  {
      try {
          $this->beginTransaction();
          $arregloCambios = json_decode($JSONArregloCambios);

          foreach ($arregloCambios as $key => $value) {
            $arKey = split("-",$key);

            $sql = "SELECT valor_criterio FROM matriz_ahp WHERE id_criterio_ahp_uno = :0 and id_criterio_ahp_dos = :1";
            $logAntiguo = $this->consultarValor($sql, array($arKey[0], $arKey[1]));

            $campos_valores = array(
                "id_criterio_ahp_uno"=>$arKey[0],
                "id_criterio_ahp_dos"=>$arKey[1],
                "id_usuario_log"=>1,
                "valor_anterior"=>$logAntiguo,
                "valor_nuevo"=>$value->x);
            $this->insert("matriz_ahp_log", $campos_valores);

            $campos_valores = array("valor_criterio"=>$value->x);
            $campos_valores_where = array("id_criterio_ahp_uno"=> $arKey[0],
                                            "id_criterio_ahp_dos"=> $arKey[1]);

            $this->update("matriz_ahp", $campos_valores, $campos_valores_where);

            $logAntiguo = $this->consultarValor($sql, array($arKey[1], $arKey[0]));

            $campos_valores = array(
                "id_criterio_ahp_uno"=>$arKey[1],
                "id_criterio_ahp_dos"=>$arKey[0],
                "id_usuario_log"=>1,
                "valor_anterior"=>$logAntiguo,
                "valor_nuevo"=>$value->r);

            $this->insert("matriz_ahp_log", $campos_valores);

            $campos_valores = array("valor_criterio"=>$value->r);
            $campos_valores_where = array("id_criterio_ahp_uno"=> $arKey[1],
                                            "id_criterio_ahp_dos"=> $arKey[0]);

            $this->update("matriz_ahp", $campos_valores, $campos_valores_where);
            

          };
          $this->commit();

          return array("rpt"=>true, "msj"=>"Registros  guardados correctamente.");

        } catch (Exception $exc) {
          return array("rpt"=>false,"msj"=>$exc);
          throw $exc;
        }
  }

  public function listarM(){
        try {
            $sql = "SELECT * FROM criterio_ahp WHERE estado_mrcb = 1";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"msj"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
  }

    public function leerDatos(){
        try {
            $sql = "SELECT * FROM criterio_ahp WHERE estado_mrcb = 1 AND id_criterio_ahp = :0";
            $resultado = $this->consultarFila($sql,array($this->getid_criterio_ahp()));
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
            array(  "id_criterio_ahp"=>$this->getid_criterio_ahp());

            $this->update("criterio_ahp", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se inactivado existosamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }
}