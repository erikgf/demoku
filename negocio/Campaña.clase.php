<?php

require_once '../datos/Conexion.clase.php';

class Campaña extends Conexion {
    private $idCampo;
    private $idSiembra;
    private $idCampaña;

    public function getIdCampo()
    {
        return $this->idCampo;
    }
    
    
    public function setIdCampo($idCampo)
    {
        $this->idCampo = $idCampo;
        return $this;
    }

    public function getIdSiembra()
    {
        return $this->idSiembra;
    }
    
    
    public function setIdSiembra($idSiembra)
    {
        $this->idSiembra = $idSiembra;
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

    public function verDetalle()
    {
        try {

            $sql = "SELECT * FROM fn_ver_campaña(:0)";
            $JSONCampo = $this->consultarValor($sql, [$this->getIdCampaña()]); 

            return array("rpt"=>true,"data"=>$JSONCampo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function cargarUdAsignacion()
    {
        try {

            $sql = "SELECT * FROM fn_obtener_ud_x_campaña(:0)";
            $JSONUds = $this->consultarValor($sql, [$this->getIdCampaña()]);             

            return array("rpt"=>true,"data"=>$JSONUds);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function registrarAsignacionCampaña($idEvaluador, $JSONUmd)
    {
        $this->beginTransaction();
        try {
            $sql = "SELECT * FROM fn_registrar_asignacion_campaña(:0,:1,:2)";
            $data = $this->consultarValor($sql, [$this->getIdCampaña(), $idEvaluador, $JSONUmd]); 

            $this->commit();
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            $this->rollBack();
            throw $exc;
        }
    }

    public function getPredataRegistro(){
        try {
            $sql = "SELECT id_tipo_riego,
                    (SELECT COALESCE(MAX(nisira_codigo),'0')::integer FROM campaña WHERE id_siembra = s.id_siembra ) as n_campaña 
                    FROM siembra s 
                    WHERE s.id_siembra = :0";
            $predata = $this->consultarFila($sql, [$this->getIdSiembra()]); 

            return $predata;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function registrarCampaña($fechaInicioCampaña, $fechaFinCampaña, $fechaInicioCosecha, $fechaFinCosecha,
                                     $año, $fechaPrimeraEvaluacion, $supervisor, $JSONUmd = "", $coordenadasCampo)
    {
        try {
            $this->beginTransaction();

            $idUsuario = $_SESSION["obj_usuario"]["id_usuario"];

            $fechaInicioCampaña = $fechaInicioCampaña == "" ? NULL : $fechaInicioCampaña;
            $fechaFinCampaña = $fechaFinCampaña == "" ? NULL : $fechaFinCampaña;
            $fechaInicioCosecha = $fechaInicioCosecha == "" ? NULL : $fechaInicioCosecha;
            $fechaFinCosecha = $fechaFinCosecha == "" ? NULL : $fechaFinCosecha;
            
            $ar = [$fechaInicioCampaña, $fechaFinCampaña, $fechaInicioCosecha, $fechaFinCosecha, $fechaPrimeraEvaluacion, $año,
                    $supervisor, $idUsuario, $this->getIdSiembra()];

            $rptaE = $this->consultarValor("SELECT fn_registrar_campaña(:0,:1,:2,:3,:4,:5,:6,:7,:8)",$ar);
            $rpta  = json_decode($rptaE);


            if ($rpta->r == 0){
                $this->rollBack();
                return array("rpt"=>false,"msj"=>$rpta->msj);
            }


            $idCampaña = $rpta->id_campaña;
            $numeroCampaña = $rpta->numero;
            $idCampo = $rpta->id_campo;

            $esPrimera = $numeroCampaña === '001';
        
            if ($esPrimera){

                if ($JSONUmd == "" || $JSONUmd == null){
                    return array("rpt"=>false,"msj"=>$rpta->msj);
                }

                $sql = "SELECT COALESCE(MAX(id_umd),0) FROM umd";
                $idUmd = $this->consultarValor($sql);

                $temp = $idUmd;

                $objJSONumd = json_decode($JSONUmd);

                foreach ($objJSONumd as $key => $UMD) {
                    //Registrar umd               
                    $paramsUmd = ["area_hectarea"=>(strlen($UMD->hectarea) <= 0 ? 1.0 : $UMD->hectarea) , 
                                            "id_umd"=>++$idUmd, "id_campo"=>$idCampo];
                                
                    $this->insert("umd",$paramsUmd);
                    //insertar umd_coordenada 
                    foreach ($UMD->coordenadas as $_ley => $umdCoordenada) {
                        $paramsCoords = ["latitud"=>$umdCoordenada->latitud, "longitud"=>$umdCoordenada->longitud, "id_umd"=>$idUmd];
                        $this->insert("umd_coordenada",$paramsCoords);
                    }

                    //insertar campaña_umd
                    $paramsCampañaUmd = ["id_campaña"=>$idCampaña,"id_umd"=>$idUmd,
                                    "numero_nivel_uno"=>$UMD->nivel_uno,"numero_nivel_dos"=>$UMD->nivel_dos,
                                    "hectarea_disponible"=>(strlen($UMD->hectarea) <= 0 ? 1 : $UMD->hectarea),"estado_activo"=>'A'];

                    if ($UMD->nivel_tres != NULL){
                        $paramsCampañaUmd["numero_nivel_tres"] = $UMD->nivel_tres;
                    }

                    $this->insert("campaña_umd",$paramsCampañaUmd);

                } 

                $this->update("campo", ["lat"=>$coordenadasCampo[0],"lng"=>$coordenadasCampo[1]], ["id_campo"=>$idCampo]);
            } else {

                //Recorrer todos los campaña_umd anteriores

            }
            $this->commit();
            return array("rpt"=>true, "data"=>$rptaE);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }
    
    public function listarCampañasMapa()
    {
        try {

            $sql = "SELECT c_.id_campaña, c_.año_campaña, s_.id_tipo_riego
            FROM campaña c_
            INNER JOIN siembra s_ ON s_.id_siembra = c_.id_siembra
            WHERE c_.estado = 'A' AND s_.id_campo = :0";
            $campañas = $this->consultarFilas($sql, [$this->getIdCampo()]);  

            return array("rpt"=>true,"data"=>$campañas);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    public function listarAñosCampañas()
    {
        try {

            $sql = "SELECT distinct c_.año_campaña
            FROM campaña c_";
            $campañas = $this->consultarFilas($sql);  

            return array("rpt"=>true,"data"=>$campañas);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }

    private function descargarDatosEvaluaciones(){
        try{
            $sql = "SELECT 
                e.id_evaluacion,
                e.id_supervisor_asignado,
                ca.nombre_campo,
                (CASE si.id_tipo_riego WHEN 1 THEN 'VÁLVULAS' ELSE 'CUARTELES' END) as tipo_umd,
                si.id_tipo_riego as tipo_riego,
                (SELECT COUNT(id_umd) FROM campaña_umd cu WHERE cu.id_campaña = c.id_campaña AND estado_activo = 'A') AS cantidad_umd,
                estado_evaluacion::integer
                 FROM evaluacion e 
                 INNER JOIN campaña c ON e.id_campaña = c.id_campaña
                 INNER JOIN siembra si ON si.id_siembra = c.id_siembra                                
                 INNER JOIN campo ca ON ca.id_campo = si.id_campo
                 WHERE e.estado = 'A'";

            $evaluacion = $this->consultarFilas($sql);

            $sql = "SELECT eu.id_evaluacion, 
                    eu.id_umd,
                    cu.numero_nivel_uno,
                    cu.numero_nivel_dos,
                    COALESCE(cu.numero_nivel_tres,'') as numero_nivel_tres,
                    eu.estado_evaluacion::integer,
                    eu.id_evaluador_asignado
                    FROM evaluacion_umd eu
                    INNER JOIN evaluacion e ON e.id_evaluacion = eu.id_evaluacion 
                    INNER JOIN campaña_umd cu ON cu.id_campaña = e.id_campaña AND eu.id_umd = cu.id_umd
                    WHERE e.estado = 'A'";

            $evaluacion_umd = $this->consultarFilas($sql);

            $sql = "SELECT 
                    eup.id_evaluacion,
                    eup.id_umd,
                    eup.numero_punto,
                    COALESCE(latitud, 0) as latitud,
                    COALESCE(longitud,0) as longitud,
                    COALESCE(eup.id_evaluador_registro, -1) as id_evaluador_registro,
                    eup.dato_muestreo_1 as d_1,
                    eup.dato_muestreo_2 as d_2,
                    eup.dato_muestreo_3 as d_3,
                    eup.dato_muestreo_4 as d_4,
                    eup.dato_muestreo_5 as d_5,
                    eup.dato_muestreo_6 as d_6,
                    eup.dato_muestreo_7 as d_7,
                    eup.dato_muestreo_8 as d_8,
                    0 as fecha_hora_registro--eup.fecha_hora_registro 
                    FROM evaluacion_umd_punto eup
                    INNER JOIN evaluacion e ON e.id_evaluacion  = eup.id_evaluacion
                    WHERE e.estado = 'A'
                    ";

            $evaluacion_umd_punto = $this->consultarFilas($sql);
            
            $sql = "SELECT 
                    eupm.id_evaluacion,
                    eupm.id_umd,
                    eupm.numero_punto,
                    eupm.numero_muestra,
                    eupm.dato_muestreo_1 as d_1,
                    eupm.dato_muestreo_2 as d_2,
                    eupm.dato_muestreo_3 as d_3,
                    eupm.dato_muestreo_4 as d_4,
                    eupm.dato_muestreo_5 as d_5,
                    eupm.dato_muestreo_6 as d_6,
                    eupm.dato_muestreo_7 as d_7,
                    eupm.dato_muestreo_8 as d_8
                    FROM evaluacion_umd_punto_muestra eupm
                    INNER JOIN evaluacion e ON e.id_evaluacion  = eupm.id_evaluacion
                    WHERE e.estado = 'A'
                    ";

            $evaluacion_umd_punto_muestra = $this->consultarFilas($sql);

            return ["evaluacion"=>$evaluacion,
                        "evaluacion_umd"=>$evaluacion_umd,
                            "evaluacion_umd_punto"=>$evaluacion_umd_punto,
                                "evaluacion_umd_punto_muestra"=>$evaluacion_umd_punto_muestra];
        } catch (Exception $exc){
            throw $exc;
        }
    }

    private function descargarDatosLiberaciones(){
        try{
            $sql = "SELECT
                l.id_liberacion,
                l.cantidad_moscas,
                ca.nombre_campo,
                (CASE si.id_tipo_riego WHEN 1 THEN 'VÁLVULAS' ELSE 'CUARTELES' END) as tipo_umd,
                si.id_tipo_riego as tipo_riego,
                (SELECT COUNT(id_umd) FROM campaña_umd cu WHERE cu.id_campaña = c.id_campaña AND estado_activo = 'A') AS cantidad_umd,
                l.estado_liberacion::integer                            
                FROM liberacion l
                INNER JOIN campaña c ON l.id_campaña = c.id_campaña
                 INNER JOIN siembra si ON si.id_siembra = c.id_siembra                                
                 INNER JOIN campo ca ON ca.id_campo = si.id_campo
                 WHERE l.estado = 'A'";

            $liberacion = $this->consultarFilas($sql);

            $sql = "SELECT lu.id_liberacion, 
                    lu.id_umd,
                    cu.numero_nivel_uno,
                    cu.numero_nivel_dos,
                    COALESCE(cu.numero_nivel_tres,'') as numero_nivel_tres,
                    lu.cantidad_moscas,
                    lu.estado_liberacion::integer,
                    lu.id_liberador_asignado
                    FROM liberacion_umd lu
                    INNER JOIN liberacion l ON l.id_liberacion = lu.id_liberacion 
                    INNER JOIN campaña_umd cu ON cu.id_campaña = l.id_campaña AND lu.id_umd = cu.id_umd
                    WHERE l.estado = 'A'";

            $liberacion_umd = $this->consultarFilas($sql);


            $sql = "SELECT 
                    lup.id_liberacion,
                    lup.id_umd,
                    lup.numero_punto,
                    COALESCE(latitud, 0) as latitud,
                    COALESCE(longitud,0) as longitud,
                    lup.fecha_hora_registro::char(19) as fecha_hora_registro--lup.fecha_hora_registro 
                    FROM liberacion_umd_punto lup
                    INNER JOIN liberacion l ON l.id_liberacion  = lup.id_liberacion
                    WHERE l.estado = 'A'
                    ";

            $liberacion_umd_punto = $this->consultarFilas($sql);

            return ["liberacion"=>$liberacion,
                        "liberacion_umd"=>$liberacion_umd,
                            "liberacion_umd_punto"=>$liberacion_umd_punto];
        } catch (Exception $exc){
            throw $exc;
        }
    }

    public function descargarDatosApp($idUsuario)
    {
        try {

            /*evaluador (1) or supervisor (2)*/       
            $sql = "SELECT id_rol FROM usuario WHERE id_usuario = :0";
            $rolUsuario = $this->consultarValor($sql, $idUsuario);
            /*liberador (3)*/


            if ($rolUsuario == 1 || $rolUsuario == 2){
                $tipo = "e";
                $data = $this->descargarDatosEvaluaciones();
            } else {
                $tipo = "l";
                $data  = $this->descargarDatosLiberaciones();
            }
            
            return array("rpt"=>true,"tipo"=>$tipo,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }


     public function recibirDatosApp($idUsuario, $JSONDataApp)
    {
        try {

            /*evaluador or supervisor*/            
            /*liberador*/
            $sql = "SELECT id_rol FROM usuario WHERE id_usuario = :0";
            $tipoUsuario = $this->consultarValor($sql, $idUsuario);
            /*liberador (3)*/


            if ($tipoUsuario == 1 || $tipoUsuario == 2){
               $tipo = "e";
            } else {
               $tipo = "l";
            }

            if ($tipo == "e"){
                include 'Muestra.clase.php';
                $objMuestra = new Muestra();
                $objMuestra->recibirDatosApp($idUsuario, json_decode($JSONDataApp));    
            } else {
                include 'LiberacionUMD.clase.php';
                $objLiberacionUMD = new LiberacionUMD();
                $objLiberacionUMD->recibirDatosApp($idUsuario, json_decode($JSONDataApp));    
            }

            return array("rpt"=>true);
        } catch (Exception $exc) {
            throw $exc;
        }
    }   


    public function reporteResultadoRiesgos($arrCampañas){
        try {

            //la evaluacion debe TENER un fecha_inicio_Evalaucion_temporal y Estado Confirmado.
            $cantArr = count($arrCampañas);
            if ($cantArr > 0){
                $str = " AND (";
                for ($i=0; $i < $cantArr; $i++) { 
                    if ($i > 0){
                        $str .= " OR ";
                    }
                    $str .= " EXTRACT(YEAR FROM e.fecha_inicio_evaluacion) = ".$arrCampañas[$i];
                }
                $str .= ")";
            }  else {
                $str = "";
            }         

            $sql = "SELECT 
                    e.numero_evaluacion,
                    ca.nombre_campo,
                    e.nivel_infestacion,
                    to_char(e.fecha_inicio_evaluacion,'DD-MM-YYYY') as fecha, 
                    v.descripcion as variedad,
                    (select sum(hectarea_disponible) FROM campaña_umd WHERE id_campaña = c.id_campaña) as area,
                    tr.descripcion as tipo_riesgo,
                    tr.indicador_color
                    FROM evaluacion e
                    INNER JOIN campaña c ON c.id_campaña = e.id_campaña
                    INNER JOIN siembra s ON s.id_siembra = c.id_siembra
                    INNER JOIN variedad_caña v ON v.id_variedad_caña = s.id_variedad_caña
                    INNER JOIN tipo_riesgo tr ON tr.id_tipo_riesgo = e.id_tipo_riesgo
                    INNER JOIN campo ca ON ca.id_campo = s.id_campo
                    WHERE e.estado <> 'P' AND e.estado_evaluacion ".$str."
                    ORDER BY fecha_inicio_evaluacion DESC " ;
             
            return $this->consultarFilas($sql);

        } catch (Exception $exc) {            
            throw $exc;
        }
    }


    public function generarUmdCampaña($k)
    {
        $arrayCampos = json_decode($k);
        $this->beginTransaction();

        if ($arrayCampos == NULL){ print_r("nulea"); return;}

        foreach ($arrayCampos as $key => $obj) {

            $sql = "SELECT c.id_campaña FROM campaña C 
                inner join siembra si ON si.id_siembra = c.id_siembra
                INNER JOIN campo ca ON ca.id_campo  = si.id_campo
                WHERE ca.estado = 'A' AND UPPER(ca.nombre_campo) = UPPER('".($obj->nombre)."')";

            $id = $this->consultarValor($sql);

            foreach ($obj->umd as $_key => $umd) {
                $sql = "INSERT INTO campaña_umd(numero_nivel_uno,numero_nivel_dos,numero_nivel_tres,  id_campaña)
                    VALUES('".$umd->nivel_uno."', '".$umd->nivel_dos."','".$umd->nivel_tres."', ".$id.")";
                
            var_dump($sql);
                $this->consulta($sql);

                /*$this->insert(utf8_decode("campaña_umd"), 
                                ["nivel_uno"=>$umd->nivel_uno, 
                                    "nivel_dos"=>$umd->nivel_dos,
                                    "nivel_tres"=>$umd->nivel_tres,
                                    "hectarea"=>$umd->hectarea]);*/
            }
        }

        $this->commit();
        return;        
    }
    
}

    