 <?php

require_once '../datos/Conexion.clase.php';

class MatrizAhp extends Conexion {


  private $idCriterioAhpUno;
  private $idCriterioAhpDos;
  private $valorCriterio;

  public function getIdCriterioAhpUno()
  {
      return $this->idCriterioAhpUno;
  }
  
  
  public function setIdCriterioAhpUno($idCriterioAhpUno)
  {
      $this->idCriterioAhpUno = $idCriterioAhpUno;
      return $this;
  }

  public function getIdCriterioAhpDos()
  {
      return $this->idCriterioAhpDos;
  }
  
  
  public function setIdCriterioAhpDos($idCriterioAhpDos)
  {
      $this->idCriterioAhpDos = $idCriterioAhpDos;
      return $this;
  }

  public function getValorCriterio()
  {
      return $this->valorCriterio;
  }
  
  
  public function setValorCriterio($valorCriterio)
  {
      $this->valorCriterio = $valorCriterio;
      return $this;
  }


  public function listarMatrizPrincipal()
  {
      try {

          $sql = "SELECT id_criterio_ahp, nombre_criterio, estado_activacion FROM criterio_ahp ORDER BY 1";
          $arCriterios = $this->consultarFilas($sql);

          $matrizCriteriosValores = array();
          $sqlL = "SELECT id_criterio_ahp_dos as id_criterio_ahp_der, valor_criterio,
                (SELECT estado_activacion FROM criterio_ahp WHERE id_criterio_ahp = ma.id_criterio_ahp_dos) as activacion_dos
                FROM matriz_ahp ma
                WHERE id_criterio_ahp_uno = :0 
                UNION SELECT :0, 1.000, :1";
          foreach ($arCriterios as $key => $value) {
            $tmpArreglo = $this->consultarFilas($sqlL, [$value["id_criterio_ahp"],$value["estado_activacion"]]);          
            $objCriterioValores = array("id_criterio_ahp" =>$value["id_criterio_ahp"],
                                        "nombre_criterio" => $value["nombre_criterio"],
                                        "estado_activacion" => $value["estado_activacion"],
                                          "valores"=>$tmpArreglo);
            array_push($matrizCriteriosValores,$objCriterioValores);
          }

          return array("rpt"=>true, "data"=>array("criterios"=>$arCriterios, "matrizCriteriosValores"=>$matrizCriteriosValores));

        } catch (Exception $exc) {
          return array("rpt"=>false,"msj"=>$exc);
          throw $exc;
        }
  }

  public function guardarValoresMatrizPrincipal()
  {
    # code...
  }

  
}
