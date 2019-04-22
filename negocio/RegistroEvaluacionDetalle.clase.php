<?php

require_once '../datos/Conexion.clase.php';

class RegistroEvaluacionDetalle extends Conexion {
    private $cod_registro_detalle;
    private $cod_registro;
    private $tipo_evaluacion;

    private $dia_entrenudos;
    private $dia_entrenudos_infestados;
    private $dia_tallos;
    private $dia_tallos_infestados;
    private $dia_larvas_estadio_1;
    private $dia_larvas_estadio_2;
    private $dia_larvas_estadio_3;
    private $dia_larvas_estadio_4;
    private $dia_larvas_estadio_5;
    private $dia_larvas_estadio_6;
    private $dia_crisalidas;
    private $dia_larvas_parasitadas;
    private $dia_billaea_larvas;
    private $dia_billaea_pupas;

    public function getCodRegistro()
    {
        return $this->cod_registro;
    }
    
    public function setCodRegistro($cod_registro)
    {
        $this->cod_registro = $cod_registro;
        return $this;
    }

    public function getCodRegistroDetalle()
    {
        return $this->cod_registro_detalle;
    }
    
    
    public function setCodRegistroDetalle($cod_registro_detalle)
    {
        $this->cod_registro_detalle = $cod_registro_detalle;
        return $this;
    }

    public function getTipoEvaluacion()
    {
        return $this->tipo_evaluacion;
    }
    
    
    public function setTipoEvaluacion($tipo_evaluacion)
    {
        $this->tipo_evaluacion = $tipo_evaluacion;
        return $this;
    }

    public function getDiaEntrenudos()
    {
        return $this->dia_entrenudos;
    }
    
    
    public function setDiaEntrenudos($dia_entrenudos)
    {
        $this->dia_entrenudos = $dia_entrenudos;
        return $this;
    }

    public function getDiaEntrenudosInfestados()
    {
        return $this->dia_entrenudos_infestados;
    }
    
    
    public function setDiaEntrenudosInfestados($dia_entrenudos_infestados)
    {
        $this->dia_entrenudos_infestados = $dia_entrenudos_infestados;
        return $this;
    }

    public function getDiaTallos()
    {
        return $this->dia_tallos;
    }
    
    
    public function setDiaTallos($dia_tallos)
    {
        $this->dia_tallos = $dia_tallos;
        return $this;
    }

    public function getDiaTallosInfestados()
    {
        return $this->dia_tallos_infestados;
    }
    
    
    public function setDiaTallosInfestados($dia_tallos_infestados)
    {
        $this->dia_tallos_infestados = $dia_tallos_infestados;
        return $this;
    }

    public function getDiaLarvasEstadio1()
    {
        return $this->dia_larvas_estadio_1;
    }
    
    
    public function setDiaLarvasEstadio1($dia_larvas_estadio_1)
    {
        $this->dia_larvas_estadio_1 = $dia_larvas_estadio_1;
        return $this;
    }

    public function getDiaLarvasEstadio2()
    {
        return $this->dia_larvas_estadio_2;
    }
    
    
    public function setDiaLarvasEstadio2($dia_larvas_estadio_2)
    {
        $this->dia_larvas_estadio_2 = $dia_larvas_estadio_2;
        return $this;
    }

    public function getDiaLarvasEstadio3()
    {
        return $this->dia_larvas_estadio_3;
    }
    
    
    public function setDiaLarvasEstadio3($dia_larvas_estadio_3)
    {
        $this->dia_larvas_estadio_3 = $dia_larvas_estadio_3;
        return $this;
    }

    public function getDiaLarvasEstadio4()
    {
        return $this->dia_larvas_estadio_4;
    }
    
    
    public function setDiaLarvasEstadio4($dia_larvas_estadio_4)
    {
        $this->dia_larvas_estadio_4 = $dia_larvas_estadio_4;
        return $this;
    }

    public function getDiaLarvasEstadio5()
    {
        return $this->dia_larvas_estadio_5;
    }
    
    
    public function setDiaLarvasEstadio5($dia_larvas_estadio_5)
    {
        $this->dia_larvas_estadio_5 = $dia_larvas_estadio_5;
        return $this;
    }

    public function getDiaLarvasEstadio6()
    {
        return $this->dia_larvas_estadio_6;
    }
    
    
    public function setDiaLarvasEstadio6($dia_larvas_estadio_6)
    {
        $this->dia_larvas_estadio_6 = $dia_larvas_estadio_6;
        return $this;
    }

    public function getDiaCrisalidas()
    {
        return $this->dia_crisalidas;
    }
    
    
    public function setDiaCrisalidas($dia_crisalidas)
    {
        $this->dia_crisalidas = $dia_crisalidas;
        return $this;
    }

    public function getDiaLarvasParasitadas()
    {
        return $this->dia_larvas_parasitadas;
    }
    
    
    public function setDiaLarvasParasitadas($dia_larvas_parasitadas)
    {
        $this->dia_larvas_parasitadas = $dia_larvas_parasitadas;
        return $this;
    }

    public function getDiaBillaeaLarvas()
    {
        return $this->dia_billaea_larvas;
    }
    
    
    public function setDiaBillaeaLarvas($dia_billaea_larvas)
    {
        $this->dia_billaea_larvas = $dia_billaea_larvas;
        return $this;
    }

    public function getDiaBillaeaPupas()
    {
        return $this->dia_billaea_pupas;
    }
    
    
    public function setDiaBillaeaPupas($dia_billaea_pupas)
    {
        $this->dia_billaea_pupas = $dia_billaea_pupas;
        return $this;
    }

    public function editar($JSONData) {
        $this->beginTransaction();
        try {            

            $obj = json_decode($JSONData);
            $this->setid_cargo($obj->id);
            $this->setDescripcion($obj->descripcion);
            
            if ($this->verificarRepetidoEditar()){
                return array("rpt"=>false,"msj"=>"Ya existe este cargo");
            }

            $campos_valores = 
            array(  "descripcion"=>$this->getDescripcion());

            $campos_valores_where = 
            array(  "id_cargo"=>$this->getid_cargo());

            $this->update("cargo", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha actualizado exitosamente.");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc);
        }
    }

    private function obtenerSQLTipoEvaluacion(){
        $sql  = "";

        switch ($this->getTipoEvaluacion()) {
            case '2':
                $sql = " SELECT 
                            (CASE p.tipo_riego WHEN '1' THEN 'M'||p.numero_nivel_1||'-T'||p.numero_nivel_2||'-V'||p.numero_nivel_3 ELSE 'J'||p.numero_nivel_1||'-C'||p.numero_nivel_3 END) as rotulo_parcela,
                            item,
                            dia_entrenudos,
                            dia_entrenudos_infestados,
                            dia_tallos,
                            dia_tallos_infestados,
                            dia_larvas_estadio_1,
                            dia_larvas_estadio_2,
                            dia_larvas_estadio_3,
                            dia_larvas_estadio_4,
                            dia_larvas_estadio_5,
                            dia_larvas_estadio_6,
                            dia_crisalidas,
                            dia_larvas_parasitadas,
                            dia_billaea_larvas,
                            dia_billaea_pupas
                            FROM registros_detalle rd
                            INNER JOIN registros_cabecera rc ON rc.cod_registro = rd.cod_registro
                            INNER JOIN parcela p  ON p.cod_parcela  = rc.cod_parcela
                            WHERE rd.cod_registro_detalle = :0";
                break;
            default:
                break;
        }
        return $sql;
    }

    public function leerEditarDetalle(){
        try {

            $sql = $this->obtenerSQLTipoEvaluacion();

            if ($sql == ""){
                return ["rpt"=>false, "msj"=>"Tipo de evaluación inválida o no disponible."];
            }

            $registro = $this->consultarFila($sql, [$this->getCodRegistroDetalle()]);

            return array("rpt"=>true,"data"=>$registro);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }

    public function editarDetalle(){
        switch ($this->getTipoEvaluacion()) {
            case '2':
                return $this->editarDetalleDiatraea();
            default:
                return array("rpt"=>false,"msj"=>"Tipo evaluación no válido o no disponible.");
        }
    }

    public function editarDetalleDiatraea(){
        try {

            if ($this->getDiaTallos() == 0){
                $larvasIndice = 0;
            } else {
                $larvasIndice = (($this->getDiaLarvasEstadio1() + $this->getDiaLarvasEstadio1() + $this->getDiaLarvasEstadio1() + 
                            $this->getDiaLarvasEstadio1() + $this->getDiaLarvasEstadio1() + $this->getDiaLarvasEstadio1() ) / $this->getDiaTallos());    
            }

            $campos_valores = [
                "dia_entrenudos"=>$this->getDiaEntrenudos(),
                "dia_entrenudos_infestados"=>$this->getDiaEntrenudosInfestados(),
                "dia_tallos"=>$this->getDiaTallos(),
                "dia_tallos_infestados"=>$this->getDiaTallosInfestados(),
                "dia_larvas_estadio_1"=>$this->getDiaLarvasEstadio1(),
                "dia_larvas_estadio_2"=>$this->getDiaLarvasEstadio2(),
                "dia_larvas_estadio_3"=>$this->getDiaLarvasEstadio3(),
                "dia_larvas_estadio_4"=>$this->getDiaLarvasEstadio4(),
                "dia_larvas_estadio_5"=>$this->getDiaLarvasEstadio5(),
                "dia_larvas_estadio_6"=>$this->getDiaLarvasEstadio6(),
                "dia_larvas_indice"=>$larvasIndice,
                "dia_crisalidas"=>$this->getDiaCrisalidas(),
                "dia_larvas_parasitadas"=>$this->getDiaLarvasParasitadas(),
                "dia_billaea_larvas"=>$this->getDiaBillaeaLarvas(),
                "dia_billaea_pupas"=>$this->getDiaBillaeaPupas()
            ];

            $campos_valores_where = [
                "cod_registro_detalle"=>$this->getCodRegistroDetalle()
            ];

            $this->update("registros_detalle",$campos_valores, $campos_valores_where);

            return array("rpt"=>true,"msj"=>"Registro guardado correctamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function eliminarDetalle(){
        try {
            $campos_valores_where = ["cod_registro_detalle"=>$this->getCodRegistroDetalle()];
            $this->delete("registros_detalle", $campos_valores_where);

            return array("rpt"=>true,"msj"=>"Registro eliminado correctamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }


}