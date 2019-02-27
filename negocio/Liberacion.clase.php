<?php

require_once '../datos/Conexion.clase.php';

class Liberacion extends Conexion {    
    private $idLiberacion;
    private $idCampaña;

    public function getIdLiberacion()
    {
        return $this->idLiberacion;
    }
    
    
    public function setIdLiberacion($idLiberacion)
    {
        $this->idLiberacion = $idLiberacion;
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

            $sql = "SELECT * FROM  fn_listar_campos_para_liberacion();";
            $campos = $this->consultarFilas($sql);

            return array("rpt"=>true,"data"=>$campos);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerCamposProgramados($idLib){
        try {
            $arParam =  array($idLib);
            $sql = "SELECT 
                    distinct lu.id_liberacion, cp.nombre_campo as nombre, si.id_tipo_riego as tipo_riego, 
                    (CASE WHEN si.id_tipo_riego=1 THEN 'VÁLVULAS' ELSE 'CUARTELES' END) as tipo_umd,
                    (SELECT COUNT(*) FROM liberacion_umd
                     WHERE id_liberacion = li.id_liberacion AND id_campaña = ca.id_campaña) as cantidad
                    FROM 
                    liberacion li
                    INNER JOIN liberacion_umd lu  ON li.id_liberacion = lu.id_liberacion
                    INNER JOIN campaña ca ON ca.id_campaña = li.id_campaña
                    INNER JOIN siembra si ON si.id_siembra = ca.id_siembra
                    INNER JOIN campo cp ON cp.id_campo = si.id_campo
                    WHERE li.estado  = 'A' AND lu.id_liberador_asignado = :0";
            $campos = $this->consultarFilas($sql,$arParam);

            $sql = "SELECT 
                    COUNT(distinct cp.nombre_campo)
                    FROM 
                    liberacion li
                    INNER JOIN liberacion_umd lu  ON li.id_liberacion = lu.id_liberacion
                    INNER JOIN campaña ca ON ca.id_campaña = li.id_campaña
                    INNER JOIN siembra si ON si.id_siembra = ca.id_siembra
                    INNER JOIN campo cp ON cp.id_campo = si.id_campo
                    WHERE li.estado  = 'A' AND lu.id_liberador_asignado = :0";
            $numero_campos = $this->consultarValor($sql,$arParam);

            return array("rpt"=>true,"data"=>array("campos"=>$campos, "numero_campos"=>$numero_campos));
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerCamposProgramadosVer($idLib){
        try {
            $arParam =  array($idLib);
            $sql = "SELECT 
                    distinct lu.id_liberacion, cp.nombre_campo as nombre, si.id_tipo_riego as tipo_riego, 
                    (CASE WHEN si.id_tipo_riego=1 THEN 'VÁLVULAS' ELSE 'CUARTELES' END) as tipo_umd,
                    (SELECT COUNT(*) FROM liberacion_umd
                     WHERE id_liberacion = li.id_liberacion AND id_campaña = ca.id_campaña) as cantidad,
                    (SELECT COUNT(*) FROM liberacion_umd
                     WHERE id_liberacion = li.id_liberacion AND id_campaña = ca.id_campaña AND estado_liberacion) as cantidad_completadas
                    FROM 
                    liberacion li
                    INNER JOIN liberacion_umd lu  ON li.id_liberacion = lu.id_liberacion
                    INNER JOIN campaña ca ON ca.id_campaña = li.id_campaña
                    INNER JOIN siembra si ON si.id_siembra = ca.id_siembra
                    INNER JOIN campo cp ON cp.id_campo = si.id_campo
                    WHERE li.estado  = 'A' AND lu.id_liberador_asignado = :0";
            $campos = $this->consultarFilas($sql,$arParam);

            $sql = "SELECT 
                    COUNT(distinct cp.nombre_campo)
                    FROM 
                    liberacion li
                    INNER JOIN liberacion_umd lu  ON li.id_liberacion = lu.id_liberacion
                    INNER JOIN campaña ca ON ca.id_campaña = li.id_campaña
                    INNER JOIN siembra si ON si.id_siembra = ca.id_siembra
                    INNER JOIN campo cp ON cp.id_campo = si.id_campo
                    WHERE li.estado  = 'A' AND lu.id_liberador_asignado = :0";
            $numero_campos = $this->consultarValor($sql,$arParam);

            return array("rpt"=>true,"data"=>array("campos"=>$campos, "numero_campos"=>$numero_campos));
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerValvulas($idLiberacion, $idLib)
    {
        try {

            $sql = "SELECT 
                distinct cp.nombre_campo as nombre,                   
                (SELECT COUNT(*) FROM liberacion_umd
                WHERE id_liberacion = li.id_liberacion AND id_campaña = ca.id_campaña) as total_valvulas,
                (SELECT COUNT(*) FROM liberacion_umd
                WHERE id_liberacion = li.id_liberacion AND id_campaña = ca.id_campaña AND estado_liberacion) as liberadas_valvulas,
                li.cantidad_moscas as moscas_asignadas
                FROM 
                liberacion li
                INNER JOIN liberacion_umd lu  ON li.id_liberacion = lu.id_liberacion
                INNER JOIN campaña ca ON ca.id_campaña = li.id_campaña
                INNER JOIN siembra si ON si.id_siembra = ca.id_siembra
                INNER JOIN campo cp ON cp.id_campo = si.id_campo
                WHERE li.estado  = 'A' AND li.id_liberacion = :0;";
            $campo = $this->consultarFila($sql, array($idLiberacion));

            $sql = "SELECT 
                    lu.id_umd,
                    lu.id_liberacion,
                    cu.numero_nivel_tres as correlativo,
                    COALESCE(cantidad_moscas, 0) as moscas_asignadas  
                     FROM liberacion_umd lu
                     INNER JOIN campaña_umd cu ON lu.id_umd = cu.id_umd AND cu.id_campaña = lu.id_campaña
                     WHERE lu.id_liberacion = :0 AND lu.id_liberador_asignado = :1 AND NOT estado_liberacion 
                     ORDER BY cu,numero_nivel_tres";
            $campo["valvulas"] = $this->consultarFilas($sql, array($idLiberacion, $idLib));

            return array("rpt"=>true,"data"=>$campo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerValvulasVer($idLiberacion, $idLib)
    {
        try {

            $sql = "SELECT 
                distinct cp.nombre_campo as nombre,                   
                (SELECT COUNT(*) FROM liberacion_umd
                WHERE id_liberacion = li.id_liberacion AND id_campaña = ca.id_campaña) as total_valvulas,
                (SELECT COUNT(*) FROM liberacion_umd
                WHERE id_liberacion = li.id_liberacion AND id_campaña = ca.id_campaña AND estado_liberacion) as liberadas_valvulas,
                li.cantidad_moscas as moscas_asignadas
                FROM 
                liberacion li
                INNER JOIN liberacion_umd lu  ON li.id_liberacion = lu.id_liberacion
                INNER JOIN campaña ca ON ca.id_campaña = li.id_campaña
                INNER JOIN siembra si ON si.id_siembra = ca.id_siembra
                INNER JOIN campo cp ON cp.id_campo = si.id_campo
                WHERE li.estado  = 'A' AND li.id_liberacion = :0;";
            $campo = $this->consultarFila($sql, array($idLiberacion));

            $sql = "SELECT 
                    lu.id_umd,
                    lu.id_liberacion,
                    cu.numero_nivel_tres as correlativo,
                    COALESCE(cantidad_moscas, 0) as moscas_asignadas  
                     FROM liberacion_umd lu
                     INNER JOIN campaña_umd cu ON lu.id_umd = cu.id_umd AND cu.id_campaña = lu.id_campaña
                     WHERE lu.id_liberacion = :0 AND lu.id_liberador_asignado = :1
                     ORDER BY cu,numero_nivel_tres";
            $campo["valvulas"] = $this->consultarFilas($sql, array($idLiberacion, $idLib));

            return array("rpt"=>true,"data"=>$campo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerJironesLiberador($idLiberacion, $idLib)
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

    public function obtenerCuarteles($idLiberacion, $idLib)
    {
        try {

            $sql = "SELECT 
                distinct cp.nombre_campo as nombre,                   
                (SELECT COUNT(*) FROM liberacion_umd
                WHERE id_liberacion = li.id_liberacion AND id_campaña = ca.id_campaña) as total_valvulas,
                (SELECT COUNT(*) FROM liberacion_umd
                WHERE id_liberacion = li.id_liberacion AND id_campaña = ca.id_campaña AND estado_liberacion) as liberadas_valvulas,
                li.cantidad_moscas as moscas_asignadas
                FROM 
                liberacion li
                INNER JOIN liberacion_umd lu  ON li.id_liberacion = lu.id_liberacion
                INNER JOIN campaña ca ON ca.id_campaña = li.id_campaña
                INNER JOIN siembra si ON si.id_siembra = ca.id_siembra
                INNER JOIN campo cp ON cp.id_campo = si.id_campo
                WHERE li.estado  = 'A' AND li.id_liberacion = :0;";
            $campo = $this->consultarFila($sql, array($idLiberacion));

            $sql = "SELECT 
                    lu.id_umd,
                    lu.id_liberacion,
                    cu.numero_nivel_tres as correlativo,
                    COALESCE(cantidad_moscas, 0) as moscas_asignadas  
                     FROM liberacion_umd lu
                     INNER JOIN campaña_umd cu ON lu.id_umd = cu.id_umd AND cu.id_campaña = lu.id_campaña
                     WHERE lu.id_liberacion = :0 AND  lu.id_liberador_asignado = :1 AND NOT estado_liberacion 
                     ORDER BY cu,numero_nivel_tres";
            $campo["valvulas"] = $this->consultarFilas($sql, array($idLiberacion, $idLib));

            return array("rpt"=>true,"data"=>$campo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerCuartelesVer($idLiberacion, $idLib)
    {
        try {

            $sql = "SELECT 
                distinct cp.nombre_campo as nombre,                   
                (SELECT COUNT(*) FROM liberacion_umd
                WHERE id_liberacion = li.id_liberacion AND id_campaña = ca.id_campaña) as total_valvulas,
                (SELECT COUNT(*) FROM liberacion_umd
                WHERE id_liberacion = li.id_liberacion AND id_campaña = ca.id_campaña AND estado_liberacion) as liberadas_valvulas,
                li.cantidad_moscas as moscas_asignadas
                FROM 
                liberacion li
                INNER JOIN liberacion_umd lu  ON li.id_liberacion = lu.id_liberacion
                INNER JOIN campaña ca ON ca.id_campaña = li.id_campaña
                INNER JOIN siembra si ON si.id_siembra = ca.id_siembra
                INNER JOIN campo cp ON cp.id_campo = si.id_campo
                WHERE li.estado  = 'A' AND li.id_liberacion = :0;";
            $campo = $this->consultarFila($sql, array($idLiberacion));

            $sql = "SELECT 
                    lu.id_umd,
                    lu.id_liberacion,
                    cu.numero_nivel_tres as correlativo,
                    COALESCE(cantidad_moscas, 0) as moscas_asignadas  
                     FROM liberacion_umd lu
                     INNER JOIN campaña_umd cu ON lu.id_umd = cu.id_umd AND cu.id_campaña = lu.id_campaña
                     WHERE lu.id_liberacion = :0 AND lu.id_liberador_asignado = :1
                     ORDER BY cu,numero_nivel_tres";
            $campo["valvulas"] = $this->consultarFilas($sql, array($idLiberacion, $idLib));

            return array("rpt"=>true,"data"=>$campo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function listarLiberacionesCampo(){
        try {

            $sql = "SELECT * FROM  fn_listar_liberaciones_x_campaña(:0);";
            $liberaciones = $this->consultarFilas($sql, [$this->getIdCampaña()]);

            return array("rpt"=>true,"data"=>$liberaciones);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function listarLiberacionPorId(){
        try {

            $sql = " SELECT 
                   li.id_liberacion, cp.nombre_campo, 
                   estado_liberacion,                   
                   fn_tipo_lib_eval(li.tipo_liberacion) as tipo_liberacion,
                   fn_fecha(fecha_liberacion) as fecha_liberacion, 
                   fn_estado_lib_eval(li.estado) as estado,
                   fn_estado_color_lib_eval(li.estado) as estado_color,
                   (SELECT valor FROM variable_general WHERE nombre = 'cantidad_moscas_hectarea_liberacion')::int * 
                   (SELECT SUM(hectarea_disponible) FROM campaña_umd WHERE id_campaña = li.id_campaña)::int as cantidad_moscas_recomendada,                   
                   (SELECT SUM(hectarea_disponible) FROM campaña_umd WHERE id_campaña = li.id_campaña) as numero_hectareas,
                   cantidad_moscas,
                   si.id_tipo_riego,
                   (select COUNT(*) +1 from liberacion li_
                    WHERE li_.id_campaña = ca.id_campaña AND li_.fecha_liberacion > li.fecha_liberacion AND estado <> 'P') as numero_liberacion
                   FROM liberacion li
                   INNER JOIN campaña ca ON ca.id_campaña = li.id_campaña
                   INNER JOIN siembra si ON si.id_siembra = ca.id_siembra
                   INNER JOIN campo cp ON cp.id_campo = si.id_campo
                   WHERE li.id_liberacion =:0";

            $cabecera = $this->consultarFila($sql, [$this->getIdLiberacion()]);

            $sql = "SELECT fn_obtener_liberacion_x_id(:0);";
            $unidades_distribucion = $this->consultarValor($sql, [$this->getIdLiberacion()]);

            return array("rpt"=>true,"data"=>["cabecera"=>$cabecera, "unidades_distribucion"=>$unidades_distribucion]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

     public function obtenerLiberadores(){
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

    public function guardarMoscas($cantidad_moscas){
        try {

            $sql = "SELECT fn_registrar_asignacion_liberacion(:0, :1, NULL)";            
            $msj = $this->consultarValor($sql, [$this->getIdLiberacion(),$cantidad_moscas]);

            return array("rpt"=>true,"msj"=>$msj);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function registrarPrimeraLiberacion($fechaInicio){
        try {

            if (!isset($_SESSION)){
                return array("rpt"=>false,"msj"=>"Necesito un usuario autentificado, inicie sesión.");
            }
            $sql = "SELECT fn_registrar_primera_liberacion_x_campaña(:0, :1, :2)";            
            $msj = $this->consultarValor($sql, [$this->getIdCampaña(), $fechaInicio, $_SESSION["obj_usuario"]["id_usuario"]]);

            return array("rpt"=>true,"msj"=>$msj);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function registrarAsignacionLiberacionPorId($JSONAsignaciones){
        try {

            $cantidadMoscas = NULL;
            $sql = "SELECT fn_registrar_asignacion_liberacion(:0,:1,:2)";
            $rpta = $this->consultarValor($sql, [$this->getIdLiberacion(), $cantidadMoscas, $JSONAsignaciones]);

            return array("rpt"=>true,"data"=>json_decode($rpta));
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

     public function registrarAsignacionLiberacion($idLiberador, $JSONUmd)
    {
        $this->beginTransaction();
        try {
            $sql = "SELECT * FROM fn_registrar_asignacion_liberacion_x_id(:0,:1,:2)";
            $data = $this->consultarValor($sql, [$this->getIdLiberacion(), $idLiberador, $JSONUmd]); 

            $this->commit();
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function listarLiberacionesMapa(){
        try {

            $sql = "SELECT id_liberacion, to_char(fecha_liberacion,'DD-MM-YYYY') as fecha_liberacion, estado, 
                estado_liberacion, numero_liberacion
                FROM liberacion 
                WHERE id_campaña = :0   AND estado <> 'P'
                ORDER BY numero_liberacion DESC";

            $liberaciones = $this->consultarFilas($sql, [$this->getIdCampaña()]);

            return array("rpt"=>true,"data"=>$liberaciones);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerLiberacionesCampañas ($fecha_liberacion){
        try {

            $sql = "SELECT * FROM fn_listar_liberaciones_x_dia(:0)";

            $liberaciones = $this->consultarFilas($sql, [$fecha_liberacion]);

            return array("rpt"=>true,"data"=>$liberaciones);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function confirmarLiberacion(){
        try {

            //la liberacion debe estar OBLIGATORIAMENTE EN PROGRAMACION Y tener una fecha ANTERIOR al del día actual.
            $sql = "SELECT fecha_liberacion < current_date as fecha_correcta,
                           estado <> 'P' as estado_correcto,
                           fecha_liberacion
                             FROM liberacion WHERE id_liberacion = :0";
            $objValidacion = $this->consultarFila($sql, [$this->getIdEvaluacion()]);

            if ($objValidacion["fecha_correcta"]){
                return array("rpt"=>false,"msj"=>"No se puede confirmar una liberación que tiene fecha más antigua al día de HOY.  Consulte con el administrador.");                
            }

            if ($objValidacion["estado_correcto"]){
                return array("rpt"=>false,"msj"=>"No se puede confirmar una liberación que tenga una estado DIFERENTE a PROGRAMADA. Consulte con el administrador.");                                
            }

            $this->update("liberacion",["estado"=>"C"],["id_liberacion"=>$this->getIdEvaluacion()]);    

            return array("rpt"=>true,"msj"=>"Liberación editada correctamente.");
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function anularLiberacion(){
        try {

            //la liberacion debe TENER un fecha_inicio_Evalaucion_temporal y Estado Confirmado.
            $sql = "SELECT estado <> 'C' as estado_correcto
                        FROM liberacion WHERE id_liberacion = :0";
            $objValidacion = $this->consultarFila($sql, [$this->getIdLiberacion()]);

            if ($objValidacion["estado_correcto"]){
                return array("rpt"=>false,"msj"=>"No se puede anular una confirmación si el estado de liberación es DIFERENTE a CONFIRMADA. Consulte con el administrador.");                                
            }

            $this->update("liberacion",["estado"=>"P"],["id_liberacion"=>$this->getIdLiberacion()]);    

            return array("rpt"=>true,"msj"=>"Confirmación de evaluación anulada correctamente");
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function obtenerLiberacionesPosteriores($fecha_liberacion){
        try {

            //la evaluacion debe TENER un fecha_inicio_Evalaucion_temporal y Estado Confirmado.

            if ($fecha_liberacion == null || $fecha_liberacion == ""){
                return ["rpt"=>true, []];
            }

            $sql = "SELECT * FROM fn_listar_liberaciones_posterior_x_id(:0,:1)";
            $liberaciones = $this->consultarFilas($sql, [$this->getIdLiberacion(), $fecha_liberacion]);

            return array("rpt"=>true,"data"=>$liberaciones);
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function editarFechaLiberacion($nueva_fecha_liberacion, $arrastrar){
        try {

            //la liberacion debe TENER un fecha_inicio_Evalaucion_temporal y Estado Confirmado.

            if ($nueva_fecha_liberacion == null || $nueva_fecha_liberacion == ""){
                return ["rpt"=>false, "msj"=>"No se ha ingresado una fecha válida."];
            }

            $sql = "SELECT fn_editar_liberacion_fecha(:0,:1,:2)";
            $rpta = $this->consultarValor($sql, [$this->getIdLiberacion(), $nueva_fecha_liberacion, $arrastrar]);

            return array("rpt"=>true,"data"=>json_decode($rpta));
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function reporteResultadoLiberaciones($arrCampos,$f0,$f1){
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
                lu.id_umd,
                l.numero_liberacion,
                to_char(l.fecha_liberacion,'DD-MM-YYYY') as fecha, 
                ca.nombre_campo,
                numero_nivel_uno as modulo_jiron,
                COALESCE(numero_nivel_dos) as turno,
                COALESCE(numero_nivel_tres,numero_nivel_dos) as valvula_cuartel,
                v.descripcion as variedad,
                cu.hectarea_disponible as area,
                lu.cantidad_moscas,
                s.id_tipo_riego
                        FROM liberacion_umd lu 
                        INNER JOIN liberacion l ON lu.id_liberacion = l.id_liberacion                                               
                        INNER JOIN campaña c ON c.id_campaña = l.id_campaña
                        INNER JOIN campaña_umd cu ON cu.id_umd = lu.id_umd
                        INNER JOIN siembra s ON s.id_siembra = c.id_siembra
                        INNER JOIN variedad_caña v ON v.id_variedad_caña = s.id_variedad_caña
                        INNER JOIN campo ca ON ca.id_campo = s.id_campo
                WHERE  l.fecha_liberacion >= :0 AND l.fecha_liberacion <= :1 AND  l.estado <> 'P' ".$str."
                ORDER BY fecha_liberacion, numero_liberacion,nombre_campo, modulo_jiron, turno, valvula_cuartel  " ;
             
            return $this->consultarFilas($sql, [$f0, $f1]);

        } catch (Exception $exc) {            
            throw $exc;
        }
    }
    
}

    