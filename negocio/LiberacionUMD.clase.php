<?php

require_once '../datos/Conexion.clase.php';
require_once 'UtilidadesExtra.rasgo.php';

class LiberacionUMD extends Conexion {    
    private $idLiberacion;
    private $idUmd;
    private $idCampaña;

    use UtilidadesExtra;

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

    public function getIdUmd()
    {
        return $this->idUmd;
    }
    
    
    public function setIdUmd($idUmd)
    {
        $this->idUmd = $idUmd;
        return $this;
    }
    
    public function obtenerUMD($idUmd, $idLiberacion)
    {        
        try {

            $arParam =  [$idLiberacion, $idUmd];
            $numeroPuntos = $this->consultarValor("SELECT fn_get_variable('numero_puntos_liberacion')");

            $sql = "SELECT 
                distinct cp.nombre_campo,   
                CONCAT('VÁLVULA: ',cu.numero_nivel_tres) as numero_valvula,
                si.id_tipo_riego as tipo_riego,
                (SELECT COUNT(*) FROM liberacion_umd_punto 
                WHERE id_liberacion = lu.id_liberacion AND lu.id_umd = id_umd) as puntos_liberados,                
                lu.cantidad_moscas,
                ".$numeroPuntos." as numero_puntos
                FROM 
                liberacion li
                INNER JOIN liberacion_umd lu  ON li.id_liberacion = lu.id_liberacion AND li.id_campaña  = lu.id_campaña
                INNER JOIN campaña_umd cu ON cu.id_campaña = lu.id_campaña AND cu.id_umd = lu.id_umd
                INNER JOIN campaña ca ON ca.id_campaña = li.id_campaña
                INNER JOIN siembra si ON si.id_siembra = ca.id_siembra
                INNER JOIN campo cp ON cp.id_campo = si.id_campo
                WHERE li.estado  = 'A' AND li.id_liberacion = :0 AND lu.id_umd = :1";

            $cabecera = $this->consultarFila($sql, $arParam);

            $sql = "SELECT
                    numero_punto,
                    to_char(fecha_hora_registro, 'HH12:MI:SS AM') as fecha_hora_registro
                    FROM liberacion_umd_punto
                    WHERE id_liberacion = :0 AND id_umd = :1
                    ORDER BY 1";

            $datosPuntos = $this->consultarFilas($sql, $arParam);
                        
            return array("rpt"=>true,"data"=>array("cabecera"=>$cabecera, "datos_puntos"=>$datosPuntos));
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function guardarLiberacion($idUmd, $idLiberacion, $idLiberador, $lat, $lon)
    {
        try {
            $this->beginTransaction();
          
            $msj = "Punto liberado CORRECTAMENTE";

            $tipoRiego = $this->obtenerTipoRiegoUmd($idUmd); //tipo_riego == 1 //
            $strUmd =   $tipoRiego == 1 ? 
                        "Válvula COMPLETAMENTE liberada: " :
                        "Cuartel COMPLETAMENTE liberado: ";

            $sql = "SELECT cu.id_campaña, 
                    COALESCE(cu.numero_nivel_tres, cu.numero_nivel_dos) as numero_umd
                    FROM campaña_umd cu
                    INNER JOIN liberacion_umd lu ON lu.id_campaña = cu.id_campaña AND cu.id_umd = lu.id_umd
                    WHERE lu.id_liberacion = :0 AND cu.id_umd = :1";
            $vars = $this->consultarFila($sql, [$idLiberacion, $idUmd]);
            $idCampaña = $vars["id_campaña"]; $numeroUmd = $vars["numero_umd"];

            $numeroPuntos = $this->consultarValor("SELECT fn_get_variable('numero_puntos_liberacion')");
            $sql = "SELECT COUNT(*) FROM liberacion_umd_punto WHERE id_liberacion = :0 AND id_umd = :1";
            $liberaciones_hechas = $this->consultarValor($sql, array($idLiberacion, $idUmd));
            $liberacionesCompletas = $liberaciones_hechas >= $numeroPuntos;

            if (!$liberacionesCompletas){

                 $campos_valores = array(
                            "numero_punto" => ++$liberaciones_hechas,
                            "id_umd"=>$idUmd,
                            "id_liberacion"=>$idLiberacion,
                            "id_campaña"=>$idCampaña,
                            "latitud"=>$lat,
                            "longitud"=>$lon
                            );

                 $this->insert("liberacion_umd_punto",$campos_valores); 

                 $liberaciones_hechas++;
                 $liberacionesCompletas = $liberaciones_hechas >= $numeroPuntos;

                 if ($liberacionesCompletas){
                        $campos_valores = array("estado_liberacion"=>1);
                        $campos_where = array("id_umd"=>$idUmd,"id_liberacion"=>$idLiberacion);
                        $this->update("liberacion_umd", $campos_valores,$campos_where);

                        $msj = $strUmd.$numeroUmd;
                 }
            }

            $this->commit();
            return array("rpt"=>true, "msj"=>$msj,"pnuevo"=>$liberacionesCompletas);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    private function neoGuardarLiberacionPunto($objLiberacion)
    {
        try {

            /*Registrar*/
            /*INSERTAR EL Punto EN liberacion_umd_punto*/
            $this->setIdLiberacion($objLiberacion->id);

            $arregloMuestras = $objLiberacion->muestras;
            $topePunto = 5;

            foreach ($arregloMuestras as $indice => $objPunto) {
                /*Insertamos una por una la muestra, y verificamos si su "numero_punto" es 5 para pasar a registrar
                 la UMD*/
                $campos_valores_ = array(
                            "id_umd"=>$objPunto->id_umd,
                            "id_liberacion"=>$objPunto->id_liberacion,
                            "numero_punto"=>$objPunto->numero_punto,
                            "latitud"=>$objPunto->latitud,
                            "longitud"=>$objPunto->longitud,
                            "fecha_hora_registro"=>$objPunto->fecha_hora_registro                            
                            );

                $this->insert("liberacion_umd_punto",$campos_valores_); 

                if ($objPunto->numero_punto == $topePunto){
                        /*Actualizar UMD, estado_liberacion = true*/
                        $campos_valores = array(
                                "estado_liberacion"=> true,
                                "id_liberador_asignado"=> $objPunto->id_liberador_asignado                      
                                );

                        $campos_valores_where = [
                                "id_liberacion" => $objPunto->id_liberacion,
                                "id_umd"=> $objPunto->id_umd
                                ];

                        $this->update("liberacion_umd",$campos_valores,$campos_valores_where); 

                }
            }

            /*Despues de insertar todo, verificar si se completó el campo*/

            $sql = "SELECT 
                    (SELECT COUNT(cu.id_umd) FROM campaña_umd cu WHERE cu.estado_activo = 'A' AND cu.id_campaña = l.id_campaña) as totales,
                    (SELECT COUNT(lu.id_umd) FROM liberacion_umd lu WHERE lu.estado_liberacion AND lu.id_liberacion = l.id_liberacion) completadas
                     FROM liberacion l WHERE NOT l.estado_liberacion AND l.id_liberacion = :0 ";

            $liberacionFila = $this->consultarFila($sql, $this->getIdLiberacion());

            $estaCompleto = false;
            if ($liberacionFila["completadas"] > 0 && $liberacionFila["completadas"] == $liberacionFila["totales"]){
                $estaCompleto = true;
            }

            if ($estaCompleto){                        
                /*Operación Campo.*/
                $campos_valores = ["estado_liberacion"=>true,
                                    "fecha_fin_liberacion"=>date('Y-m-d')];

                $campos_valores_where = ["id_liberacion"=>$this->getIdLiberacion()];

                $this->update("liberacion",$campos_valores,$campos_valores_where);

            } 

            /*Devolvemos EXITO, de lado del móvil se hace UPDATE puntos, umd y liberaciones.*/
            return true;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function recibirDatosApp($idUsuario, $liberaciones)
    {
        try {

            $this->beginTransaction();
           
            foreach ($liberaciones as $key => $objLiberaciones) {
               $exito = $this->neoGuardarLiberacionPunto($objLiberaciones);
               if (!$exito){
                    $this->rollBack();
                    return ["rpt"=>false];
               }
            }
            $this->commit();
            return ["rpt"=>true];
        } catch (Exception $exc) {
            $this->rollBack();
            throw $exc;
        }
    }

    public function listarLiberacionesUMDMapa(){
        try { 

                $sql = "SELECT ev.indice_poblacion_calculada, 
                            ev.id_tipo_riesgo, ev.nivel_infestacion, ev.intensidad_daño, 
                            li.estado_liberacion,
                            li.cantidad_moscas,
                            c.lat,c.lng FROM campo c
                        LEFT JOIN siembra s ON s.id_campo = c.id_campo AND s.estado = 'A'
                        LEFT JOIN campaña ca ON ca.id_siembra = s.id_siembra AND ca.estado = 'A'
                        LEFT JOIN liberacion li ON ca.id_campaña = li.id_campaña
                        LEFT JOIN evaluacion ev ON li.id_evaluacion_precedente = ev.id_evaluacion
                            WHERE li.id_liberacion = :0";

                $cabecera = $this->consultarFila($sql, [$this->getIdLiberacion()]);
                
                $sql = "SELECT cu.id_umd, cu.numero_nivel_uno, cu.numero_nivel_dos, cu.numero_nivel_tres,
                            indice_poblacion_calculada, 
                            id_tipo_riesgo, 
                            nivel_infestacion,
                            intensidad_daño,
                            lu.cantidad_moscas,
                            CONCAT(p.nombres,' ',p.apellidos) as liberador_cierre, 
                            lu.estado_liberacion,
                            (SELECT COUNT(numero_punto) FROM liberacion_umd_punto lup
                                WHERE lup.id_umd = lu.id_umd AND lup.id_liberacion = lu.id_liberacion) as puntos_realizados
                            FROM campaña_umd cu                            
                            INNER JOIN liberacion_umd lu ON lu.id_umd = cu.id_umd AND lu.id_campaña = cu.id_campaña
                            INNER JOIN liberacion l ON l.id_liberacion = lu.id_liberacion
                            INNER JOIN evaluacion_umd eu ON eu.id_umd = lu.id_umd AND lu.id_campaña = eu.id_campaña AND l.id_evaluacion_precedente = eu.id_evaluacion
                            LEFT JOIN usuario u ON u.id_usuario = lu.id_liberador_asignado
                            LEFT JOIN personal p ON p.id_personal = u.id_personal
                            WHERE l.id_liberacion = :0
                            ORDER BY numero_nivel_tres::integer ";

                $umd = $this->consultarFilas($sql, [$this->getIdLiberacion()]);

                foreach ($umd as $key => $value) {
                    
                    $sql = "SELECT latitud as lat, longitud as lng FROM umd_coordenada WHERE id_umd = :0";
                    $umdCoord = $this->consultarFilas($sql,[$value["id_umd"]]);
                    $umd[$key]["_"] = json_encode($umdCoord,JSON_NUMERIC_CHECK);
                }

            return array("rpt"=>true,"data"=>["id_liberacion"=>$this->getIdLiberacion(), "cabecera"=>$cabecera, "umd"=>$umd]);
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
                            CONCAT(p.nombres,' ',p.apellidos) as liberador_cierre
                            FROM liberacion_umd_punto lup
                            LEFT JOIN liberacion_umd lu ON lu.id_liberacion = lup.id_liberacion AND lu.id_umd = lup.id_umd
                            LEFT JOIN usuario u ON u.id_usuario = lu.id_liberador_asignado
                            LEFT JOIN personal p ON p.id_personal = u.id_personal                    
                            WHERE lup.id_liberacion = :0 AND lup.id_umd = :1";

                $data = $this->consultarFilas($sql, [$this->getIdLiberacion(), $this->getIdUmd()]);
             
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }
    
}

    