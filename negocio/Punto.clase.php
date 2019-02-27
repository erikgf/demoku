<?php

require_once '../datos/Conexion.clase.php';
require_once 'UtilidadesExtra.rasgo.php';

class Punto extends Conexion {
    private $idUmd;
    private $idEvaluacion;
    private $numeroPunto;
    private $valorPunto;
    private $idCampaña;

    use UtilidadesExtra;

    public function obtenerMuestra($idEvaluacion, $idUmd, $punto, $obtenerDatosMuestreo = true)
    {
        try {
            $arParams = [$idEvaluacion, $idUmd, $punto];
            $cabecera = $this->consultarFila($this->obtenerSQLCabeceraPuntoMuestra($idUmd), $arParams);

            $sql = "SELECT descripcion as abreviatura_dato_muestreo, 
                _md_1.valor_muestra as m_0, _md_2.valor_muestra as m_1, _md_3.valor_muestra as m_2, _md_4.valor_muestra as m_3, _md_5.valor_muestra as m_4
                    FROM dato_muestreo dm ";     
            
            $muestras = $this->consultarValor("SELECT fn_get_variable('numero_muestras_evaluacion')");

            for ($i=1; $i <= $muestras; $i++) { 
                $sql .= " LEFT JOIN muestra _md_".$i." ON _md_".$i.".id_dato_muestreo = dm.id_dato_muestreo AND _md_".$i.".numero_muestra = ".$i." AND ";
                $sql .= ' _md_'.$i.'.id_umd = :1 AND _md_'.$i.'.id_evaluacion = :0 AND _md_'.$i.'.numero_punto = :2'; 
            }
                
            $sql .= " WHERE dm.estado_mrcb= 1 ORDER BY dm.id_dato_muestreo";

            $listaDatosMuestreo = $this->consultarFilas($sql, $arParams);            

            foreach ($listaDatosMuestreo as $clave => $valor) {
                $sumatoria = 0;
                for ($i=0; $i < $muestras; $i++) {                     
                    $sumatoria += $valor["m_".$i] != null ? $valor["m_".$i] : 0;
                }

                $listaDatosMuestreo[$clave]["suma"] = $sumatoria;
            }
                        
            return array("rpt"=>true,"data"=>array("cabecera"=>$cabecera, "datos_muestra"=>$listaDatosMuestreo));
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
            throw $exc;
        }
    }
    
}

    