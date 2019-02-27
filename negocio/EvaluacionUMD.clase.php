<?php

require_once '../datos/Conexion.clase.php';
require_once 'UtilidadesExtra.rasgo.php';

class EvaluacionUMD extends Conexion{

    private $idEvaluacion;
    private $idUmd;
    private $idCampaña;

    use UtilidadesExtra;

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

    public function getIdUmd()
    {
        return $this->idUmd;
    }
    
    
    public function setIdUmd($idUmd)
    {
        $this->idUmd = $idUmd;
        return $this;
    }

    public function cmnObtenerPuntosMuestreo($idUmd, $idEvaluacion, $obtenerDatosMuestreo = true, $ver =  false)
    {
            $boolRiego =  $this->obtenerTipoRiegoUmd($idUmd) == 1;

            $sqlCabecera = "SELECT cp.nombre_campo, ";

            if ($boolRiego){
                /* Valvulas */
                $sqlCabecera .= " CONCAT('VÁLVULA: ',ca_u.numero_nivel_tres) as numero_valvula, ";                          
            } else {
                $sqlCabecera .= "CONCAT('JIRÓN: ', ca_u.numero_nivel_uno) as numero_jiron,
                    CONCAT('CUARTEL: ', ca_u.numero_nivel_dos) as numero_cuartel, ";                
            }

            $sqlCabecera .= "
                    si.id_tipo_riego as tipo_riego ,
                    (SELECT COUNT(distinct(numero_punto)) 
                    FROM punto p
                    WHERE p.id_campaña = ca.id_campaña AND 
                    id_umd = ca_u.id_umd AND id_evaluacion = ev_u.id_evaluacion
                    ) as puntos_muestreados
                    FROM
                    evaluacion_umd ev_u 
                    LEFT JOIN campaña_umd ca_u ON ev_u.id_umd = ca_u.id_umd AND ev_u.id_campaña = ev_u.id_campaña
                    LEFT JOIN campaña ca ON ca.id_campaña = ca_u.id_campaña AND ca.estado = 'A' 
                    LEFT JOIN siembra si ON si.id_siembra = ca.id_siembra AND si.estado = 'A'
                    LEFT JOIN campo cp ON cp.id_campo = si.id_campo AND cp.estado ='A'
                    WHERE 
                    ev_u.id_umd = :0 AND ev_u.id_evaluacion = :1";

           $cabecera = $this->consultarFila($sqlCabecera, [$idUmd, $idEvaluacion]);

            $datosMuestreo = null;
            if ($obtenerDatosMuestreo){
                $sql = "SELECT * FROM dato_muestreo WHERE estado_mrcb = 1 ORDER BY 1";
                $datosMuestreo = $this->consultarFilas($sql);
            }

            /* TBL */
            $puntos = $this->consultarValor("SELECT fn_get_variable('numero_puntos_evaluacion')");
            $datosPuntos = array($puntos);

            function getSQL($boolRiego)
            {
                    /* numero_punto as punto, ca_u.numero_nivel_". 
                            ($boolRiego ? "tres as valvula" : "dos as cuartel")
                            .", ca_u.id_umd, SE HA BORRADO EN DE LA TABLA DE MUESTRA PORQUE NO ES DATA NECESARIA, SINO MAS BIEN REPETITIVA*/
                $sql = "SELECT 
                            valor_punto as valor,
                            id_dato_muestreo
                            FROM punto p
                            JOIN evaluacion_umd ev_u ON ev_u.id_campaña = p.id_campaña 
                            AND ev_u.id_umd = p.id_umd 
                            AND p.id_evaluacion = ev_u.id_evaluacion
                            WHERE numero_punto = :0                        
                                AND ev_u.id_umd = :1 AND p.id_evaluacion = :2
                                ORDER BY id_dato_muestreo";
                return $sql;
            }

            for ($i=0; $i < $puntos ; $i++) { 
                $sql  = getSQL($boolRiego);
                $_k = $i+1;
                $params_ =  [$_k, $idUmd, $idEvaluacion];
                if ($ver){
                     $datosPuntos[$i] = array();                    
                     $datosPuntos[$i]["pm"]  = $this->consultarFilas($sql, $params_);   
                     $datosPuntos[$i]["x"] = array("x" => count($datosPuntos[$i]["pm"]) > 0,
                                                    "val_ud"=>$idUmd,
                                                    "punto"=>$_k);  
                } else {
                    $datosPuntos[$i]  = $this->consultarFilas($sql, $params_);
                }
            }
                        
            return array("cabecera"=>$cabecera, "datos_puntos"=>$datosPuntos, "datos_muestreo"=>$datosMuestreo);
    }

    public function obtenerPuntosMuestreo($idUmd, $idEvaluacion, $obtenerDatosMuestreo = true)
    {
        try {
            $data = $this->cmnObtenerPuntosMuestreo($idUmd,$idEvaluacion,$obtenerDatosMuestreo);
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function obtenerPuntosMuestreoVer($idUmd,$idEvaluacion,$obtenerDatosMuestreo = true)
    {
        try {
            $data = $this->cmnObtenerPuntosMuestreo($idUmd,$idEvaluacion,$obtenerDatosMuestreo, true);
            return array("rpt"=>true,"data"=>$data);

        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }   

    public function listarEvaluacionesUMDMapa(){
        try { 

                $sql = "SELECT indice_poblacion_calculada, 
                            id_tipo_riesgo, nivel_infestacion, intensidad_daño, 
                            estado_evaluacion,
                            s.id_tipo_riego as tr,
                            c.lat,c.lng,
                            ca.id_campaña FROM campo c
                        LEFT JOIN siembra s ON s.id_campo = c.id_campo AND s.estado = 'A'
                        LEFT JOIN campaña ca ON ca.id_siembra = s.id_siembra AND ca.estado = 'A'
                        LEFT JOIN evaluacion ev ON ca.id_campaña = ev.id_campaña
                            WHERE ev.id_evaluacion = :0";

                $cabecera = $this->consultarFila($sql, [$this->getIdEvaluacion()]);

                /*
                $sql = "SELECT cu.id_umd, cu.numero_nivel_uno, cu.numero_nivel_dos, cu.numero_nivel_tres,
                            indice_poblacion_calculada, 
                            id_tipo_riesgo, 
                            nivel_infestacion,
                            intensidad_daño,
                            CONCAT(p.nombres,' ',p.apellidos) as evaluador_cierre, estado_evaluacion,
                            (SELECT COUNT(numero_punto) FROM  evaluacion_umd_punto eup
                                WHERE eup.id_umd = eu.id_umd AND eup.id_evaluacion = eu.id_evaluacion) as puntos_realizados
                            FROM campaña_umd cu
                            INNER JOIN evaluacion_umd eu ON eu.id_umd = cu.id_umd AND cu.id_campaña = eu.id_campaña
                            LEFT JOIN usuario u ON u.id_usuario = eu.id_evaluador_cierre
                            LEFT JOIN personal p ON p.id_personal = u.id_personal
                            WHERE eu.id_evaluacion = :0
                            ORDER BY numero_nivel_tres::integer ";
                            */

                $sql = "SELECT cu.id_umd, cu.numero_nivel_uno, cu.numero_nivel_dos, cu.numero_nivel_tres,
                            '0.00' as indice_poblacion_calculada, 
                            '1' as id_tipo_riesgo, 
                            '1.00' as nivel_infestacion,
                            '1.00' as intensidad_daño,
                            CONCAT('JUAN',' PEREZ') as evaluador_cierre, true as estado_evaluacion,
                            '10' as puntos_realizados
                            FROM campaña_umd cu
                            -- INNER JOIN evaluacion_umd eu ON eu.id_umd = cu.id_umd AND cu.id_campaña = eu.id_campaña
                            WHERE  cu.id_campaña = :0
                            ORDER BY numero_nivel_tres::integer ";

                //$umd = $this->consultarFilas($sql, [$this->getIdEvaluacion()]);
                $umd = $this->consultarFilas($sql, [$cabecera["id_campaña"]]);
                var_dump($umd);

                foreach ($umd as $key => $value) {
                    
                    $sql = "SELECT latitud as lat, longitud as lng FROM umd_coordenada WHERE id_umd = :0";
                    $umdCoord = $this->consultarFilas($sql,[$value["id_umd"]]);
                    $umd[$key]["_"] = json_encode($umdCoord,JSON_NUMERIC_CHECK);
                }

            return array("rpt"=>true,"data"=>["id_evaluacion"=>$this->getIdEvaluacion(), "cabecera"=>$cabecera, "umd"=>$umd]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function listarUMDPuntosRealizadosMapa(){
        try { 

                $sql = "SELECT numero_punto, latitud as lat, longitud as lng, 
                            to_char(fecha_hora_registro, 'DD-MM-YYYY') as fecha,
                            to_char(fecha_hora_registro, 'HH12:MI:SS AM') as hora,
                            CONCAT(p.nombres,' ',p.apellidos) as evaluador_cierre
                            FROM evaluacion_umd_punto eup
                            LEFT JOIN usuario u ON u.id_usuario = eup.id_evaluador_registro
                            LEFT JOIN personal p ON p.id_personal = u.id_personal                    
                            WHERE eup.id_evaluacion = :0 AND id_umd = :1";

                $data = $this->consultarFilas($sql, [$this->getIdEvaluacion(), $this->getIdUmd()]);
             
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

}