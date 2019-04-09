<?php

require_once '../datos/Conexion.clase.php';

class RegistroEvaluacion extends Conexion {
    private $cod_registro;
    private $cod_campo;
    private $fecha_registro;
    private $cod_evaluador;
    private $tipo_evaluacion;
    private $numero_nivel_1;
    private $numero_nivel_2;
    private $numero_nivel_3;

    public function getCodRegistro()
    {
        return $this->cod_registro;
    }
    
    
    public function setCodRegistro($cod_registro)
    {
        $this->cod_registro = $cod_registro;
        return $this;
    }

    public function getCodCampo()
    {
        return $this->cod_campo;
    }
    
    
    public function setCodCampo($cod_campo)
    {
        $this->cod_campo = $cod_campo;
        return $this;
    }

    public function getFechaRegistro()
    {
        return $this->fecha_registro;
    }
    
    
    public function setFechaRegistro($fecha_registro)
    {
        $this->fecha_registro = $fecha_registro;
        return $this;
    }

    public function getCodEvaluador()
    {
        return $this->cod_evaluador;
    }
    
    
    public function setCodEvaluador($cod_evaluador)
    {
        $this->cod_evaluador = $cod_evaluador;
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

    public function getNumeroNivel1()
    {
        return $this->numero_nivel_1;
    }
    
    
    public function setNumeroNivel1($numero_nivel_1)
    {
        $this->numero_nivel_1 = $numero_nivel_1;
        return $this;
    }

    public function getNumeroNivel2()
    {
        return $this->numero_nivel_2;
    }
    
    
    public function setNumeroNivel2($numero_nivel_2)
    {
        $this->numero_nivel_2 = $numero_nivel_2;
        return $this;
    }

    public function getNumeroNivel3()
    {
        return $this->numero_nivel_3;
    }
    
    
    public function setNumeroNivel3($numero_nivel_3)
    {
        $this->numero_nivel_3 = $numero_nivel_3;
        return $this;
    }

    public function agregar($JSONData) {
        $this->beginTransaction();
        try {            

            $obj = json_decode($JSONData);
            $this->setDescripcion($obj->descripcion);

            if ($this->verificarRepetidoAgregar()){
                return array("rpt"=>false,"msj"=>"Ya existe este cargo");
            }

            $campos_valores = 
            array(  "descripcion"=>$this->getDescripcion());

            $this->insert("cargo", $campos_valores);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha agregado exitosamente.");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc);
        }
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

    public function cargarNivelUno(){
        try {
            $sql = "SELECT tipo_riego, cp.cod_campaña
                    FROM campaña cp 
                    INNER JOIN siembra si ON si.cod_siembra = cp.cod_siembra
                    INNER JOIN campo ca ON ca.cod_campo = si.cod_siembra
                    WHERE ca.cod_campo = :0";
            $campaña = $this->consultarFila($sql, [$this->getCodCampo()]);

            $niveles = $this->_obtenerNivel(1, $campaña["cod_campaña"], $campaña["tipo_riego"]);

            return array("rpt"=>true,"data"=>["niveles"=>$niveles, "tipo_riego"=>$campaña["tipo_riego"]]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }

    public function cargarNivelDos(){
        try {
            $sql = "SELECT tipo_riego, cp.cod_campaña
                    FROM campaña cp 
                    INNER JOIN siembra si ON si.cod_siembra = cp.cod_siembra
                    INNER JOIN campo ca ON ca.cod_campo = si.cod_siembra
                    WHERE ca.cod_campo = :0";
            $campaña = $this->consultarFila($sql, [$this->getCodCampo()]);

            if ($campaña["tipo_riego"] == "0"){
                $niveles = $this->_obtenerNivel(3, $campaña["cod_campaña"], $campaña["tipo_riego"], $this->getNumeroNivel1());
            } else {
                $niveles = $this->_obtenerNivel(2, $campaña["cod_campaña"], $campaña["tipo_riego"], $this->getNumeroNivel1());
            }

            return array("rpt"=>true,"data"=>["niveles"=>$niveles, "tipo_riego"=>$campaña["tipo_riego"]]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }

    public function cargarNivelTres(){
        try {

            $sql = "SELECT tipo_riego, cp.cod_campaña
                    FROM campaña cp 
                    INNER JOIN siembra si ON si.cod_siembra = cp.cod_siembra
                    INNER JOIN campo ca ON ca.cod_campo = si.cod_siembra
                    WHERE ca.cod_campo = :0";
            $campaña = $this->consultarFila($sql, [$this->getCodCampo()]);

            $niveles = $this->_obtenerNivel(3, $campaña["cod_campaña"], $campaña["tipo_riego"], $this->getNumeroNivel1(), $this->getNumeroNivel2());

            return array("rpt"=>true,"data"=>["niveles"=>$niveles, "tipo_riego"=>$campaña["tipo_riego"]]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }

    public function obtenerDataFiltro($fechaDesde, $fechaHasta){
        try {

            $sql = "SELECT distinct cp.nombre_campo as descripcion, cp.cod_campo as codigo
                    FROM registros_cabecera rc
                    INNER JOIN parcela p ON p.cod_parcela = rc.cod_parcela
                    INNER JOIN campaña c ON c.cod_campaña = p.cod_campaña
                    INNER JOIN siembra si ON si.cod_siembra = c.cod_siembra
                    INNER JOIN campo cp ON cp.cod_campo = si.cod_campo AND cp.estado_mrcb
                    WHERE fecha_evaluacion BETWEEN '$fechaDesde'::date AND '$fechaHasta'::date
                    ORDER BY descripcion";

            $campos = $this->consultarFilas($sql);

            $sql = "SELECT distinct CONCAT(co.apellidos,' ',co.nombres) as descripcion, u.cod_usuario as codigo
                    FROM registros_cabecera rc
                    INNER JOIN usuario u ON u.cod_usuario = rc.cod_evaluador
                    INNER JOIN colaborador co ON co.cod_colaborador = u.cod_usuario
                    WHERE fecha_evaluacion BETWEEN '$fechaDesde'::date AND '$fechaHasta'::date
                    ORDER BY CONCAT(co.apellidos,' ',co.nombres)";

            $evaluadores = $this->consultarFilas($sql);

            return array("rpt"=>true,"data"=>["campos"=>$campos, "evaluadores"=>$evaluadores]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }

    public function obtenerCabeceras($fechaDesde, $fechaHasta){
        try {

            $sql = "SELECT rc.cod_registro, 
                    ca.nombre_campo,
                    p.numero_nivel_1, p.numero_nivel_2, p.numero_nivel_3,
                    (CASE p.tipo_riego WHEN '1' THEN 'M'||p.numero_nivel_1||'-T'||p.numero_nivel_2||'-V'||p.numero_nivel_3 ELSE 'J'||p.numero_nivel_1||'-C'||p.numero_nivel_3 END) as rotulo_parcela,
                    to_char(fecha_evaluacion,'DD-MM-YYYY') as fecha_evaluacion, 
                    f.descripcion as tipo_evaluacion,
                    CONCAT(col.apellidos,' ',col.nombres) as evaluador,
                    rc.cod_formulario_evaluacion
                    FROM registros_cabecera rc
                    INNER JOIN formulario f ON f.cod_formulario = rc.cod_formulario_evaluacion
                    INNER JOIN parcela p ON p.cod_parcela = rc.cod_parcela
                    INNER JOIN campaña cp ON cp.cod_campaña = p.cod_campaña
                    INNER JOIN siembra si ON si.cod_siembra = cp.cod_siembra
                    INNER JOIN campo ca ON ca.cod_campo = si.cod_campo
                    INNER JOIN usuario u ON u.cod_usuario = rc.cod_evaluador
                    INNER JOIN colaborador col ON col.cod_colaborador = u.cod_usuario 
                    WHERE true ";

            $parametros = [];
            $lastParametro = 0;

            if (strlen($this->getCodCampo()) > 0 && $this->getCodCampo() != "*"){
                $sql .= " AND ca.cod_campo = :$lastParametro ";
                array_push($parametros, $this->getCodCampo());
                $lastParametro++;
            }

            if (strlen($fechaDesde) > 0 && $fechaDesde != ""){
                $sql .= " AND fecha_evaluacion >= :$lastParametro ";
                array_push($parametros, $fechaDesde);
                $lastParametro++;
            }

            if (strlen($fechaHasta) > 0 && $fechaHasta != ""){
                $sql .= " AND fecha_evaluacion <= :$lastParametro ";
                array_push($parametros, $fechaHasta);
                $lastParametro++;
            }

            if (strlen($this->getTipoEvaluacion()) > 0 && $this->getTipoEvaluacion() != "*"){
                $sql .= " AND rc.cod_formulario_evaluacion = :$lastParametro ";
                array_push($parametros, $this->getTipoEvaluacion());
                $lastParametro++;
            }

            if (strlen($this->getCodEvaluador()) > 0 && $this->getCodEvaluador() != "*"){
                $sql .= " AND rc.cod_evaluador = :$lastParametro ";
                array_push($parametros, $this->getCodEvaluador());
                $lastParametro++;
            }

            if (strlen($this->getNumeroNivel1()) > 0){
                $sql .= " AND p.numero_nivel_1 = :$lastParametro ";
                array_push($parametros, $this->getNumeroNivel1());
                $lastParametro++;
            }

            if (strlen($this->getNumeroNivel2()) > 0){
                $sql .= " AND p.numero_nivel_2 = :$lastParametro ";
                array_push($parametros, $this->getNumeroNivel2());
                $lastParametro++;
            }

            if (strlen($this->getNumeroNivel3()) > 0){
                $sql .= " AND p.numero_nivel_3 = :$lastParametro ";
                array_push($parametros, $this->getNumeroNivel3());
            }

            $sql .= " ORDER BY rc.cod_registro DESC";
            $resultado = $this->consultarFilas($sql,$parametros);

            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }

    private function _obtenerNivel($NIVEL, $codCampaña, $tipo_riego, $nivel1 = NULL, $nivel2 = NULL){
        //Solo obtiene una lista con un solo campo llamado: DESCRIPCION
        $parametros = [$codCampaña];
        $sqlW = "";

        $nParam = 1;

        if ($NIVEL >= 2){
            if ($tipo_riego == "1"){
                $sqlW .= " AND numero_nivel_1 = :$nParam ";
                array_push($parametros, $nivel1);
                $nParam++;
            }
        }

        if ($NIVEL >= 3){
            if ($tipo_riego == "0"){
                $sqlW .= " AND numero_nivel_1 = :$nParam ";
                array_push($parametros, $nivel1);
                $nParam++;
            } else {
                $sqlW .= " AND numero_nivel_2 = :$nParam ";
                array_push($parametros, $nivel2);
                $nParam++;
            }
        }

        $sql =  "SELECT  *
                    FROM 
                    (SELECT distinct(numero_nivel_".$NIVEL.") as descripcion
                        FROM parcela p
                        WHERE cod_campaña = :0 ".$sqlW." ) t
                    ORDER BY t.descripcion::integer";

        return $this->consultarFilas($sql, $parametros);
    }

    public function leerEditarCabecera(){
        try {

            $sql = "SELECT
                rc.cod_registro, 
                rc.cod_evaluador,
                rc.cod_parcela,
                cp.cod_campaña,
                ca.cod_campo,
                p.numero_nivel_1, p.numero_nivel_2, p.numero_nivel_3,
                fecha_evaluacion,
                f.descripcion as formulario,
                si.tipo_riego,
                ca.nombre_campo
                FROM registros_cabecera  rc
                INNER JOIN parcela p ON p.cod_parcela = rc.cod_parcela
                INNER JOIN formulario f ON f.cod_formulario = rc.cod_formulario_evaluacion
                INNER JOIN campaña cp ON cp.cod_campaña = p.cod_campaña
                INNER JOIN siembra si ON si.cod_siembra = cp.cod_siembra
                INNER JOIN campo ca ON ca.cod_campo = si.cod_campo
                WHERE cod_registro = :0";

           $registro = $this->consultarFila($sql, [$this->getCodRegistro()]);

           /*si el tipo riego lo permite, enviar la data correspondiente*/
           $data_1 = $this->_obtenerNivel(1, $registro["cod_campaña"], $registro["tipo_riego"]); 
           if ($registro["tipo_riego"] == "0"){
                $data_2 = [];
           } else {
                $data_2 = $this->_obtenerNivel(2, $registro["cod_campaña"], $registro["tipo_riego"], $registro["numero_nivel_1"]);
           }

           $data_3 =  $this->_obtenerNivel(3, $registro["cod_campaña"], $registro["tipo_riego"], $registro["numero_nivel_1"], $registro["numero_nivel_2"]);

            return array("rpt"=>true,"data"=>["registro"=>$registro, "data_1"=>$data_1, "data_2"=>$data_2, "data_3"=>$data_3]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }

    public function eliminarCabecera(){
        try {

            $campos_valores_where = ["cod_registro"=>$this->getCodRegistro()];
            $this->delete("registros_cabecera", $campos_valores_where);

            $campos_valores_where = ["cod_registro"=>$this->getCodRegistro()];
            $this->delete("registros_detalle", $campos_valores_where);

            return array("rpt"=>true,"msj"=>"Registro eliminado correctamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }


    public function editarCabecera(){
        try {
            /*1.- obtener parcela*/
            /*2.- ralziar bloque de posibles cambios*/
            $sql = "SELECT cod_parcela FROM parcela p
                    INNER JOIN campaña cp ON cp.cod_campaña = p.cod_campaña
                    INNER JOIN siembra si ON si.cod_siembra = cp.cod_siembra
                    INNER JOIN campo ca ON ca.cod_campo = si.cod_campo
                    WHERE p.numero_nivel_1 = :0 AND p.numero_nivel_2 = :1 AND p.numero_nivel_3 = :2 AND ca.cod_campo = :3";


            $codParcela = $this->consultarValor($sql, 
                                [   $this->getNumeroNivel1(), 
                                    $this->getNumeroNivel2() == NULL ? "" : $this->getNumeroNivel2(), 
                                    $this->getNumeroNivel3(),
                                    $this->getCodCampo()]);

            $campos_valores = [
                "cod_parcela"=>$codParcela,
                "fecha_evaluacion"=>$this->getFechaRegistro(),
                "cod_evaluador"=>$this->getCodEvaluador(),
            ];

            $campos_valores_where = [
                "cod_registro"=>$this->getCodRegistro()
            ];

            $campos_valores_where = ["cod_registro"=>$this->getCodRegistro()];
            $this->update("registros_cabecera",$campos_valores, $campos_valores_where);

            return array("rpt"=>true,"msj"=>"Registro guardado correctamente");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;            
        }
    }

    public function verDetalles(){
        try {

            $sql = "SELECT 
                    rd.cod_registro_detalle,
                    rd.item
                    FROM registros_detalle rd
                    WHERE rd.cod_registro = :0
                    ORDER BY rd.item ";
           
            $resultado = $this->consultarFilas($sql,[$this->getCodRegistro()]);

            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }


}
