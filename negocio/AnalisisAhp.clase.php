<?php

require_once '../datos/Conexion.clase.php';

class AnalisisAhp extends Conexion {
  private $idAnalisis;
  private $STR_ID_CRITERIOS = "(";
  private $SQL_TIP = "";


  public function getIdAnalisis()
  {
      return $this->idAnalisis;
  }
  
  
  public function setIdAnalisis($idAnalisis)
  {
      $this->idAnalisis = $idAnalisis;
      return $this;
  }

  public function listar()
  {
      try {

          $sql = "SELECT * FROM criterio_ahp ORDER BY 1";
          $data = $this->consultarFilas($sql);

          return array("rpt"=>true, "msj"=>$data);
        } catch (Exception $exc) {
          return array("rpt"=>false,"msj"=>$exc);
          throw $exc;
        }
  }

  public function listarCamposAnalisis()
  {
     try {
          $sql = "SELECT 
            ce.nombre_campo,
            ce.id_campo, (ce.nivel_infeccion*100)::numeric(5,2) as nivel_infestacion, ce.edad_cultivo::int, ce.numero_corte, ce.numero_liberaciones, ce.numero_hectareas,
            vc.descripcion as variedad_caña, tr.descripcion as tipo_riego, 
            (CASE ce.tipo_liberacion WHEN 'P' THEN 'POR PROTOCOLO' ELSE 'ADICIONAL' END) as tipo_liberacion
            FROM _campos_evaluados ce
            INNER JOIN variedad_caña vc ON vc.id_variedad_caña = ce.variedad_caña
            INNER JOIN tipo_riego tr ON tr.id_tipo_riego = ce.tipo_riego ORDER BY id_campo";
          $data = $this->consultarFilas($sql);

          return array("rpt"=>true, "msj"=>$data);
        } catch (Exception $exc) {
          return array("rpt"=>false,"msj"=>$exc);
          throw $exc;
        }
  }

   public function obtenerCamposAnalisis($fechaInicio, $fechaFin)
  {
     try {

          $tmpArr = split("-",$fechaInicio);
          $fechaInicio = $tmpArr[2] ."-".$tmpArr[1]."-".$tmpArr[0];
          $tmpArr = split("-",$fechaFin);
          $fechaFin = $tmpArr[2] ."-".$tmpArr[1]."-".$tmpArr[0];
/*
          $sql = "SELECT 
            li.id_liberacion,
            fn_fecha(li.fecha_liberacion) as fecha_liberacion,            
            cp.nombre_campo,            
            (SELECT nivel_infestacion FROM evaluacion e
              WHERE  e.estado_evaluacion
              AND e.fecha_inicio_evaluacion = (SELECT MIN(_e.fecha_inicio_evaluacion) FROM evaluacion _e WHERE _e.id_evaluacion = e.id_evaluacion)
            ) as nivel_infestacion,
            (SELECT fn_meses_entre(fecha_inicio_cosecha, current_date) FROM campaña __ca WHERE __ca.id_campaña = ca.id_campaña)  as edad_cultivo,
            ca.numero_soca as numero_corte, 
            (SELECT COUNT(_l.id_liberacion) FROM liberacion _l WHERE _l.id_liberacion = li.id_liberacion AND estado_liberacion = true AND estado <> 'P') as numero_liberaciones,           
            (SELECT SUM(hectarea_disponible) FROM campaña_umd cu WHERE cu.id_campaña = ca.id_campaña AND estado_activo = 'A') as numero_hectareas,
            vc.descripcion as variedad_caña, 
            tr.descripcion as tipo_riego, 
            fn_tipo_lib_eval(li.tipo_liberacion) as tipo_liberacion
            FROM -- _campos_evaluados ce
            liberacion li
            INNER JOIN campaña ca ON ca.id_campaña = li.id_campaña AND ca.estado ='A'
            INNER JOIN siembra si ON si.id_siembra = ca.id_siembra --AND si.estado ='A'
            INNER JOIN campo cp ON cp.id_campo =  si.id_campo --AND cp.estado ='A'
            INNER JOIN variedad_caña vc ON vc.id_variedad_caña = si.id_variedad_caña
            INNER JOIN tipo_riego tr ON tr.id_tipo_riego = si.id_tipo_riego 
            WHERE li.fecha_liberacion >= :0 AND li.fecha_liberacion <= :1 AND
            li.estado = 'P' AND NOT estado_liberacion
            ORDER BY cp.id_campo";
*/
          /**/

          $sql = "SELECT  
                fecha_liberacion,
                ca.nombre_campo,
                edad_cultivo,
                numero_liberaciones,
                nivel_infeccion as nivel_infestacion,
                numero_corte,
                vc.descripcion as variedad_caña, 
                tr.descripcion as tipo_riego,
                numero_hectareas,
                tipo_riego,
                 fn_tipo_lib_eval(tipo_liberacion) as tipo_liberacion
                FROM _campos_evaluados _ce
                INNER JOIN campo ca ON ca.id_campo = _ce.id_campo
                INNER JOIN variedad_caña vc ON vc.id_variedad_caña = _ce.variedad_caña
                INNER JOIN tipo_riego tr ON tr.id_tipo_riego = _ce.tipo_riego 
                WHERE _ce.fecha_liberacion >= :0 AND _ce.fecha_liberacion <= :1 AND
                _ce.estado
                ORDER BY fecha_liberacion
                ";

          $data = $this->consultarFilas($sql, [$fechaInicio, $fechaFin]);

          return array("rpt"=>true, "msj"=>$data);
        } catch (Exception $exc) {
          return array("rpt"=>false,"msj"=>$exc);
          throw $exc;
        }
  }

  public function obtenerDataGenerarPriorizaciones()
  {
     try {
          $sql = "SELECT id_analisis_ahp,
                         to_char(fecha_registro,'DD-MM-YYYY') as fecha_registro, 
                         to_char(fecha_inicio,'DD-MM-YYYY') as fecha_inicio, 
                         to_char(fecha_fin,'DD-MM-YYYY') as fecha_fin
          FROM analisis_ahp aa
          ORDER BY id_analisis_ahp DESC, fecha_registro DESC LIMIT 1";

          $ultimaFila = $this->consultarFila($sql);

          $sql = "SELECT 
          c.nombre_campo,
          porcentaje_priorizacion as prioridad,
          (CASE WHEN tipo_liberacion = 'A' THEN 'ADICIONAL' ELSE 'POR PROTOCOLO' END) as tipo_liberacion,
          ((fn_get_variable('cantidad_moscas_hectarea_liberacion'))::int * c.hectarea)::int as numero_moscas
          FROM analisis_ahp_priorizacion aap
          INNER JOIN campo c ON c.id_campo = aap.id_campo 
          WHERE id_analisis_ahp = :0
          ORDER BY numero_orden";

          $cuerpo = $this->consultarFilas($sql, array($ultimaFila["id_analisis_ahp"]));

          $ultimoAnalisis = array(
              "cabecera" => $ultimaFila,
              "cuerpo" => $cuerpo);
         
          $sql= "SELECT
            to_char(aa.fecha_registro,'DD-MM-YYYY') as fecha_registro, 
            to_char(aa.fecha_inicio,'DD-MM-YYYY') as fecha_inicio, 
            to_char(aa.fecha_fin,'DD-MM-YYYY') as fecha_fin,
            aa.numero_campos,
            CONCAT(p.nombres,' ',p.apellidos) as usuario_registro,
            id_analisis_ahp
            FROM analisis_ahp aa
            INNER JOIN usuario u ON aa.id_usuario_registro = u.id_usuario
            INNER JOIN personal p ON p.id_personal = u.id_personal
            ORDER BY aa.fecha_registro DESC, id_analisis_ahp DESC";

          $historialAnalisis = $this->consultarFilas($sql);

          return array("rpt"=>true, "data"=>array("ultimo_analisis"=>$ultimoAnalisis, "historial_analisis"=>$historialAnalisis));
        } catch (Exception $exc) {
          return array("rpt"=>false,"msj"=>$exc);
          throw $exc;
        }
  }

   public function obtenerAnalisis($id_analisis_ahp = NULL)
  {
     try {

          if (!isset($id_analisis_ahp)){
             $sql = "SELECT id_analisis_ahp 
              FROM analisis_ahp aa
              ORDER BY id_analisis_ahp DESC, fecha_registro DESC LIMIT 1";

              $id_analisis_ahp = $this->consultarValor($sql);
          }

          $sql = "SELECT to_char(fecha_registro,'DD-MM-YYYY') as fecha_registro, 
                         to_char(fecha_inicio,'DD-MM-YYYY') as fecha_inicio, 
                         to_char(fecha_fin,'DD-MM-YYYY') as fecha_fin
          FROM analisis_ahp aa WHERE id_analisis_ahp = :0";

          $fechasAnalisis = $this->consultarFila($sql, array($id_analisis_ahp));

          $sql = "SELECT 
          c.nombre_campo,
          porcentaje_priorizacion as prioridad,
          ((fn_get_variable('cantidad_moscas_hectarea_liberacion'))::int * c.hectarea)::int as numero_moscas,
          (CASE WHEN tipo_liberacion = 'A' THEN 'ADICIONAL' ELSE 'POR PROTOCOLO' END) as tipo_liberacion
          FROM analisis_ahp_priorizacion aap
          INNER JOIN campo c ON c.id_campo = aap.id_campo 
          WHERE id_analisis_ahp = :0
          ORDER BY numero_orden";

          $cuerpo = $this->consultarFilas($sql, array($id_analisis_ahp));

          $analisis = array(
              "cabecera" => $fechasAnalisis,
              "cuerpo" => $cuerpo);
          
          return array("rpt"=>true, "data"=>array("ultimo_analisis"=>$analisis));
        } catch (Exception $exc) {
          return array("rpt"=>false,"msj"=>$exc);
          throw $exc;
        }
  }
  
  public function exeAlgoritmoAHP($fechaInicio,$fechaFin) {
        try {
          $this->beginTransaction();
          $tmpArr = split("-",$fechaInicio);
          $fechaInicio = $tmpArr[2] ."-".$tmpArr[1]."-".$tmpArr[0];
          $tmpArr = split("-",$fechaFin);
          $fechaFin = $tmpArr[2] ."-".$tmpArr[1]."-".$tmpArr[0];

            //CRITERIOS CON LOS QUE TRABAJAR.

          $sql = "SELECT id_criterio_ahp FROM criterio_ahp WHERE estado_activacion = 'A' ORDER BY id_criterio_ahp";
            $arregloCriterios = $this->consultarFilas($sql);
            $countCriterios  = count($arregloCriterios);
            foreach ($arregloCriterios as $key => $valor) {
                $tmp_id_ahp = $valor["id_criterio_ahp"];

                switch ($tmp_id_ahp) {
                        case "1":
                            $this->SQL_TIP.= " nivel_infeccion ";
                            break;
                        case "2":
                            $this->SQL_TIP.= " edad_cultivo ";
                            break;
                        case "3":
                            $this->SQL_TIP.= " numero_corte  ";
                            break;
                        case "4":
                            $this->SQL_TIP.= " variedad_caña ";
                            break;
                        case "5":
                            $this->SQL_TIP.= " (CASE WHEN tipo_riego = 1 THEN numero_liberaciones * 1/3 ELSE numero_liberaciones * 2/3 END)";
                            break;
                        case "6":
                            $this->SQL_TIP.= " numero_hectareas  ";
                            break;
                }

                $this->SQL_TIP.= " as v_".$tmp_id_ahp." ";
                $this->STR_ID_CRITERIOS .= $valor["id_criterio_ahp"]. ($key < $countCriterios - 1 ? ', ' : ') '); 

                if ($countCriterios - $key > 1) {
                    $this->SQL_TIP.=", ";
                }       
            }

            $cboFecha = " AND ce.fecha_liberacion >= '$fechaInicio' AND ce.fecha_liberacion <= '$fechaFin'  ";
            $cboEstado = " WHERE ce.estado ".$cboFecha;

            //obtener el arreglo con los valores desde las incidencias.
            //SE QUEDA IGUAL.
            /*
             - Aqui este SELECT se consigue en base a las incidencias y los valores deben extrarse obligatoriamente con las
             etiquetas var_1, var_2 y var_3, si es posible usar "AS" para definir este alias.
             Ejemplo: select codigo_incidencia, descripcion, tipo_reportador as var_1, tipo_lugar as var_2, tipo_incidencia as var_3.
             //esta consulta debe tener el formato DESEADO DE SALIDA, incluyendo descripciones, JOINS para mostrar todo lo necesario.
            */
            $sqlListar = "SELECT id_campo, 
                 ".$this->SQL_TIP.", tipo_liberacion
                 FROM _campos_evaluados ce"
                    . $cboEstado . " ORDER BY 1 ";


            $matrizEntradas = $this->consultarFilas($sqlListar);
            $countEntradas= count($matrizEntradas);

            /*GENERANDO ID ANALISIS*/
            $this->setIdAnalisis($this->consultarValor("SELECT (CASE WHEN COUNT(*) > 0 THEN MAX(id_analisis_ahp) + 1 ELSE 1 END) FROM analisis_ahp"));
            /*GUARDANDO  ENTRADAS DE ANALISIS*/

            $this->guardarEntradasAnalisis($matrizEntradas,$arregloCriterios);

            $matrizVector = $this->obtenerMatrizVector($arregloCriterios);

            $matrizCriterios = array(); /*id_critero  + array(n) : n = numero_entraedas*/

            foreach($arregloCriterios as $llave => $valor){
                $actualCriterio = $valor["id_criterio_ahp"];                
                $matrizCriterios[$actualCriterio] = array();
               # $tempCriterios[$actualCriterio] = array();
                $tempCriterios[$actualCriterio] = array();

                foreach($matrizEntradas as $_key => $_value){
                    /*Obtendre elvalor de las entras Solo de este arregloCriterios.*/
                    /*$key = numero de campo entrando.*/
                    $matrizCriterios[$actualCriterio][$_key] = array();

                    $valor_j = $_value["v_".$actualCriterio];

                    $valor_dos = $this->consultarValor(" SELECT n_criterio FROM
                                                      matriz_ahp_valor WHERE id_criterio_ahp = $actualCriterio
                                                      AND $valor_j > valor_inferior
                                                      AND $valor_j <= COALESCE(valor_superior,9999999999)");
                    if (!isset($valor_dos)){
                      $valor_dos = $this->consultarValor(" SELECT n_criterio FROM
                             matriz_ahp_valor WHERE id_criterio_ahp = $actualCriterio
                                             AND $valor_j >= valor_inferior
                                             AND $valor_j <= COALESCE(valor_superior,9999999999)");      
                    }

                    foreach ($matrizEntradas as $_key_2 => $_value_2) {
                        $tempCriterios[$actualCriterio][$_key_2] = isset($tempCriterios[$actualCriterio][$_key_2]) ? $tempCriterios[$actualCriterio][$_key_2]  : 0;
                        if ($_key == $_key_2) {
                                $valorCruce = "1.000";
                        } else{

                            $valor_i = $_value_2["v_".$actualCriterio]; //0.
                            $valor_uno = $this->consultarValor(" SELECT n_criterio FROM
                                                          matriz_ahp_valor WHERE id_criterio_ahp = $actualCriterio
                                                          AND $valor_i > valor_inferior
                                                          AND $valor_i <= COALESCE(valor_superior,9999999999)"); 


                              if (!isset($valor_uno)){
                                #var_dump($actualCriterio, "valor i ".$valor_i, "valor_uno null");
                                  $valor_uno = $this->consultarValor(" SELECT n_criterio FROM
                                         matriz_ahp_valor WHERE id_criterio_ahp = $actualCriterio
                                                         AND $valor_i >= valor_inferior
                                                         AND $valor_i <= COALESCE(valor_superior,9999999999)");      
                              }

                             $valorCruce = $this->consultarValor(" SELECT valor_$valor_uno FROM
                                              matriz_ahp_valor WHERE id_criterio_ahp = $actualCriterio
                                              AND n_criterio = $valor_dos");    

                        }

                        $tempCriterios[$actualCriterio][$_key_2] += $valorCruce;
                        array_push($matrizCriterios[$actualCriterio][$_key], $valorCruce);
                    }

                }
            }
            /*SEGHUNDA RONDA*/

            foreach ($matrizCriterios as $idCriterio => $arregloCampos) {                        

                foreach($arregloCampos as $numCampo => $valor){
                    $tempSumatoria = 0; $i = 0;
                    foreach($valor as $_numCampo => $_valor){
                        $_valor = $_valor * (round( (1/$tempCriterios[$idCriterio][$_numCampo]), 4));
                        $tempSumatoria += $_valor;
                        $i++;
                    }
                    $matrizCriterios[$idCriterio][$numCampo] = round(($tempSumatoria / $i),3); /*promedio*/
                }
            }

           

            $arregloPreFinal = array();

            foreach ($matrizCriterios as $idCriterio => $arregloCampos) {

                foreach($arregloCampos as $numCampo => $valor){
                    #var_dump($valor," : ",$matrizVector[$idCriterio]);
                    $arregloPreFinal[$numCampo]["x"] = isset($arregloPreFinal[$numCampo]) ? $arregloPreFinal[$numCampo]["x"] : "0.00";                
                    $arregloPreFinal[$numCampo]["x"] += $valor * $matrizVector[$idCriterio];
                }

            }

            $arregloPreFinal = $this->ordenarArreglo($arregloPreFinal,"x",SORT_DESC);

            $sql = "SELECT id_campo, nombre_campo, tipo_liberacion as tipo_liberacion_raw,
                 (CASE ce.tipo_liberacion WHEN 'P' THEN 'POR PROTOCOLO' ELSE 'ADICIONAL' END) as tipo_liberacion
            FROM _campos_evaluados ce ".$cboEstado." ORDER BY 1";
            $camposEvaluados = $this->consultarFilas($sql);

            $i = 0;
            $arregloFinal = array();
            foreach ($arregloPreFinal as $key => $value) {
               $camposEvaluados[$key]["prioridad"] = $value["x"];
               $arregloFinal[$i] = $camposEvaluados[$key];
               $i++;
            }

            /*CON LOS RESULTADOS FINALES GUARDAMOS: 
              A) MATRIZ VECTOR
              B) ARREGLO PRIORIZACION FINAL*/

            /*A) GUARDANDO VMATRIZ VECTOR, Se consultan todos los valores buscando la matriz vector ÚLTIMA activa, si son la misma cantidad de vector y son exactamente
              los mismos porcentajes vectores, los datos no se guardarán; Caso contrario se actualizará la matriz vector.*/
            $this->guardarMatrizVector($matrizVector);

            /*B) ARREGLO PRIORIZACION FINAL*/
            $this->guardarResultadosPriorizacion($arregloFinal);

            /*GUARDANDO EL ANALISIS*/
            $this->guardarAnalisis($countEntradas, $fechaInicio, $fechaFin);  

            $this->commit();

            return array("rpt"=>true, "data"=>array("matriz_vector"=>$matrizVector, "arreglo_priorizado"=>$arregloFinal), "msj"=>"Análisis concluido. Priorización REGISTRADA.");

        } catch (Exception $exc) {
          return array("rpt"=>false,"msj"=>$exc);
          throw $exc;
        }
    }

  private function obtenerArregloIncidenciasFormateado($arregloIncidencias, $cantidadCriterios) {
        $returnArray = array(array(), array(), array(),array(), array(), array());
        //$arreglo0 = array(); $arreglo1 = array(); $arreglo2 = array(); $arreglo3 = array();
        //obtener todos los valores de una fila y sumarlos.
        for ($i = 0; $i < count($arregloIncidencias); $i++) {
            for ($j = 1; $j <= $cantidadCriterios; $j++) {
                array_push($returnArray[$j - 1], $arregloIncidencias[$i][$j]);
            }
        }
        return $returnArray;
  }

  public function obtenerMatrizVector($arregloCriterios = NULL)
   {             
        $arregloSup = array();

        foreach ($arregloCriterios as $key => $valores) {
            $sql = "SELECT  id_criterio_ahp_dos as id_criterio_ahp,
                     (1 / (SUM(valor_criterio)  + 1))::numeric(10,4) as valor_ 
                    FROM matriz_ahp 
                    WHERE id_criterio_ahp_dos = ".$valores["id_criterio_ahp"]." 
                            AND id_criterio_ahp_uno IN ".$this->STR_ID_CRITERIOS."
                            GROUP BY id_criterio_ahp_dos
                            ORDER BY 1";

            $arregloSup[$valores["id_criterio_ahp"]] =  $this->consultarFila($sql);
        }

        $matrizVector = array();
        foreach($arregloSup as $key => $valores){

            $sql = "SELECT id_criterio_ahp_dos as id_criterio_ahp,
                    valor_criterio  as pf_valor_ 
                    FROM matriz_ahp WHERE id_criterio_ahp_uno = ".$valores["id_criterio_ahp"]." AND
                    id_criterio_ahp_dos IN ".$this->STR_ID_CRITERIOS."
                    ORDER BY 1";

            $ar = $this->consultarFilas($sql);

            $temp = 0; $i  = 0;
            foreach ($ar as $k => $v) {    
                $temp += $v["pf_valor_"] * $arregloSup[$v["id_criterio_ahp"]]["valor_"];
                $i++;
            }
            
            $valor_ = round(($temp + ($valores["valor_"])) / ($i + 1),4)*100;
            $matrizVector[$key] = $valor_;
        }

        return $matrizVector;
    }

  public function ordenarArreglo($array, $on, $order=SORT_ASC)
        {
            $new_array = array();
            $sortable_array = array();

            if (count($array) > 0) {
                foreach ($array as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $k2 => $v2) {
                            if ($k2 == $on) {
                                $sortable_array[$k] = $v2;
                            }
                        }
                    } else {
                        $sortable_array[$k] = $v;
                    }
                }

                switch ($order) {
                    case SORT_ASC:
                        asort($sortable_array);
                    break;
                    case SORT_DESC:
                        arsort($sortable_array);
                    break;
                }

                foreach ($sortable_array as $k => $v) {
                    $new_array[$k] = $array[$k];
                }
            }

            return $new_array;
        }

  public function guardarEntradasAnalisis($matrizEntradas, $arregloCriterios)
  {    
    foreach ($matrizEntradas as $key => $value) {
      $campos_valores = array("id_analisis_ahp"=>$this->getIdAnalisis(),
          ##"id_campaña"=>NULL,
          ##"id_siembra"=>NULL,
          "tipo_liberacion"=>$value["tipo_liberacion"],
          "id_campo"=>$value["id_campo"]);

          foreach ($arregloCriterios as $_key => $_value) {
            $campos_valores["criterio_".$_value["id_criterio_ahp"]] = isset($value["v_".$_value["id_criterio_ahp"]]) ? $value["v_".$_value["id_criterio_ahp"]] : NULL;
          }

      ##var_dump($campos_valores, "inseter");
      $this->insert("entradas_ahp", $campos_valores);    
    }  

  }

  public function guardarMatrizVector($matrizVector)
  {    

    /*Primero consulta si existe o hay matriz vector válida.*/
    $matrizVectorPrevia = $this->consultarFilas("SELECT id_analisis_ahp, id_criterio_ahp, valor_porcentual FROM matriz_vector_ahp_historico
                     WHERE estado_activo = 'A' ORDER BY id_criterio_ahp");
    $countMatrizVector = count($matrizVectorPrevia);

    $crearNuevoVector = false;
    if ($countMatrizVector > 0){
      /*Existe data, verifica la cantidad de registros.*/
      if ($countMatrizVector == count($matrizVector)){
        /*Mismo tamaño de vector, es decir misma cantidad de criterios.*/
          foreach ($matrizVectorPrevia as $key => $value) {
            if (abs ( ($value["valor_porcentual"] - $matrizVector[$value["id_criterio_ahp"]]) /
              $matrizVector[$value["id_criterio_ahp"]]) >= 0.00001 ) {
              $crearNuevoVector = true;
              break;
            }
          }
      } else {
        $crearNuevoVector = true;
      }
    } else {
       $crearNuevoVector = true; 
    }

    if ($crearNuevoVector){
       $consulta = $this->dblink->prepare("UPDATE matriz_vector_ahp_historico SET estado_activo = 'I' WHERE estado_activo = 'A'");
       $consulta->execute();

       foreach ($matrizVector as $key => $value) {
          $campos_valores = array("id_analisis_ahp"=>$this->getIdAnalisis(),
              "id_criterio_ahp"=>$key,
              "valor_porcentual"=>$value,
              "estado_activo"=>'A');
            
          $this->insert("matriz_vector_ahp_historico", $campos_valores);    
        }
    }
  }

  public function guardarResultadosPriorizacion($arregloFinal)
  {    
    foreach ($arregloFinal as $key => $value) {
          $campos_valores = array(
              "numero_orden"=>$key+1,
              "id_analisis_ahp"=>$this->getIdAnalisis(),
              ##"id_campaña"=>NULL,
             ## "id_siembra"=>NULL,
              "porcentaje_priorizacion"=>$value["prioridad"],
              "tipo_liberacion"=>$value["tipo_liberacion_raw"],
              "id_campo"=>$value["id_campo"]);
            
          $this->insert("analisis_ahp_priorizacion", $campos_valores);    
    }
  }

  public function guardarAnalisis($numeroCampos, $fi, $ff)
  {    
    $campos_valores = array(
              "id_usuario_registro"=>4,
              "fecha_inicio"=>$fi,
              "fecha_fin"=>$ff,
              "numero_campos"=>$numeroCampos,
              "id_analisis_ahp"=>$this->getIdAnalisis());
            
     $this->insert("analisis_ahp", $campos_valores);    

  }
 
}