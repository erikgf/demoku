<?php

require_once '../datos/Conexion.clase.php';

class Campo extends Conexion {
    private $idEvaluador;
    private $idCampo;

    private $tbl = "_campos_programados";    

    public function getIdEvaluador()
    {
        return $this->idEvaluador;
    }
    
    
    public function setIdEvaluador($idEvaluador)
    {
        $this->idEvaluador = $idEvaluador;
        return $this;
    }

    public function getIdCampo()
    {
        return $this->idCampo;
    }
    
    
    public function setIdCampo($idCampo)
    {
        $this->idCampo = $idCampo;
        return $this;
    }

    public function leerDatos(){
        try {
            $sql = "SELECT * FROM $this->tbl WHERE id_cliente = :0";
            $resultado = $this->consultarFila($sql, array($this->getIdCliente()));
            return array("rpt"=>true,"data"=>$resultado);

        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    private function sqlTipoRiego()
    {
        return "SELECT si.id_tipo_riego = 1 from campaña_umd cu
                    JOIN campaña ca ON ca.id_campaña = cu.id_campaña 
                    JOIN siembra si ON si.id_siembra = ca.id_siembra
                    WHERE cu.id_umd = :0";

    }

    private function obtenerTipoRiegoUmd($idUmd){
        return $this->consultarValor("SELECT * FROM fn_get_tipo_riego_umd(:0)",[$idUmd]);
    }

    public function obtenerCamposProgramadosLiberadorVer($idLib){
        try {
            $arParam =  array($idLib);

            $sql = "SELECT id_campo, nombre, tipo_riego,
                            (CASE WHEN tipo_riego=1 THEN 'VÁLVULAS CONCLUÍDAS' ELSE 'CUARTELES CONCLUÍDOS' END) as tipo_umd,
                            (CASE 
                            WHEN tipo_riego=1  
                            THEN (SELECT COUNT(*) FROM _valvulas_pendientes WHERE estado_liberacion = 0 AND id_campo = c.id_campo AND id_liberador = :0)
                            ELSE
                            (SELECT COUNT(*) FROM _cuarteles_pendientes WHERE estado_liberacion = 0 AND id_campo = c.id_campo AND id_liberador = :0)
                            END)
                            as cantidad_completadas,
                            (CASE 
                            WHEN tipo_riego=1  
                            THEN (SELECT COUNT(*) FROM _valvulas_pendientes WHERE id_campo = c.id_campo AND id_liberador = :0)
                            ELSE
                            (SELECT COUNT(*) FROM _cuarteles_pendientes WHERE id_campo = c.id_campo AND id_liberador = :0)
                            END)
                            as cantidad
                            FROM $this->tbl c WHERE id_liberador = :0";
            $campos = $this->consultarFilas($sql,$arParam);

            $sql = "SELECT COUNT(*) FROM $this->tbl WHERE id_liberador = :0";
            $numero_campos = $this->consultarValor($sql,$arParam);

            return array("rpt"=>true,"data"=>array("campos"=>$campos, "numero_campos"=>$numero_campos));
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerCamposProgramadosLiberador($idLib){
        try {
            $arParam =  array($idLib);
            $sql = "SELECT id_campo, nombre, tipo_riego,
                            (CASE WHEN tipo_riego=1 THEN 'VÁLVULAS' ELSE 'CUARTELES' END) as tipo_umd,
                           (CASE 
                            WHEN tipo_riego=1  
                            THEN (SELECT COUNT(*) FROM _valvulas_pendientes WHERE id_campo = c.id_campo AND id_liberador = :0)
                            ELSE
                            (SELECT COUNT(*) FROM _cuarteles_pendientes WHERE id_campo = c.id_campo AND id_liberador = :0)
                            END)
                            as cantidad
                            FROM $this->tbl c  WHERE id_liberador = :0";
            $campos = $this->consultarFilas($sql,$arParam);

            $sql = "SELECT COUNT(*) FROM $this->tbl WHERE id_liberador = :0";
            $numero_campos = $this->consultarValor($sql,$arParam);

            return array("rpt"=>true,"data"=>array("campos"=>$campos, "numero_campos"=>$numero_campos));
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }


    public function obtenerCamposSupervisor($idSupervisor){
        try {
            $sql = "SELECT * FROM $this->tbl  WHERE id_supervisor = :0";
            $resultado = $this->consultarFilas($sql, array($idSupervisor));
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }
   

    public function obtenerValvulasLiberadorVer($idCampo, $idLib)
    {
        try {

            $sql = "SELECT nombre,
                        (SELECT COUNT(*) FROM _valvulas_pendientes 
                        WHERE id_campo = _cp.id_campo) as total_valvulas,  
                        (SELECT COUNT(*) FROM _valvulas_pendientes 
                        WHERE id_campo = _cp.id_campo AND estado_liberacion = 0) as liberadas_valvulas,
                        moscas_asignadas                   
                         FROM _campos_programados _cp 
                         WHERE _cp.id_campo = :0";
            $campo = $this->consultarFila($sql, array($idCampo));

            $sql = "SELECT distinct(_vp.id_valvula) as id_valvula, correlativo, cantidad_moscas, _vp.id_campo
                    FROM _valvulas_pendientes _vp
                    INNER JOIN puntos_liberacion pl ON pl.id_valvula = _vp.correlativo
                    WHERE _vp.id_liberador = :0 AND _vp.id_campo = :1
                    ORDER BY _vp.id_valvula";

            $campo["valvulas"] = $this->consultarFilas($sql, array($idLib, $idCampo));
            return array("rpt"=>true,"data"=>$campo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerCuartelesLiberadorVer($idCampo, $numeroJiron, $idLib)
    {
        try {

            $sql = "SELECT 
                        (SELECT COUNT(*) FROM _cuarteles_pendientes WHERE estado_liberacion = 0 AND id_jiron = :1) as liberados_cuarteles, 
                        (SELECT COUNT(*) FROM _cuarteles_pendientes WHERE id_jiron = :1) as total_cuarteles
                    FROM _jiron_pendientes jp 
                    WHERE id_campo = :0 AND numero = :1";

            $cabecera = $this->consultarFila($sql, array($idCampo,$numeroJiron));

            $sql = "SELECT distinct(id_jiron) as jiron, correlativo,_cp.id_campo 
                    FROM _cuarteles_pendientes  _cp
                    INNER JOIN puntos_liberacion pl ON pl.jiron = _cp.id_jiron AND pl.cuartel = _cp.correlativo
                    WHERE id_liberador = :0 AND id_jiron = :1 AND _cp.id_campo = :2
                    ORDER BY correlativo";

            $cuarteles = $this->consultarFilas($sql, array($idLib, $numeroJiron,$idCampo));

            return array("rpt"=>true,"data"=>array("cuarteles"=>$cuarteles,"cabecera"=>$cabecera));
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerJironesLiberador($idCampo, $idLib)
    {
        try {

            $sql = "SELECT nombre, moscas_asignadas FROM _campos_programados WHERE id_campo  = :0";
            $campo = $this->consultarFila($sql, array($idCampo));


            $sql = "SELECT numero
                    FROM _jiron_pendientes  
                    WHERE estado_liberacion = 1 AND id_liberador = :0 AND id_campo = :1 
                    ORDER BY numero";

            $campo["jirones"] = $this->consultarFilas($sql, array($idLib, $idCampo));

            return array("rpt"=>true,"data"=>$campo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerValvulasReasignar($idEvaluacion, $idSup)
    {
        try {

            $sql = "SELECT nombre FROM _campos_programados _cp WHERE _cp.id_campo = :0";
            $campo = $this->consultarFila($sql, array($idCampo));

            $sql = "SELECT id_campo,id_valvula, correlativo FROM _valvulas_pendientes
                    WHERE estado_evaluacion = 1 AND id_campo = :0 AND id_evaluador <> :1 
                    ORDER BY correlativo;";

            $campo["valvulas"] = $this->consultarFilas($sql,  array($idCampo,$idSup) );
            return array("rpt"=>true,"data"=>$campo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }
    
    public function obtenerCuartelesLiberador($idCampo, $numeroJiron, $idLib)
    {
        try {

            $sql = "SELECT 
                        (SELECT COUNT(*) FROM _cuarteles_pendientes WHERE estado_liberacion = 0 AND id_jiron = :1) as liberados_cuarteles, 
                        (SELECT COUNT(*) FROM _cuarteles_pendientes WHERE id_jiron = :1) as total_cuarteles
                    FROM _jiron_pendientes jp 
                    WHERE id_campo = :0 AND numero = :1";

            $cabecera = $this->consultarFila($sql, array($idCampo,$numeroJiron));

            $sql = "SELECT id_jiron as jiron, correlativo,id_campo FROM _cuarteles_pendientes  
                    WHERE estado_liberacion = 1 AND id_liberador = :0 AND id_jiron = :1 
                    ORDER BY correlativo";

            $cuarteles = $this->consultarFilas($sql, array($idLib, $numeroJiron));
            return array("rpt"=>true,"data"=>array("cuarteles"=>$cuarteles,"cabecera"=>$cabecera));
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerValvulasSupervisor($idCampo, $numeroModulo, $turno, $idEvaluador, $idSupervisor)
    {
        try {

            $sql = "SELECT *,
                         (SELECT nombre_apellidos FROM personal p INNER JOIN usuario u 
                            ON u.id_personal = p.id_personal WHERE u.id_usuario = :3) as evaluador,
                        (SELECT COUNT(*) FROM _valvulas_pendientes 
                        WHERE id_campo = :0 AND modulo = :1 AND turno = :2 AND id_evaluador = :3) as n_valvulas,  
                        _mp.numero as modulo                      
                         FROM _campos_programados _cp 
                         LEFT JOIN _modulos_pendientes _mp                         
                         ON _cp.id_campo = _mp.id_campo
                         LEFT JOIN _turnos t ON _mp.numero = t.modulo
                         WHERE _cp.id_campo = :0 AND _mp.numero = :1 AND t.turno = :2";
            $campo = $this->consultarFila($sql, array($idCampo,$numeroModulo,$turno,$idEvaluador));

            $sql = "SELECT * FROM _valvulas_pendientes  WHERE id_evaluador = :0 AND id_campo = :1 AND modulo= :2 
                    AND turno =:3 ORDER BY correlativo";
            $campo["valvulas"] = $this->consultarFilas($sql, array($idEvaluador, $idCampo,$numeroModulo,$turno));
            return array("rpt"=>true,"data"=>$campo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }


    public function obtenerLiberacionesUMD($idUmd, $idLiberacion)
    {
        try {
            if ($boolRiego){
                /* Valvulas */
                $sql = "SELECT cp.nombre as nombre_campo, CONCAT('VÁLVULA: ',vp.correlativo) as numero_valvula,
                (SELECT COUNT(*) FROM puntos_liberacion 
                WHERE id_valvula = :0 AND id_campo = cp.id_campo ) 
                as puntos_liberados,
                '1' as tipo_riego,
                cantidad_moscas
                FROM _valvulas_pendientes vp
                INNER JOIN _campos_programados cp ON cp.id_campo = vp.id_campo
                WHERE vp.correlativo = :0 AND vp.id_campo = :1";            
            
                $cabecera = $this->consultarFila($sql, array($paramUno,$idCampo));

            } else {
                $sql = "SELECT cp.nombre as nombre_campo,  CONCAT('JIRÓN: ', ccp.id_jiron) as numero_jiron,
                CONCAT('CUARTEL: ', ccp.correlativo) as numero_cuartel,
                (SELECT COUNT(*) FROM puntos_liberacion WHERE jiron =  jp.numero AND cuartel = ccp.correlativo AND id_campo = cp.id_campo ) 
                as puntos_liberados,
                '2' as tipo_riego,
                cantidad_moscas
                FROM _cuarteles_pendientes ccp
                INNER JOIN _jiron_pendientes jp ON jp.numero = ccp.id_jiron
                INNER JOIN _campos_programados cp ON jp.id_campo = cp.id_campo
                WHERE jp.numero = :0 AND ccp.correlativo = :1 AND cp.id_campo = :2";  

                $p = explode("_",$paramUno);
                $cabecera = $this->consultarFila($sql, array($p[0],$p[1],$idCampo));
            }

            /* TBL */
                if ($boolRiego) {
                    $sql = "SELECT * FROM puntos_liberacion WHERE id_valvula = :0  AND id_campo = :1";
                    $datosPuntos  = $this->consultarFilas($sql, array( $paramUno,$idCampo));
                } else {
                    $sql = "SELECT * FROM puntos_liberacion WHERE jiron = :0 AND cuartel = :1 AND id_campo = :2";
                    $datosPuntos  = $this->consultarFilas($sql, array( $p[0],$p[1],$idCampo));
                }
                        
            return array("rpt"=>true,"data"=>array("cabecera"=>$cabecera, "datos_puntos"=>$datosPuntos));
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerPuntosMuestreoSupervisor($idEvaluador,$idValvula, $obtenerDatosMuestreo = true)
    {
        try {
            /* cabecera */
            $sql = "SELECT cp.nombre as nombre_campo, 
                (SELECT nombre_apellidos FROM personal p INNER JOIN usuario u 
                    ON u.id_personal = p.id_personal WHERE u.id_usuario = :1) as evaluador,
                modulo, turno, vp.correlativo as numero_valvula,
                (SELECT COUNT(distinct(id_valvula)) FROM _puntos_muestreados WHERE id_valvula = :0) as valvulas_realizadas,
                (SELECT COUNT(*) FROM _valvulas_pendientes WHERE id_campo = vp.id_campo) as valvulas_totales,
                (SELECT COUNT(distinct(punto)) FROM _puntos_muestreados WHERE id_valvula = :0) as puntos_muestreados
                FROM _valvulas_pendientes vp
                INNER JOIN _campos_programados cp ON cp.id_campo = vp.id_campo
                WHERE id_valvula = :0";
                
            $cabecera = $this->consultarFila($sql, array($idValvula, $idEvaluador));

            $datosMuestreo = null;
            if ($obtenerDatosMuestreo){
                $sql = "SELECT * FROM dato_muestreo WHERE estado_mrcb = 1";
                $datosMuestreo = $this->consultarFilas($sql);
            }

            /* TBL */
            $puntos = 5;
            $datosPuntos = array($puntos);

            for ($i=0; $i < $puntos ; $i++) { 
                $sql = "SELECT * FROM _puntos_muestreados WHERE punto = :0 AND id_valvula = :1";
                $datosPuntos[$i]  = $this->consultarFilas($sql, array(($i+1), $idValvula));
            }
                        
            return array("rpt"=>true,"data"=>array("cabecera"=>$cabecera, "datos_puntos"=>$datosPuntos, "datos_muestreo"=>$datosMuestreo));
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }    

    public function obtenerEvaluadoresDisponiblesReasignar($idSup)
    {
        try {
            /* Yo mismo */

            $sql = "SELECT id_usuario, 'YO MISMO' as evaluador
                    FROM usuario u INNER JOIN personal p
                    ON p.id_personal = u.id_personal
                    WHERE ID_rol = 1 AND estado = 'A'";

            /*Los demás*/
            $sql = "SELECT id_usuario, p.nombre_apellidos as evaluador
                    FROM usuario u INNER JOIN personal p
                    ON p.id_personal = u.id_personal
                    WHERE ID_rol = 1 AND estado = 'A'";

            $evaluadores_disponibles = $this->consultarFilas($sql);

            array_unshift($evaluadores_disponibles, array("id_usuario"=>$idSup, "evaluador"=>"YO MISMO"));

            $sqlForeach = "SELECT 
                            estado_evaluacion, SUM(total)::int as total
                            FROM 
                            (SELECT
                            'v',estado_evaluacion , COUNT(*) as total
                            FROM _valvulas_pendientes
                             WHERE id_evaluador = :0
                            GROUP BY estado_evaluacion
                            UNION
                            SELECT
                            'c',estado_evaluacion , COUNT(*) as total
                            FROM _cuarteles_pendientes
                             WHERE id_evaluador = :0
                            GROUP BY estado_evaluacion
                            ) as t
                            GROUP BY estado_evaluacion ";

            foreach ($evaluadores_disponibles as $key => $value) {
                
                $obj = $this->consultarFilas($sqlForeach, array($value["id_usuario"]));    
                
                if (count($obj)){
                    $evaluadores_disponibles[$key]["ud_realizadas"] = $obj[0]["total"];
                    $evaluadores_disponibles[$key]["ud_por_realizar"] = $obj[1]["total"];
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

   public function guardarReasignacion($JSONCabecera, $JSONUd)
    {
        try {
            $this->beginTransaction();

            $cabecera = json_decode($JSONCabecera);
            $uds = json_decode($JSONUd);

            $nombreEvaluador = 
            $this->consultarValor("SELECT nombre_apellidos FROM personal p INNER JOIN usuario u ON u.id_personal = p.id_personal WHERE p.id_personal = :0",
                                    array($cabecera->p_idEvaluador));

            $esValvulas = $cabecera->p_tr == 1;

            $sql = $esValvulas ? "SELECT id_evaluador FROM _valvulas_pendientes WHERE correlativo = :0 AND id_campo = :1" 
                                        : "SELECT id_evaluador FROM _cuarteles_pendientes WHERE jiron = :0 AND cuartel = : 1 AND id_campo = :2" ;
     
            $msjOk = ($esValvulas ? "Válvulas" : "Cuarteles")." reasignadas a ".$nombreEvaluador.": ";
            $msjSi = "";
            $msjNo = "No se reasignó por ya estar asignado: ";
            $msjNoFlag = false; $msjSiFlag = false;

            foreach ($uds as $key => $value) {            

                $campos_valores = array(
                    "id_campo"=> $cabecera->p_idCampo,
                    "id_evaluador_nuevo"=> $cabecera->p_idEvaluador,
                    "id_supervisor"=> $cabecera->p_idSupervisor                    
                    );

                if ($esValvulas){
                    $evaluador_anterior = $this->consultarValor($sql, array($value, $cabecera->p_idCampo));
                    $campos_valores["valvula"] = $value;

                } else {
                    $p = explode("_",$value);
                    $evaluador_anterior = $this->consultarValor($sql, array($p[0],$p[1], $cabecera->p_idCampo));
                    $campos_valores["jiron"] = $p[0];
                    $campos_valores["cuartel"] = $p[1];
                }
                
                if ($evaluador_anterior == $cabecera->p_idEvaluador){
                    /*Mismo, ergo no hay sentido en reasignar.*/
                    $msjNoFlag = true;
                    if ($esValvulas){
                        $msjNo .= $value.", ";                
                    } else{
                        $msjNo .= $p[0]." ".$p[1].", ";                
                    }
                    
                } else {
                    $msjSiFlag = true;
                    $campos_valores["id_evaluador_anterior"] = $evaluador_anterior;
                    $this->insert("_reasignacion",$campos_valores);  
                    if ($esValvulas){
                        $msjSi .= $value.", ";      
                    } else{
                        $msjSi .= $p[0]." ".$p[1].", ";                
                    }
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


    public function listarCampos()
    {
        try {

            $sql = "SELECT * FROM fn_listar_campos()";
            $campos = $this->consultarFilas($sql);            
            return array("rpt"=>true,"data"=>$campos);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function verDetalle()
    {
        try {

            $sql = "SELECT * FROM fn_ver_campo(:0)";
            $JSONCampo = $this->consultarValor($sql, [$this->getIdCampo()]); 

            return array("rpt"=>true,"data"=>$JSONCampo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function registrarCampo($nombre, $hectarea, $hectarea_total)
    {
        try {
            $this->beginTransaction();
            $ar = [$nombre, $hectarea, $hectarea_total];
            $data = $this->consultarValor("SELECT fn_registrar_campo(:0,:1,:2)",$ar);

            $this->commit();
            return array("rpt"=>true, "data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }
    
    public function listarCamposCampañaActiva()
    {
        try {

            $sql = "SELECT * FROM fn_listar_campos_campaña_activa() ORDER BY nombre_campo";
            $campos = $this->consultarFilas($sql); 

            return array("rpt"=>true,"data"=>$campos);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function listarCampoAsignarLiberacion($id_campaña)
    {
        try {

            $sql = "SELECT * FROM fn_listar_evaluaciones_liberaciones_x_campaña(:0)";
            $data = $this->consultarFila($sql,[$id_campaña]);     

            if ($data == false){
                return array("rpt"=>true,"data"=>[]);
            };

            $umd = json_decode($data["json_ha"]);

            foreach ($umd as $key => $value) {
                    switch($value->id_tipo_riesgo){
                        case 0:
                        $ha_bajo = $value->hectarea;
                        break;
                        case 1:
                        $ha_medio = $value->hectarea;
                        break;
                        case 2:
                        $ha_alto = $value->hectarea;
                        break;
                    }
            }

            if ($ha_alto == "0.00"){
                $data["moscas_base_alto"] = "0";
            }

            if ($ha_medio == "0.00"){
                $data["moscas_base_medio"] = "0";
            }

            if ($ha_bajo == "0.00"){
                $data["moscas_base_bajo"] = "0";
            }

            $total_rojo_ambar = $data["moscas_base_alto"] * $ha_alto + $data["moscas_base_medio"] * $ha_medio;

            if ($total_rojo_ambar > $data["total_parejas_moscas"]){

            } else {
                if ($ha_bajo > 0.00){
                    $dif = $data["total_parejas_moscas"] - $total_rojo_ambar;
                    $moscas_base_bajo = (float) floor($dif / $ha_bajo);
                    $data["moscas_base_bajo"]= $moscas_base_bajo;
                }
            }


            $data["ha_alto"]= $ha_alto;
            $data["ha_medio"]= $ha_medio;
            $data["ha_bajo"]= $ha_bajo;

            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }


    public function listarCamposMapa()
    {
        try {

            $sql = "select c.id_campo, nombre_campo, hectarea
                FROM campo c
                WHERE c.estado = 'A'
                ORDER BY nombre_campo ASC";
            $campos = $this->consultarFilas($sql);            
            return array("rpt"=>true,"data"=>$campos);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    

    
}

    