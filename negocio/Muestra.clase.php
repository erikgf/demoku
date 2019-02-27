<?php

require_once '../datos/Conexion.clase.php';
require_once 'UtilidadesExtra.rasgo.php';

class Muestra extends Conexion{

    private $idEvaluacion;
    private $idUmd;
    private $numeroPunto;
    private $numeroMuestra;
    private $idEvaluador;    


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

    public function getIdUmd()
    {
        return $this->idUmd;
    }
    
    
    public function setIdUmd($idUmd)
    {
        $this->idUmd = $idUmd;
        return $this;
    }

    public function getNumeroPunto()
    {
        return $this->numeroPunto;
    }
    
    
    public function setNumeroPunto($numeroPunto)
    {
        $this->numeroPunto = $numeroPunto;
        return $this;
    }

    public function getNumeroMuestra()
    {
        return $this->numeroMuestra;
    }
    
    
    public function setNumeroMuestra($numeroMuestra)
    {
        $this->numeroMuestra = $numeroMuestra;
        return $this;
    }

    public function getIdEvaluador()
    {
        return $this->idEvaluador;
    }
    
    
    public function setIdEvaluador($idEvaluador)
    {
        $this->idEvaluador = $idEvaluador;
        return $this;
    }

    /*@obsolete */
    public function obtenerRegistroMuestraCabecera($idEvaluacion, $idUmd, $punto, $obtenerDatosMuestreo = true)
    {
       try {
             $cabecera = $this->consultarFila($this->obtenerSQLCabeceraPuntoMuestra($idUmd), [$idEvaluacion,$idUmd,$punto]);
                        
            return array("rpt"=>true,"data"=>$cabecera);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }    

    /*@obsolete */
    public function guardarMuestra($JSONCabecera, $JSONformulario)
    {
        try {
            $this->beginTransaction();

            $cabecera = json_decode($JSONCabecera);
            $formulario = json_decode($JSONformulario);

            $this->idEvaluador = $cabecera->p_idEvaluador;
            
            $sql = "SELECT id_campaña FROM evaluacion WHERE id_evaluacion = :0";
            $idCampaña = $this->consultarValor($sql, [$cabecera->p_idEvaluacion]);     

            $booleanoTR = $this->obtenerTipoRiegoUmd($cabecera->p_idUmd) == 1;

            $datoMuestreoTmp = [];
            foreach ($formulario as $key => $value) {
                $campos_valores = array(
                        "id_dato_muestreo"=>$key,
                        "valor_muestra"=>$value,
                        "numero_muestra"=>($cabecera->p_numeroMuestra),
                        "id_umd"=>($cabecera->p_idUmd),
                        "id_campaña"=>$idCampaña,
                        "id_evaluacion"=>($cabecera->p_idEvaluacion),
                        "numero_punto"=>($cabecera->p_numeroPunto)
                        ); 
                    
                $this->insert("muestra",$campos_valores);    
                array_push($datoMuestreoTmp, $value);
            }

            /*INSERTAR LA MUESTRA en la nueva tabla evaluacion_umd_punto_muestra*/
            $campos_valores = array(
                            "numero_muestra"=>($cabecera->p_numeroMuestra),
                            "numero_punto"=>($cabecera->p_numeroPunto),
                            "id_umd"=>($cabecera->p_idUmd),
                            "id_evaluacion"=>$cabecera->p_idEvaluacion,
                            "dato_muestreo_1"=>$datoMuestreoTmp[0],
                            "dato_muestreo_2"=>$datoMuestreoTmp[1],
                            "dato_muestreo_3"=>$datoMuestreoTmp[2],
                            "dato_muestreo_4"=>$datoMuestreoTmp[3],
                            "dato_muestreo_5"=>$datoMuestreoTmp[4],
                            "dato_muestreo_6"=>$datoMuestreoTmp[5],
                            "dato_muestreo_7"=>$datoMuestreoTmp[6],
                            "dato_muestreo_8"=>$datoMuestreoTmp[7]
                            );

            $this->insert("evaluacion_umd_punto_muestra",$campos_valores); 


            $sql = "SELECT (COUNT(*)/(SELECT COUNT(*) FROM dato_muestreo WHERE estado_mrcb =1)) 
                    FROM muestra
                    WHERE id_umd = :0 AND numero_punto = :1 AND id_evaluacion = :2 AND id_campaña = :3";

            $muestra_hechas = $this->consultarValor($sql,
                        array($cabecera->p_idUmd, $cabecera->p_numeroPunto, $cabecera->p_idEvaluacion, $idCampaña));
            
            $registrePuntoNuevo = false;

            if ($muestra_hechas >= 5){
                /*Registrando un nuevo nuevo PUTO*/
                $registrePuntoNuevo = true;
                $sql= "SELECT id_dato_muestreo, SUM(valor_muestra) as sumatoria FROM muestra
                        WHERE id_umd = :0 AND numero_punto = :1 AND id_evaluacion = :2 AND id_campaña = :3
                        GROUP BY id_dato_muestreo ORDER BY 1";

                    $sumatoria_muestras = $this->consultarFilas($sql, 
                        array($cabecera->p_idUmd, $cabecera->p_numeroPunto, $cabecera->p_idEvaluacion, $idCampaña));

                    foreach ($sumatoria_muestras as $key => $value) {
                        $campos_valores = array(
                            "numero_punto"=>($cabecera->p_numeroPunto),
                            "id_umd"=>($cabecera->p_idUmd),
                            "id_dato_muestreo"=>$value["id_dato_muestreo"],
                            "id_evaluacion"=>$cabecera->p_idEvaluacion,
                            #"id_evaluador_registro"=>$this->idEvaluador,
                            "id_campaña"=>$idCampaña,
                            "valor_punto"=>$value["sumatoria"],
                            #"latitud"=>$cabecera->_latitud,
                            #"longitud"=>$cabecera->_longitud
                            );
                        $this->insert("punto",$campos_valores); 
                    }

                        /*Con el nuevo cambio se evita un poco la repetición de data*/
                        /*Aqui por motivos de data, EZ debería guardarse el valor resumen de las 5 muestras.*/

                        $campos_valores = array(
                            "numero_punto"=>($cabecera->p_numeroPunto),
                            "id_umd"=>($cabecera->p_idUmd),
                            "id_evaluacion"=>$cabecera->p_idEvaluacion,
                            "id_evaluador_registro"=>$this->idEvaluador,
                            "latitud"=>$cabecera->_latitud,
                            "longitud"=>$cabecera->_longitud,
                            "dato_muestreo_1"=>$sumatoria_muestras[0]["sumatoria"],
                            "dato_muestreo_2"=>$sumatoria_muestras[1]["sumatoria"],
                            "dato_muestreo_3"=>$sumatoria_muestras[2]["sumatoria"],
                            "dato_muestreo_4"=>$sumatoria_muestras[3]["sumatoria"],
                            "dato_muestreo_5"=>$sumatoria_muestras[4]["sumatoria"],
                            "dato_muestreo_6"=>$sumatoria_muestras[5]["sumatoria"],
                            "dato_muestreo_7"=>$sumatoria_muestras[6]["sumatoria"],
                            "dato_muestreo_8"=>$sumatoria_muestras[7]["sumatoria"]
                            );

                        $this->insert("evaluacion_umd_punto",$campos_valores); 

            }

            $registreUDNuevo = false;

            $sql = "  SELECT COUNT(distinct(numero_punto)) FROM punto 
               WHERE  id_umd = :0 AND id_evaluacion = :1 AND id_campaña = :2";
               $puntos_muestreados = $this->consultarValor($sql, array($cabecera->p_idUmd,$cabecera->p_idEvaluacion, $idCampaña)); 

            $msj = " Registro de muestra CORRECTO.";

            $ud  = ($booleanoTR ? " LA VALVULA " : " EL CUARTEL");

            if ($puntos_muestreados >= 5){
                /*Se terminó todo un UMD*/            
                $sql = "SELECT fn_get_variable('tallos_totales_evaluados'); ";
                $cantidadTallosDefault  = $this->consultarValor($sql);

                $sql = "SELECT id_dato_muestreo, 
                    SUM(valor_punto) as valor FROM punto p   
                    WHERE id_evaluacion = :0 AND id_campaña = :1
                        AND id_umd = :2 AND id_dato_muestreo IN (1,2,3,7)               
                        GROUP BY id_dato_muestreo
                    ORDER BY 1";

                $subtotales = $this->consultarFilas($sql, [$cabecera->p_idEvaluacion, $idCampaña, $cabecera->p_idUmd]);
                $this->obtenerGuardarUMDEvaluacion($cantidadTallosDefault,
                                                $cabecera->p_idUmd,
                                                $idCampaña,$cabecera->p_idEvaluacion, 
                                                $subtotales);
          
                $msj = "Se ha registrado ".$ud." COMPLETAMENTE.";
                $registreUDNuevo  = true;

            }

            if ($registrePuntoNuevo && $registreUDNuevo == false) {
                $msj = "Se ha registrado UN PUNTO DE ".$ud.".";
            }

            /*Verificar si se ha registrado todo el campo*/
            $sql = "SELECT
                    (SELECT COUNT(*) from evaluacion_umd
                    WHERE id_evaluacion = :0) as totales,
                    (SELECT COUNT(*) 
                    FROM evaluacion_umd
                    WHERE id_evaluacion = :0 AND estado_evaluacion) as evaluadas";
    
            $filaTemp = $this->consultarFila($sql, [$cabecera->p_idEvaluacion]);

            $estaCompleto = false;
            if ($filaTemp["evaluadas"] > 0 && $filaTemp["evaluadas"] == $filaTemp["totales"]){
                $estaCompleto = true;
            }
            
            if ($estaCompleto){                
                $msj = "Se ha concluído la evaluación total del CAMPO.";
                /*Operación Campo.*/
                $sql = "SELECT
                          AVG(indice_poblacion_calculada)::numeric(5,2) as indice_poblacion_calculada,
                          AVG(nivel_infestacion)::numeric(5,2) as nivel_infestacion,
                          AVG(intensidad_daño)::numeric(5,2) as intensidad_daño
                           FROM evaluacion_umd
                              WHERE id_evaluacion = :0 AND id_campaña = :1";

                $evaluacionCampo = $this->consultarFila($sql, [$cabecera->p_idEvaluacion, $idCampaña]);

                $sql = "SELECT id_tipo_riesgo FROM tipo_riesgo 
                WHERE minimo <= :0 AND COALESCE(maximo,999999.99) >= :0  AND estado_mrcb = 1";

                $idTipoRiesgo = $this->consultarValor($sql, [$evaluacionCampo["indice_poblacion_calculada"]]);

                $campos_valores = ["indice_poblacion_calculada"=> $evaluacionCampo["indice_poblacion_calculada"],
                                    "nivel_infestacion"=> $evaluacionCampo["nivel_infestacion"],
                                    "intensidad_daño"=> $evaluacionCampo["intensidad_daño"],
                                    "id_tipo_riesgo"=>$idTipoRiesgo,
                                    "fecha_fin_evaluacion"=>date('Y-m-d')];

                $campos_valores_where = ["id_evaluacion"=>$cabecera->p_idEvaluacion,
                                        "id_campaña"=>$idCampaña];

                $this->update("evaluacion",$campos_valores,$campos_valores_where);

                /*COMO YA SE REGISTRO UN CAMPO COMPLETAMENTE___ verificamos si es que este tipo_riego nos va a generar
                un nueva liberacion ADICIONAL (en modo programada)*/
            }

            $this->commit();
            return array("rpt"=>true, "msj"=>$msj,"pnuevo"=>$registrePuntoNuevo,"npuntos"=>$registreUDNuevo,"tipo_riego"=>$booleanoTR);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    /*@obsolete */
    private function obtenerGuardarUMDEvaluacion($cantidadTallosDefault, $idUmd, $idCampaña, $idEvaluacion, $UMDData)
    {
        /*descripcion = 'TALLO INFESTADO' OR -- 1 UMDATA_0
                        descripcion = 'ENTRENUDO' OR      ---  2 UMDDATA_1
                        descripcion = 'ENTRENUDO DAÑADO'   OR -- 3 UMDATA_2
                        descripcion = 'MOSCA - LARVA'    -- 7 UMDATA_3
                        */

        $nivelInfestacion = round ((float) ($UMDData[0]["valor"] / $cantidadTallosDefault) * 100, 2);                  
        $intensidadDaño = round ((float) ($UMDData[2]["valor"] / $UMDData[1]["valor"]), 2);
        $indicePoblacion = round ((float) ($UMDData[3]["valor"] / $cantidadTallosDefault) , 4);

        $sql = "SELECT id_tipo_riesgo FROM tipo_riesgo 
                WHERE minimo <= :0 AND COALESCE(maximo,999999.99) >= :0 
                AND estado_mrcb = 1";

        $idTipoRiesgo = $this->consultarValor($sql, [$indicePoblacion]);
        /*Obtener idtiporiesgo en el que se enceuntra mi indice de poblacion*/


        $campos_valores = [     
                "estado_evaluacion"=>true,                        
                "id_evaluador_cierre"=>$this->idEvaluador,
                "nivel_infestacion"=>$nivelInfestacion,
                "intensidad_daño"=>$intensidadDaño,
                "indice_poblacion_calculada"=>$indicePoblacion,
                "id_tipo_riesgo"=>$idTipoRiesgo
                ];

        $campos_valores_where =[
                "id_campaña"=>$idCampaña,
                "id_evaluacion"=>$idEvaluacion,
                "id_umd"=>$idUmd
                ];

        $this->update("evaluacion_umd", $campos_valores,$campos_valores_where);
    }

    private function obtenerDatosResumenEvaluacion($tallosInfestados, $entrenudos, $entrenudosDañados, $moscasLarvas){
        $sql = "SELECT fn_get_variable('tallos_totales_evaluados');";
        $cantidadTallosDefault  = $this->consultarValor($sql);

        $nivelInfestacion = round ((float) ($tallosInfestados / $cantidadTallosDefault) * 100, 2);    /*%*/
        $intensidadDaño = round ((float) ($entrenudosDañados / $entrenudos), 2); /*Indice*/
        $indicePoblacionCalculada = round ((float) ($moscasLarvas / $cantidadTallosDefault) , 4); /*Indice*/

        $idTipoRiesgo = $this->obtenerTipoRiesgo($indicePoblacionCalculada);

        return ["id_tipo_riesgo"=> $idTipoRiesgo,
                    "nivel_infestacion" => $nivelInfestacion,
                        "intensidad_daño"=> $intensidadDaño,
                            "indice_poblacion_calculada"=> $indicePoblacionCalculada];
    }

    /*@obsolete */
    private function obtenerTipoRiesgo($indicePoblacionCalculada){
        $sql = "SELECT id_tipo_riesgo FROM tipo_riesgo 
                WHERE minimo <= :0 AND COALESCE(maximo,999999.99) >= :0 
                AND estado_mrcb = 1";

        return $this->consultarValor($sql, [$indicePoblacionCalculada]);

    }

    /*@obsolete */
    public function generarUMDEvaluacion($idUmd, $idEvaluacion){
       try {
        $this->beginTransaction();

        $sql = "SELECT id_campaña FROM evaluacion WHERE id_evaluacion = :0";
        $idCampaña = $this->consultarValor($sql, [$idEvaluacion]);

        $sql = "SELECT id_evaluador_asignado FROM evaluacion_umd WHERE id_evaluacion = :0";    
        $this->idEvaluador = $this->consultarValor($sql, [$idEvaluacion]);

        $sql = "SELECT COUNT(distinct(numero_punto)) FROM evaluacion_umd_punto 
                WHERE  id_evaluacion = :0 AND id_umd = :1";

        $puntos_muestreados = $this->consultarValor($sql,[$idEvaluacion, $idUmd]); 

        if ($puntos_muestreados >= 5){

            

            $sql = "SELECT
                    SUM(dato_muestreo_1)::integer as _1,
                    SUM(dato_muestreo_2)::integer as _2,
                    SUM(dato_muestreo_3)::integer as _3,
                    SUM(dato_muestreo_7)::integer as _7              
                    FROM evaluacion_umd_punto p   
                       WHERE id_evaluacion = :0 
                    AND id_umd = :1
                    GROUP BY id_umd, id_evaluacion
                    ORDER BY 1";

            $pre_subtotales = $this->consultarFila($sql, [$idEvaluacion, $idUmd]);
            $subtotales = [["valor"=>$pre_subtotales["_1"]],
                           ["valor"=>$pre_subtotales["_2"]],
                           ["valor"=>$pre_subtotales["_3"]],
                           ["valor"=>$pre_subtotales["_7"]]];
                    /*M[0] : 1, 
                      M[1] : 2
                      M[2] : 3
                      M[3] : 7*/
            $this->obtenerGuardarUMDEvaluacion($cantidadTallosDefault,
                                                   $idUmd,
                                                   $idCampaña,
                                                   $idEvaluacion, 
                                                   $subtotales);
            
            $msj = "Se ha registrado UMD COMPLETAMENTE.";
        } else {
            $msj = "Se necesita 5 puntos para registrar un UMD.";
        }

        $this->commit();
        return array("rpt"=>true, "msj"=>$msj);

        } catch (Exception $exc) {
            $this->rollBack();
            throw $exc;
        }
    }

    private function neoGuardarMuestras($objEvaluacion)
    {
        try {

            /*Registrar*/
            /*INSERTAR LA MUESTRA en la nueva tabla evaluacion_umd_punto_muestra*/
            $this->setIdEvaluacion($objEvaluacion->id);
            $topeMuestra = 5;
            $topePunto = 5;
            $nMuestra = 0; /*Contador de muestras*/
            $nPunto = 0; /*Contador de puntos.*/
            $d_1  = $d_2 = $d_3 = $d_4 = $d_5 = $d_6 = $d_7 = $d_8 = 0;
            $dm_1  = $dm_2 = $dm_3 = $dm_4 = $dm_5 = $dm_6 = $dm_7 = $dm_8 = 0;

            $arregloMuestras = $objEvaluacion->muestras;

            foreach ($arregloMuestras as $indice => $objMuestra) {
                /*Insertamos una por una la muestra, sumamos el contador de muestra por cada iteración y sumamos a 1 el punto por cada 
                5 muestras (y devolvemos lam uestra a 0)*/
                $campos_valores_ = array(
                            "id_umd"=>$objMuestra->id_umd,
                            "id_evaluacion"=>$objMuestra->id_evaluacion,
                            "numero_muestra"=>$objMuestra->numero_muestra,
                            "numero_punto"=>$objMuestra->numero_punto,
                            "dato_muestreo_1"=>$objMuestra->d_1,
                            "dato_muestreo_2"=>$objMuestra->d_2,
                            "dato_muestreo_3"=>$objMuestra->d_3,
                            "dato_muestreo_4"=>$objMuestra->d_4,
                            "dato_muestreo_5"=>$objMuestra->d_5,
                            "dato_muestreo_6"=>$objMuestra->d_6,
                            "dato_muestreo_7"=>$objMuestra->d_7,
                            "dato_muestreo_8"=>$objMuestra->d_8
                            );

                $this->insert("evaluacion_umd_punto_muestra",$campos_valores_); 

                $d_1 += $objMuestra->d_1;
                $d_2 += $objMuestra->d_2;
                $d_3 += $objMuestra->d_3;
                $d_4 += $objMuestra->d_4;
                $d_5 += $objMuestra->d_5;
                $d_6 += $objMuestra->d_6;
                $d_7 += $objMuestra->d_7;
                $d_8 += $objMuestra->d_8;

                $nMuestra++;

                if ($nMuestra == 5){
                    /*Registrar Punto, nPunto++*/
                    $nMuestra = 0;

                    $campos_valores = array(
                            "numero_punto"=>$objMuestra->numero_punto,
                            "id_umd"=>$objMuestra->id_umd,
                            "id_evaluacion"=>$objMuestra->id_evaluacion,
                            "id_evaluador_registro"=>$objMuestra->id_evaluador_asignado,
                            "dato_muestreo_1"=>$d_1,
                            "dato_muestreo_2"=>$d_2,
                            "dato_muestreo_3"=>$d_3,
                            "dato_muestreo_4"=>$d_4,
                            "dato_muestreo_5"=>$d_5,
                            "dato_muestreo_6"=>$d_6,
                            "dato_muestreo_7"=>$d_7,
                            "dato_muestreo_8"=>$d_8,
                            "latitud"=>$objMuestra->latitud,
                            "longitud"=>$objMuestra->longitud
                            );

                    $this->insert("evaluacion_umd_punto",$campos_valores); 
                    $nPunto++;

                    $dm_1 += $d_1;
                    $dm_2 += $d_2;
                    $dm_3 += $d_3;
                    $dm_4 += $d_4;
                    $dm_5 += $d_5;
                    $dm_6 += $d_6;
                    $dm_7 += $d_7;
                    $dm_8 += $d_8;

                    $d_1  = $d_2 = $d_3 = $d_4 = $d_5 = $d_6 = $d_7 = $d_8 = 0;

                    if ($nPunto == 5){
                        $nPunto = 0;
                        /*Registrar Umd (Actualizar)*/
                        /*1: Tallos, 2:EntrenudoTotales, 3: EntrenudosDañados, 7: MoscasLarvas*/
                        $datosResumen = $this->obtenerDatosResumenEvaluacion($dm_1,$dm_2,$dm_3,$dm_7);
                        $campos_valores = array(
                                "estado_evaluacion"=> true,
                                "id_evaluador_cierre"=> $objMuestra->id_evaluador_asignado,
                                "indice_poblacion_calculada"=> $datosResumen["indice_poblacion_calculada"],
                                "nivel_infestacion"=> $datosResumen["nivel_infestacion"],
                                "intensidad_daño"=> $datosResumen["intensidad_daño"],
                                "id_tipo_riesgo"=> $datosResumen["id_tipo_riesgo"]                        
                                );

                        $campos_valores_where = [
                                "id_evaluacion" => $objMuestra->id_evaluacion,
                                "id_umd"=> $objMuestra->id_umd
                                ];

                        $this->update("evaluacion_umd",$campos_valores,$campos_valores_where); 
                        $dm_1  = $dm_2 = $dm_3 = $dm_4 = $dm_5 = $dm_6 = $dm_7 = $dm_8 = 0;

                    }
                }
                
            }

            /*Despues de insertar todo, verificar si se completó el campo*/

            $sql = "SELECT 
                    (SELECT COUNT(cu.id_umd) FROM campaña_umd cu WHERE cu.estado_activo = 'A' AND cu.id_campaña = e.id_campaña) as totales,
                    (SELECT COUNT(eu.id_umd) FROM evaluacion_umd eu WHERE eu.estado_evaluacion AND eu.id_evaluacion = e.id_evaluacion) completadas
                     FROM evaluacion e WHERE NOT e.estado_evaluacion AND e.id_evaluacion = :0 ";

            $evaluacionFila = $this->consultarFila($sql, $this->getIdEvaluacion());

            $estaCompleto = false;
            if ($evaluacionFila["completadas"] > 0 && $evaluacionFila["completadas"] == $evaluacionFila["totales"]){
                $estaCompleto = true;
            }

            if ($estaCompleto){                        
                /*Operación Campo.*/
                $sql = "SELECT
                          AVG(nivel_infestacion)::numeric(5,2) as nivel_infestacion,
                          AVG(indice_poblacion_calculada)::numeric(6,4) as indice_poblacion_calculada,
                          AVG(intensidad_daño)::numeric(6,4) as intensidad_daño
                          FROM evaluacion_umd
                          WHERE id_evaluacion = :0";

                $evaluacionCampo = $this->consultarFila($sql, $this->getIdEvaluacion());

                $idTipoRiesgo = $this->obtenerTipoRiesgo($evaluacionCampo["indice_poblacion_calculada"]);

                $campos_valores = ["indice_poblacion_calculada"=> $evaluacionCampo["indice_poblacion_calculada"],
                                    "nivel_infestacion"=> $evaluacionCampo["nivel_infestacion"],
                                    "intensidad_daño"=> $evaluacionCampo["intensidad_daño"],
                                    "id_tipo_riesgo"=>$idTipoRiesgo,
                                    "estado_evaluacion"=>true,
                                    "fecha_fin_evaluacion"=>date('Y-m-d')];

                $campos_valores_where = ["id_evaluacion"=>$this->getIdEvaluacion()];

                $this->update("evaluacion",$campos_valores,$campos_valores_where);

                /*COMO YA SE REGISTRO UN CAMPO COMPLETAMENTE___ verificamos si es que este tipo_riego nos va a generar
                un nueva liberacion ADICIONAL (en modo programada)*/
            } 

            /*Devolvemos EXITO, de lado del móvil se hace UPDATE a muestras, puntos, umd y evaluaciones.*/
            return true;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function recibirDatosApp($idUsuario, $evaluaciones)
    {
        try {

            /*evaluador or supervisor*/            
            /*liberador*/

            /*
                for -> evaluaciones
                    neoGuardarMuestra
            */
            $this->beginTransaction();
           
            foreach ($evaluaciones as $key => $objEvaluacion) {
               $exito = $this->neoGuardarMuestras($objEvaluacion);
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
    
}