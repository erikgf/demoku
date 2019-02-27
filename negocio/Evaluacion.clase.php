<?php

require_once '../datos/Conexion.clase.php';

class Evaluacion extends Conexion {
    private $idEvaluador;
    private $idEvaluacion;
    private $idCampaña;
    
    public function getIdEvaluador()
    {
        return $this->idEvaluador;
    }
    
    public function setIdEvaluador($idEvaluador)
    {
        $this->idEvaluador = $idEvaluador;
        return $this;
    }

    public function getIdEvaluacion()
    {
        return $this->idEvaluacion;
    }
    
    
    public function setIdEvaluacion($idEvaluacion)
    {
        $this->idEvaluacion = $idEvaluacion;
        return $this;
    }

    public function getIdCampaña()
    {
        return $this->idCampaña;
    }
    
    
    public function setIdCampaña($idCampaña)
    {
        $this->idCampaña = $idCampaña;
        return $this;
    }

    public function listarCampos(){
        try {

            $sql = "SELECT * FROM  fn_listar_campos_para_evaluacion();";
            $campos = $this->consultarFilas($sql);

            return array("rpt"=>true,"data"=>$campos);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerCamposProgramados($idEval){
        try {
            $arParam =  array($idEval);
            $sql = "SELECT distinct(e.id_evaluacion),cp.nombre_campo as nombre, si.id_tipo_riego as tipo_riego,
                        (SELECT COUNT(*) FROM evaluacion_umd
                            WHERE id_evaluador_asignado = ev_u.id_evaluador_asignado
                             AND id_campaña = ev_u.id_campaña
                             AND id_evaluacion = e.id_evaluacion) as cantidad,
                        (CASE WHEN si.id_tipo_riego=1 THEN 'VÁLVULAS' ELSE 'CUARTELES' END) as tipo_umd
                        FROM evaluacion e
                        JOIN evaluacion_umd ev_u ON ev_u.id_campaña = e.id_campaña
                        JOIN campaña ca ON ca.id_campaña = ev_u.id_campaña 
                        JOIN siembra si ON si.id_siembra = ca.id_siembra AND si.estado = 'A'
                        JOIN campo cp ON cp.id_campo = si.id_campo AND cp.estado = 'A'
                        WHERE  ca.estado = 'A' AND e.estado = 'A'
                            AND ev_u.id_evaluador_asignado = :0";
            $campos = $this->consultarFilas($sql,$arParam);

            $sql = "SELECT COUNT(distinct(cp.id_campo))
                FROM evaluacion e
                JOIN evaluacion_umd ev_u ON ev_u.id_campaña = e.id_campaña AND e.id_evaluacion = ev_u.id_evaluacion
                JOIN campaña ca ON ca.id_campaña = ev_u.id_campaña
                JOIN siembra si ON si.id_siembra = ca.id_siembra 
                JOIN campo cp ON cp.id_campo = si.id_campo
                WHERE  ca.estado = 'A' AND e.estado = 'A'
                    AND ev_u.id_evaluador_asignado = :0
                    ORDER BY 1";
            $numero_campos = $this->consultarValor($sql,$arParam);

            return array("rpt"=>true,"data"=>array("campos"=>$campos, "numero_campos"=>$numero_campos));
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerCamposProgramadosVer($idUsu, $isEval){
        try {
            $arParam =  array($idUsu);

            $sql = "SELECT distinct(e.id_evaluacion) as id_evaluacion,cp.nombre_campo as nombre, 
            si.id_tipo_riego as tipo_riego,
            (SELECT COUNT(*) FROM evaluacion_umd 
                WHERE id_campaña = ev_u.id_campaña AND id_evaluacion = e.id_evaluacion 
                      AND estado_evaluacion) as cantidad_completadas,
            (SELECT COUNT(*) FROM evaluacion_umd 
                WHERE  id_campaña = ev_u.id_campaña  AND id_evaluacion = e.id_evaluacion) as cantidad,
            (CASE WHEN si.id_tipo_riego=1 THEN 'VÁLVULAS CONCLUÍDAS' ELSE 'CUARTELES CONCLUÍDOS' END) as tipo_umd
            FROM evaluacion e
            JOIN evaluacion_umd ev_u ON ev_u.id_campaña = e.id_campaña
            JOIN campaña ca ON ca.id_campaña = ev_u.id_campaña
            JOIN siembra si ON si.id_siembra = ca.id_siembra 
            JOIN campo cp ON cp.id_campo = si.id_campo
            WHERE  ca.estado = 'A' AND e.estado = 'A' AND ". ($isEval ? ' ev_u.id_evaluador_asignado = :0 ' : ' e.id_supervisor_asignado = :0'). " ORDER BY 1 " ;

            $campos = $this->consultarFilas($sql,$arParam);

            if ($isEval){

                $sql = "SELECT COUNT(distinct(cp.id_campo))
                FROM evaluacion e
                JOIN evaluacion_umd ev_u ON ev_u.id_campaña = e.id_campaña AND e.id_evaluacion = ev_u.id_evaluacion
                JOIN campaña ca ON ca.id_campaña = ev_u.id_campaña
                JOIN siembra si ON si.id_siembra = ca.id_siembra 
                JOIN campo cp ON cp.id_campo = si.id_campo
                WHERE  ca.estado = 'A' AND e.estado = 'A'
                    AND ev_u.id_evaluador_asignado = :0";

            } else {
                $sql = "SELECT COUNT(*)
                        FROM evaluacion e
                        WHERE e.id_supervisor_asignado = :0 
                        AND e.estado = 'A'";
            }

             $numero_campos = $this->consultarValor($sql,$arParam);


            return array("rpt"=>true,"data"=>array("campos"=>$campos, "numero_campos"=>$numero_campos));
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerCamposProgramadosSupervisor($idSup){
        try {
            $arParam =  array($idSup);

            $sql = "SELECT
                    distinct (id_evaluacion) as id_evaluacion,
                    cp.nombre_campo as nombre,
                    si.id_tipo_riego as tipo_riego,
                    (SELECT COUNT(*) FROM evaluacion_umd 
                    WHERE id_campaña = c.id_campaña AND id_evaluacion = e.id_evaluacion AND estado_evaluacion) as cantidad_completadas,
                    (SELECT COUNT(*) FROM evaluacion_umd 
                    WHERE  id_campaña = c.id_campaña AND id_evaluacion = e.id_evaluacion) as cantidad,
                    (CASE WHEN si.id_tipo_riego=1 THEN 'VÁLVULAS CONCLUÍDAS' ELSE 'CUARTELES CONCLUÍDOS' END) as tipo_umd
                            FROM evaluacion e 
                            INNER JOIN campaña c ON c.id_campaña = e.id_campaña AND c.estado = 'A'
                            INNER JOIN siembra si ON si.id_siembra = c.id_siembra AND si.estado = 'A'
                            INNER JOIN campo cp ON cp.id_campo = si.id_campo AND cp.estado = 'A'
                            WHERE e.estado = 'A' AND e.id_supervisor_asignado = :0";

            $campos = $this->consultarFilas($sql,$arParam);

            $sql = "SELECT 
                    COUNT(distinct (e.id_evaluacion))
                    FROM evaluacion e
                    WHERE e.estado = 'A' AND e.id_supervisor_asignado = :0";
            $numero_campos = $this->consultarValor($sql,$arParam);

            return array("rpt"=>true,"data"=>array("campos"=>$campos, "numero_campos"=>$numero_campos));
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    private function cmnObtenerValvulas($idEvaluacion, $idEval, $ver = false, $idSup = NULL)
    {
        $arWhere = [$idEvaluacion, $idEval];

        $sql = "SELECT ca.nombre_campo as nombre,
                    (SELECT COUNT(*) FROM evaluacion_umd WHERE 
                    id_campaña = c.id_campaña AND id_evaluacion = e.id_evaluacion
                    AND id_evaluador_asignado = :1) as total_valvulas,  
                    (SELECT  COUNT(*) From evaluacion_umd
                    WHERE id_campaña = c.id_campaña AND id_evaluacion = e.id_evaluacion AND estado_evaluacion                    
                    ) as evaluados_valvulas                    
                    FROM campaña c                    
                    JOIN siembra si ON si.id_siembra = c.id_siembra AND si.estado = 'A'
                    JOIN campo ca ON ca.id_campo = si.id_campo AND ca.estado = 'A'
                    JOIN evaluacion e ON e.id_campaña = c.id_campaña AND e.estado = 'A'
                    WHERE c.estado = 'A' AND e.id_evaluacion = :0;";

        $campo = $this->consultarFila($sql, $arWhere);
            
        $sql = "SELECT 
                ca_u.numero_nivel_tres as correlativo,
                ev_u.id_umd as id_umd, 
                ev_u.id_evaluacion
                FROM evaluacion_umd ev_u
                INNER JOIN campaña_umd ca_u ON ca_u.id_umd = ev_u.id_umd AND ev_u.id_campaña = ca_u.id_campaña
                WHERE ev_u.id_evaluacion = :0 
                ".($ver ? " " : " AND NOT ev_u.estado_evaluacion").                
                " AND ev_u.id_evaluador_asignado = :1
                ORDER BY numero_nivel_tres::integer ASC";

        $campo["valvulas"] = $this->consultarFilas($sql, $arWhere);

        return $campo;        
    }

    public function obtenerValvulas($idEvaluacion, $idEval)
    {
        try {
            $campo = $this->cmnObtenerValvulas($idEvaluacion, $idEval);
            return array("rpt"=>true,"data"=>$campo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerValvulasVer($idEvaluacion, $idEval, $idSup = NULL)
    {
        try {
            $campo = $this->cmnObtenerValvulas($idEvaluacion, $idEval, true , $idSup);
            return array("rpt"=>true,"data"=>$campo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerValvulasReasignar($idEvaluacion, $idSup)
    {
        try {
            
            $campo = $this->consultarFila("SELECT fn_get_campo_nombre_x_evaluacion(:0) as nombre", [$idEvaluacion]);

            $sql = "SELECT  
                    cu.id_umd,
                    cu.numero_nivel_tres as correlativo,
                    eu.id_evaluacion
                    FROM
                    evaluacion_umd eu
                    INNER JOIN campaña_umd cu ON cu.id_campaña = eu.id_campaña AND eu.id_umd = cu.id_umd
                    WHERE id_evaluacion = :0 AND NOT estado_evaluacion AND numero_nivel_tres IS NOT NULL ORDER BY 2;
                    ";

            $campo["valvulas"] = $this->consultarFilas($sql,  array($idEvaluacion));
            
            return array("rpt"=>true,"data"=>$campo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerCuartelesReasignar($idEvaluacion, $idSup)
    {
        try {
            $campo = $this->consultarFila("SELECT fn_get_campo_nombre_x_evaluacion(:0)  as nombre", [$idEvaluacion]);

            $sql = "SELECT  
                    distinct cu.numero_nivel_uno as numero_jiron
                    FROM
                    evaluacion_umd eu
                    INNER JOIN campaña_umd cu ON cu.id_campaña = eu.id_campaña AND eu.id_umd = cu.id_umd
                    WHERE id_evaluacion = :0 AND NOT estado_evaluacion AND numero_nivel_tres IS NULL;";

            $jirones = $this->consultarFilas($sql, [$idEvaluacion]);

             $sql = "SELECT  
                    cu.id_umd,
                    CONCAT('J',cu.numero_nivel_uno,'-',cu.numero_nivel_dos) as correlativo,
                    eu.id_evaluacion
                    FROM
                    evaluacion_umd eu
                    INNER JOIN campaña_umd cu ON cu.id_campaña = eu.id_campaña AND eu.id_umd = cu.id_umd
                    WHERE id_evaluacion = :0 AND NOT estado_evaluacion AND cu.numero_nivel_uno = :1 AND numero_nivel_tres IS NULL;
                    ";

            foreach ($jirones as $key => $value) {
                $jirones[$key]["cuarteles"] = $this->consultarFilas($sql, [$idEvaluacion,$value["numero_jiron"]]);
            }

            $campo["jirones"] = $jirones;
            
            return array("rpt"=>true,"data"=>$campo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerEvaluadoresDisponiblesReasignar($idEvaluacion, $idSup, $arrUmd)
    {
        try {        
            /*Los demás*/
            $sql = "SELECT distinct COALESCE((eu.id_evaluador_asignado), u.id_usuario) as id_usuario, CONCAT(p.nombres,' ',p.apellidos) as evaluador 
                        FROM usuario u 
                        LEFT JOIN evaluacion_umd eu ON eu.id_evaluador_asignado = u.id_usuario AND eu.id_evaluacion = :0 AND id_umd IN ".$arrUmd."
                        LEFT JOIN  personal p ON u.id_personal = p.id_personal   
                        WHERE id_rol IN (1,2) AND eu.id_evaluador_asignado IS NULL";

            $evaluadores_disponibles = $this->consultarFilas($sql, [$idEvaluacion]);

            //array_unshift($evaluadores_disponibles, array("id_usuario"=>$idSup, "evaluador"=>"YO MISMO"));

            $sqlForeach = "SELECT COUNT(estado_evaluacion) as total, 
            COUNT(CASE estado_evaluacion WHEN false THEN 1 END) as pendientes FROM evaluacion_umd
                            WHERE id_evaluador_asignado = :0";

            foreach ($evaluadores_disponibles as $key => $value) {

                if ($value["id_usuario"] == $idSup){
                    $evaluadores_disponibles[$key]["evaluador"] = "YO MISMO";
                }
                
                $obj = $this->consultarFila($sqlForeach, array($value["id_usuario"]));    
                
                if (count($obj)){
                    $evaluadores_disponibles[$key]["ud_realizadas"] = $obj["total"] - $obj["pendientes"];
                    $evaluadores_disponibles[$key]["ud_por_realizar"] = $obj["pendientes"];
                } else {
                    $evaluadores_disponibles[$key]["ud_realizadas"] = 0;
                    $evaluadores_disponibles[$key]["ud_por_realizar"] = 0;
                }          
            }

            return array("rpt"=>true,"data"=>$evaluadores_disponibles);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function guardarReasignacion($JSONCabecera, $JSONUmd)
    {
        try {
            $this->beginTransaction();

            $cabecera = json_decode($JSONCabecera);
            $uds = json_decode($JSONUmd);

            $nombreEvaluador = 
            $this->consultarValor("SELECT CONCAT(p.nombres,' ',p.apellidos) as nombre_apellidos FROM personal p INNER JOIN usuario u ON u.id_personal = p.id_personal WHERE u.id_usuario = :0",
                                    array($cabecera->p_idEvaluador));  

            $esValvulas = $this->consultarValor("SELECT fn_get_tipo_riego_evaluacion(:0)", [$cabecera->p_idEvaluacion]) == 1;
     
            $msjOk = ($esValvulas ? "Válvulas" : "Cuarteles")." reasignadas a ".$nombreEvaluador.": ";
            $msjSi = "";
            $msjNo = "No se reasignó por ya estar asignado: ";
            $msjNoFlag = false; $msjSiFlag = false;


            $sql = "SELECT  eu.id_evaluador_asignado
                        FROM evaluacion_umd eu
                        WHERE id_evaluacion = :0 AND eu.id_umd = :1";

            foreach ($uds as $key => $value) {            

                $campos_valores = array(
                    "id_umd"=>$value->id_umd,
                    "id_evaluador_nuevo"=> $cabecera->p_idEvaluador,
                    "validado"=>true,
                    "id_evaluacion"=>$cabecera->p_idEvaluacion,                    
                    "id_supervisor_registro"=> $cabecera->p_idSupervisor                    
                    );

                $evaluador_anterior =  $this->consultarValor($sql, [$cabecera->p_idEvaluacion, $value->id_umd]);

                if ($evaluador_anterior == $cabecera->p_idEvaluador){
                    /*Mismo, ergo no hay sentido en reasignar.*/
                    $msjNoFlag = true;
                    $msjNo .= $value->nombre_umd.", ";                
                    
                } else {
                    $msjSiFlag = true;
                    $campos_valores["id_evaluador_anterior"] = $evaluador_anterior;
                    $this->insert("umd_reasignacion_evaluador",$campos_valores);  
                    $msjSi .= $value->nombre_umd.", ";  

                    $campos_valores  = ["id_evaluador_asignado"=> $cabecera->p_idEvaluador];
                    $campos_valores_where = ["id_umd"=> $value->id_umd, "id_evaluacion"=> $cabecera->p_idEvaluacion];

                    $this->update("evaluacion_umd", $campos_valores, $campos_valores_where);
                }

            }

            if ($msjSiFlag){
                $msjOk .= $msjSi;
            } else {
                $msjOk .= "Ninguno. ";
            }

            if ($msjNoFlag){
                $msjOk.= "<br>".$msjNo;
            }

            $this->commit();
            return array("rpt"=>true, "msj"=>$msjOk);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerJirones($idEvaluacion, $idEval = NULL)
    {
        try {
            $sql = "SELECT fn_get_campo_nombre_x_evaluacion(:0) as nombre;";
            $campo = $this->consultarFila($sql, array($idEvaluacion));

            $sql  = "SELECT distinct(numero_nivel_uno) as numero 
                        FROM evaluacion_umd ev_u 
                        INNER JOIN campaña_umd cu ON cu.id_umd = ev_u.id_umd  AND cu.id_campaña = ev_u.id_campaña
                        INNER JOIN evaluacion e ON e.id_campaña = cu.id_campaña
                        WHERE  numero_nivel_tres IS NULL
                        AND e.id_evaluacion = :0";

            if ($idEval == NULL){
                $sql .= " ORDER BY 1";
                $campo["jirones"] = $this->consultarFilas($sql, array($idEvaluacion));
            } else {
                $sql .= " AND ev_u.id_evaluador_asignado = :1 ORDER BY 1";
                $campo["jirones"] = $this->consultarFilas($sql, array($idEvaluacion,$idEval));
            }

            return array("rpt"=>true,"data"=>$campo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    private function cmnObtenerCuarteles($idEvaluacion, $numeroJiron, $idEval, $ver = false, $idSup = NULL)
    {

        $arWhere =[$idEvaluacion,  $numeroJiron, $idEval];

        $sql = "SELECT ca.nombre_campo as nombre ,
                    (SELECT COUNT(*) 
                        FROM evaluacion_umd _eu
                        INNER JOIN campaña_umd _cu 
                        ON _eu.id_campaña = _cu.id_campaña AND _eu.id_umd = _cu.id_umd
                        WHERE 
                        _eu.id_evaluacion = e.id_evaluacion AND _eu.id_evaluador_asignado = :2 
                        AND _cu.numero_nivel_uno = :1) as total_cuarteles,  
                    (SELECT COUNT(*) 
                        FROM evaluacion_umd _eu
                        INNER JOIN campaña_umd _cu 
                        ON _eu.id_campaña = _cu.id_campaña AND _eu.id_umd = _cu.id_umd
                        WHERE 
                        _eu.id_evaluacion = e.id_evaluacion AND _cu.numero_nivel_uno = :1
                        AND estado_evaluacion
                        ) as evaluados_cuarteles   
                    FROM evaluacion e
                    JOIN campaña c ON c.id_campaña = e.id_campaña AND c.estado = 'A'
                    JOIN siembra si ON si.id_siembra = c.id_siembra  AND si.estado ='A'
                    JOIN campo ca ON ca.id_campo = si.id_campo  AND ca.estado = 'A'                    
                    WHERE e.id_evaluacion = :0";

        $cabecera = $this->consultarFila($sql, $arWhere);
                        
        $sql = "SELECT 
                  ca_u.numero_nivel_dos as correlativo,
                  ca_u.numero_nivel_uno as jiron,
                  ca_u.id_umd as id_umd, 
                  e.id_evaluacion
                  FROM evaluacion_umd e_u                      
                  LEFT JOIN campaña_umd  ca_u ON ca_u.id_campaña = e_u.id_campaña AND ca_u.id_umd = e_u.id_umd   
                  LEFT JOIN evaluacion e ON e.id_evaluacion = e_u.id_evaluacion                                             
                  WHERE e_u.id_evaluador_asignado = :2
                    AND ca_u.numero_nivel_tres IS NULL 
                    AND numero_nivel_uno = :1
                    AND e.id_evaluacion = :0
                    ".($ver ? " " : " AND NOT e_u.estado_evaluacion ")."
                  ORDER BY numero_nivel_dos::integer ASC";


        $cuarteles = $this->consultarFilas($sql, $arWhere);
        return ["cuarteles"=>$cuarteles, "cabecera"=>$cabecera];        
    }

    public function obtenerCuarteles($idEvaluacion, $numeroJiron, $idEval)
    {
        try {
            
            $data = $this->cmnObtenerCuarteles($idEvaluacion,$numeroJiron,$idEval);      
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerCuartelesVer($idEvaluacion, $numeroJiron, $idEval, $idSup = NULL)
    {
        try {

            $data = $this->cmnObtenerCuarteles($idEvaluacion,$numeroJiron,$idEval, true, $idSup);   
            return array("rpt"=>true,"data"=>$data);

        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }


    public function obtenerEvaluadoresEvaluacion($idEvaluacion, $idSupervisor)
    {
        try {

            $arParams =  [$idEvaluacion, $idSupervisor];

            $sql = "SELECT 
                cp.nombre_campo,
                si.id_tipo_riego as tipo_riego, 
                (CASE WHEN si.id_tipo_riego = 1 THEN 'valvulas' ELSE 'cuarteles' END) as tipo_ud,
                (SELECT COUNT(distinct(COALESCE(id_evaluador_cierre, id_evaluador_asignado))) 
                    FROM evaluacion_umd 
                    WHERE id_evaluacion = e.id_evaluacion ) as numero_evaluadores
                FROM 
                evaluacion e 
                INNER JOIN campaña ca ON e.id_campaña = ca.id_campaña 
                INNER JOIN siembra si ON si.id_siembra = ca.id_siembra
                INNER JOIN campo cp ON cp.id_campo = si.id_campo
                WHERE e.estado = 'A' AND id_evaluacion = :0 AND e.id_supervisor_asignado = :1";

            $resultado = $this->consultarFila($sql, $arParams);

            $sql = "SELECT 
                    distinct(COALESCE(eu.id_evaluador_cierre, eu.id_evaluador_asignado)) as id_evaluador,
                    CONCAT(p.nombres,' ',p.apellidos) as nombre_apellidos,
                    (SELECT 
                    COUNT(distinct(id_umd))
                    FROM evaluacion_umd 
                    WHERE id_evaluacion = e.id_evaluacion AND COALESCE(id_evaluador_cierre, id_evaluador_asignado ) = COALESCE(eu.id_evaluador_cierre, eu.id_evaluador_asignado)
                    ) as ud_asignadas,
                    (SELECT 
                    COUNT(distinct(id_umd))
                    FROM evaluacion_umd 
                    WHERE id_evaluacion = e.id_evaluacion AND  estado_evaluacion AND COALESCE(id_evaluador_cierre, id_evaluador_asignado ) = COALESCE(eu.id_evaluador_cierre, eu.id_evaluador_asignado)
                    ) as ud_realizadas
                    FROM 
                    evaluacion_umd eu 
                    INNER JOIN evaluacion e ON eu.id_evaluacion = e.id_evaluacion
                    INNER JOIN  usuario u ON u.id_usuario = COALESCE(eu.id_evaluador_cierre, eu.id_evaluador_asignado)
                    INNER JOIN personal p ON p.id_personal = u.id_personal    
                    WHERE e.id_evaluacion = :0 AND e.id_supervisor_asignado = :1";                        

            $resultado["evaluadores"] = $this->consultarFilas($sql, $arParams);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }


    public function listarEvaluacionesCampo(){
        try {

            $sql = "SELECT * FROM  fn_listar_evaluaciones_x_campaña(:0);";
            $liberaciones = $this->consultarFilas($sql, [$this->getIdCampaña()]);

            return array("rpt"=>true,"data"=>$liberaciones);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function listarEvaluacionPorId(){
        try {


            $sql = " SELECT 
                   e.id_evaluacion, 
                   COALESCE(date_part('day',age(e.fecha_fin_evaluacion,e.fecha_inicio_evaluacion))::varchar, (CASE WHEN e.estado = 'P' THEN 'No Iniciado' ELSE 'No terminado' END)) as dias_duracion,
                   cp.nombre_campo, 
                   estado_evaluacion,  
                   e.id_supervisor_asignado as id_supervisor,                               
                   fn_tipo_lib_eval(e.tipo_evaluacion) as tipo_evaluacion,
                   fn_fecha(e.fecha_inicio_evaluacion) as fecha_inicio_evaluacion, 
                   fn_estado_lib_eval(e.estado) as estado,
                   fn_estado_color_lib_eval(e.estado) as estado_color,
                   si.id_tipo_riego,
                   (select COUNT(*) +1 from evaluacion e_
                    WHERE e_.id_campaña = ca.id_campaña AND e_.fecha_inicio_evaluacion > e.fecha_inicio_evaluacion AND estado <> 'P') as numero_evaluacion
                   FROM evaluacion e
                   INNER JOIN campaña ca ON ca.id_campaña = e.id_campaña
                   INNER JOIN siembra si ON si.id_siembra = ca.id_siembra
                   INNER JOIN campo cp ON cp.id_campo = si.id_campo
                   WHERE e.id_evaluacion =:0";

            $cabecera = $this->consultarFila($sql, [$this->getIdEvaluacion()]);            

            $sql = "SELECT fn_obtener_evaluacion_x_id(:0);";
            $unidades_distribucion = $this->consultarValor($sql, [$this->getIdEvaluacion()]);

            return array("rpt"=>true,"data"=>["cabecera"=>$cabecera, "unidades_distribucion"=>$unidades_distribucion]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

     public function obtenerEvaluadores(){
        try {

            $sql = "SELECT u.id_usuario, CONCAT(p.nombres,' ',p.apellidos) as personal FROM 
                    usuario u 
                    INNER JOIN personal p ON p.id_personal = u.id_personal
                    INNER JOIN rol r ON r.id_rol = u.id_rol
                    WHERE u.estado = 'A' AND r.descripcion = 'LIBERADOR' AND p.estado_activo = 'A' AND p.estado_mrcb";

            $data = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }


    public function obtenerEvaluadoresSupervisores(){
        try {

            $sql = "SELECT u.id_usuario, CONCAT(p.nombres,' ',p.apellidos) as personal FROM 
                    usuario u 
                    INNER JOIN personal p ON p.id_personal = u.id_personal
                    INNER JOIN rol r ON r.id_rol = u.id_rol
                    WHERE u.estado = 'A' AND r.descripcion IN ('SUPERVISOR EVALUADOR','EVALUADOR')  AND p.estado_activo = 'A' AND p.estado_mrcb";

            $evaluadores = $this->consultarFilas($sql);

            $sql = "SELECT u.id_usuario, CONCAT(p.nombres,' ',p.apellidos) as personal FROM 
                    usuario u 
                    INNER JOIN personal p ON p.id_personal = u.id_personal
                    INNER JOIN rol r ON r.id_rol = u.id_rol
                    WHERE u.estado = 'A' AND r.descripcion IN ('SUPERVISOR EVALUADOR')  AND p.estado_activo = 'A' AND p.estado_mrcb";

            $supervisores = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>["evaluadores"=>$evaluadores,"supervisores"=>$supervisores]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }


    public function registrarAsignacionEvaluacionPorId($JSONAsignaciones, $idSupervisor = NULL){
        try {
            
            $idSupervisor = $idSupervisor == "" ? NULL : $idSupervisor;
            
            $sql = "SELECT fn_registrar_asignacion_evaluacion(:0,:1,:2)";
            $rpta = $this->consultarValor($sql, [$this->getIdEvaluacion(),  $idSupervisor, $JSONAsignaciones]);

            return array("rpt"=>true,"data"=>json_decode($rpta));
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }


    public function listarEvaluacionCampañasActiva(){
        try {

            $sql = "SELECT * FROM  fn_listar_evaluaciones_activas();";
            $evaluaciones = $this->consultarFilas($sql);

            return array("rpt"=>true,"data"=>$evaluaciones);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerCampoEvaluacion(){
        try {

            $sql = "SELECT * FROM  fn_listar_evaluaciones_activas_x_id(:0);";
            $evaluaciones = $this->consultarFila($sql, [$this->getIdEvaluacion()]);

            return array("rpt"=>true,"data"=>$evaluaciones);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function cargarEvaluacionUmdAvance($turnoJiron, $evaluador){
        try {

            $sql = "SELECT  fn_obtener_umd_x_campaña_avance(:0,:1,:2,:3);";
            $JSONUmd = $this->consultarValor($sql, [$this->getIdCampaña(), $this->getIdEvaluacion(), $turnoJiron, $evaluador]);

            return array("rpt"=>true,"data"=>$JSONUmd);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function listarEvaluacionesMapa(){
        try {

            $sql = "SELECT id_evaluacion, to_char(fecha_inicio_evaluacion,'DD-MM-YYYY') as fecha_inicio_evaluacion, estado, 
                estado_evaluacion, numero_evaluacion
                FROM evaluacion 
                WHERE id_campaña = :0   AND estado <> 'P'
                ORDER BY numero_evaluacion DESC";

            $evaluaciones = $this->consultarFilas($sql, [$this->getIdCampaña()]);

            return array("rpt"=>true,"data"=>$evaluaciones);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerEvaluacionesCampañas($fecha_evaluacion){
        try {

            $sql = "SELECT * FROM fn_listar_evaluaciones_x_dia(:0)";

            $evaluaciones = $this->consultarFilas($sql, [$fecha_evaluacion]);

            return array("rpt"=>true,"data"=>$evaluaciones);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function confirmarEvaluacion(){
        try {

            //la evaluacion debe estar OBLIGATORIAMENTE EN PROGRAMACION Y tener una fecha ANTERIOR al del día actual.
            $sql = "SELECT fecha_inicio_evaluacion < current_date as fecha_correcta,
                           estado <> 'P' as estado_correcto,
                           fecha_inicio_evaluacion
                             FROM evaluacion WHERE id_evaluacion = :0";
            $objValidacion = $this->consultarFila($sql, [$this->getIdEvaluacion()]);

            if ($objValidacion["fecha_correcta"]){
                return array("rpt"=>false,"msj"=>"No se puede confirmar una evaluación que tiene fecha más antigua al día de HOY.  Consulte con el administrador.");                
            }

            if ($objValidacion["estado_correcto"]){
                return array("rpt"=>false,"msj"=>"No se puede confirmar una evaluación que tenga una estado DIFERENTE a PROGRAMADA. Consulte con el administrador.");                                
            }

            $this->update("evaluacion",["estado"=>"C"],["id_evaluacion"=>$this->getIdEvaluacion()]);    

            return array("rpt"=>true,"msj"=>"Evaluación editada correctamente.");
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function anularEvaluacion(){
        try {

            //la evaluacion debe TENER un fecha_inicio_Evalaucion_temporal y Estado Confirmado.
            $sql = "SELECT estado <> 'C' as estado_correcto
                        FROM evaluacion WHERE id_evaluacion = :0";
            $objValidacion = $this->consultarFila($sql, [$this->getIdEvaluacion()]);

            if ($objValidacion["estado_correcto"]){
                return array("rpt"=>false,"msj"=>"No se puede anular una confirmación si el estado de evaluación es DIFERENTE a CONFIRMADA. Consulte con el administrador.");                                
            }

            $this->update("evaluacion",["estado"=>"P"],["id_evaluacion"=>$this->getIdEvaluacion()]);    

            return array("rpt"=>true,"msj"=>"Confirmación de evaluación anulada correctamente");
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function obtenerEvaluacionesPosteriores($fecha_evaluacion){
        try {

            //la evaluacion debe TENER un fecha_inicio_Evalaucion_temporal y Estado Confirmado.

            if ($fecha_evaluacion == null || $fecha_evaluacion == ""){
                return ["rpt"=>true, []];
            }

            $sql = "SELECT * FROM fn_listar_evaluaciones_posterior_x_id(:0,:1)";
            $evaluaciones = $this->consultarFilas($sql, [$this->getIdEvaluacion(), $fecha_evaluacion]);

            return array("rpt"=>true,"data"=>$evaluaciones);
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function editarFechaEvaluacion($nueva_fecha_evaluacion, $arrastrar){
        try {

            //la evaluacion debe TENER un fecha_inicio_Evalaucion_temporal y Estado Confirmado.

            if ($nueva_fecha_evaluacion == null || $nueva_fecha_evaluacion == ""){
                return ["rpt"=>false, "msj"=>"No se ha ingresado una fecha válida."];
            }

            $sql = "SELECT fn_editar_evaluacion_fecha(:0,:1,:2)";
            $rpta = $this->consultarValor($sql, [$this->getIdEvaluacion(), $nueva_fecha_evaluacion, $arrastrar]);

            return array("rpt"=>true,"data"=>json_decode($rpta));
        } catch (Exception $exc) {
            throw $exc;
        }
    }


    public function reporteResultadoEvaluaciones($arrCampos,$f0,$f1){
        try {

            //la evaluacion debe TENER un fecha_inicio_Evalaucion_temporal y Estado Confirmado.
            $cantArr = count($arrCampos);
            if ($cantArr > 0){
                $str = " AND (";
                for ($i=0; $i < $cantArr; $i++) { 
                    if ($i > 0){
                        $str .= " OR ";
                    }
                    $str .= " ca.id_campo = ".$arrCampos[$i];
                }
                $str .= ")";
            }  else {
                $str = "";
            }         

            $sql = "SELECT 
                eu.id_umd,
                e.numero_evaluacion,
                to_char(e.fecha_inicio_evaluacion,'DD-MM-YYYY') as fecha, 
                ca.nombre_campo,
                numero_nivel_uno as modulo_jiron,
                COALESCE(numero_nivel_dos) as turno,
                COALESCE(numero_nivel_tres,numero_nivel_dos) as valvula_cuartel,
                v.descripcion as variedad,
                cu.hectarea_disponible as area,
                eu.indice_poblacion_calculada,
                eu.nivel_infestacion,
                eu.intensidad_daño,     
                tr.descripcion as tipo_riesgo,
                tr.indicador_color,
                s.id_tipo_riego,
                (SELECT SUM(dato_muestreo_1) FROM evaluacion_umd_punto p WHERE p.id_evaluacion = eu.id_evaluacion AND p.id_umd = eu.id_umd) as n_tallos,        
                (SELECT SUM(dato_muestreo_2) FROM evaluacion_umd_punto p WHERE p.id_evaluacion = eu.id_evaluacion AND p.id_umd = eu.id_umd) as n_entrenudos,
                (SELECT SUM(dato_muestreo_3) FROM evaluacion_umd_punto p WHERE p.id_evaluacion = eu.id_evaluacion AND p.id_umd = eu.id_umd) as n_entrenudos_dañados,
                (SELECT SUM(dato_muestreo_4) FROM evaluacion_umd_punto p WHERE p.id_evaluacion = eu.id_evaluacion AND p.id_umd = eu.id_umd) as n_barreno_larva,
                (SELECT SUM(dato_muestreo_5) FROM evaluacion_umd_punto p WHERE p.id_evaluacion = eu.id_evaluacion AND p.id_umd = eu.id_umd) as n_barreno_crisalida,
                (SELECT SUM(dato_muestreo_6) FROM evaluacion_umd_punto p WHERE p.id_evaluacion = eu.id_evaluacion AND p.id_umd = eu.id_umd) as n_mosca_larva_parasito,
                (SELECT SUM(dato_muestreo_7) FROM evaluacion_umd_punto p WHERE p.id_evaluacion = eu.id_evaluacion AND p.id_umd = eu.id_umd) as n_mosca_larva,
                (SELECT SUM(dato_muestreo_8) FROM evaluacion_umd_punto p WHERE p.id_evaluacion = eu.id_evaluacion AND p.id_umd = eu.id_umd) as n_mosca_pupa     
                        FROM evaluacion_umd eu 
                        INNER JOIN evaluacion e ON eu.id_evaluacion = e.id_evaluacion                                               
                        INNER JOIN campaña c ON c.id_campaña = e.id_campaña
                        INNER JOIN campaña_umd cu ON cu.id_umd = eu.id_umd
                        INNER JOIN siembra s ON s.id_siembra = c.id_siembra
                        INNER JOIN variedad_caña v ON v.id_variedad_caña = s.id_variedad_caña
                        INNER JOIN tipo_riesgo tr ON tr.id_tipo_riesgo = eu.id_tipo_riesgo
                        INNER JOIN campo ca ON ca.id_campo = s.id_campo
                WHERE  e.fecha_inicio_evaluacion >= :0 AND e.fecha_inicio_evaluacion <= :1 ".$str."
                ORDER BY fecha, numero_evaluacion,nombre_campo, modulo_jiron, turno, valvula_cuartel  " ;
             
            return $this->consultarFilas($sql, [$f0, $f1]);

        } catch (Exception $exc) {            
            throw $exc;
        }
    }
    
    
}

    