<?php 

/** Incluye PHPExcel */
require_once '../plugins/Classes/PHPExcel.php';
require_once '../datos/local_config_web.php';
// Crear nuevo objeto PHPExcel
require_once MODELO . "/Evaluacion.clase.php";

  if (isset($_GET['p_fi']) && isset($_GET["p_ff"]))  {  
    try {

		$objPHPExcel = new PHPExcel();    
		$objEvaluacion = new Evaluacion();

		$campos = !isset($_GET["p_campos"]) ? "[]" : "[".$_GET["p_campos"]."]";
        $idCampos = json_decode($campos);
        $f0 = $_GET["p_fi"];
        $f1 = $_GET["p_ff"];

        //$todosLosCampos = $idCampos[0] == "0";
       	
     	$cuerpo = $objEvaluacion->reporteResultadoEvaluaciones($idCampos,$f0,$f1);
   //     $cuerpo = [];

		$objPHPExcel->setActiveSheetIndex(0);

		function _fechear($fecha){
			$ar = split("[/-]",$fecha);    
       		return $ar[2]."-".$ar[1]."-".$ar[0];
		}
/*
		$cellColor = function ($cell, $color) use (&$objPHPExcel) {
		   return $objPHPExcel->getStyle($cell);
		};
		*/

		$tituloStyle = array('font' => array('bold' => true,'size' => 15),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$subTituloStyle = array('font' => array('size' => 12),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
	    $fechaHoraStyle = array('font' => array('bold' => true, 'name' => 'Arial','size' => 8),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));
		$cadenaFecha = 'Reporte de';
		$cadenaFecha .= ($f1 == $f0) ? ' '._fechear($f0) : 'l '._fechear($f0).' al '._fechear($f1);

		$ultimaColumna = "T";
		$objPHPExcel->getActiveSheet()
					->setCellValue('A1','Cayalti S.A.A.')
					->mergeCells('A2:'.$ultimaColumna.'2')
					->mergeCells('A3:'.$ultimaColumna.'3')
					->mergeCells('S1:'.$ultimaColumna.'1')
					->setCellValue('S1', 'Fecha: '.date('d-m-Y').' Hora: '.date('H:i:s'))
					->setCellValue('A2', 'REPORTE DE RESULTADO DE EVALUACIONES')
					->setCellValue('A3', $cadenaFecha);

		$objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($fechaHoraStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($fechaHoraStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A2:'.$ultimaColumna.'2')->applyFromArray($tituloStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A3:'.$ultimaColumna.'3')->applyFromArray($tituloStyle);
		/*Inicio tabla CABECERA: A7-D7*/
		$filaI = 4;
			$objPHPExcel->getActiveSheet()
					->setCellValue('A'.$filaI, 'FECHA')
					->setCellValue('B'.$filaI, 'NÚMERO')
					->setCellValue('C'.$filaI, 'CAMPO')					
					->setCellValue('D'.$filaI, 'VARIEDAD')
					->setCellValue('E'.$filaI, 'MODULO / JIRON')
					->setCellValue('F'.$filaI, 'TURNO')
					->setCellValue('G'.$filaI, 'VALVULA / CUARTEL')
					->setCellValue('H'.$filaI, 'AREA')
					->setCellValue('I'.$filaI, 'INDICE POBLACIONAL')
					->setCellValue('J'.$filaI, 'NIVEL INFESTACIÓN')
					->setCellValue('K'.$filaI, 'INTENSIDAD DE DAÑO')
					->setCellValue('L'.$filaI, 'TIPO RIESGO')
					->setCellValue('M'.$filaI, 'N° TALLOS')
					->setCellValue('N'.$filaI, 'N° ENTRENUDOS TOTAL')
					->setCellValue('O'.$filaI, 'N° ENTRENUDOS DAÑADOS')
					->setCellValue('P'.$filaI, 'N° BARRENO - LARVAS')
					->setCellValue('Q'.$filaI, 'N° BARRENO - CRISÁLIDAS')
					->setCellValue('R'.$filaI, 'N° MOSCA - LARVAS PARÁSITO')
					->setCellValue('S'.$filaI, 'N° MOSCA - LARVAS')
					->setCellValue('T'.$filaI, 'N° MOSCA - PUPAS');

			$objPHPExcel->getActiveSheet()->getStyle('E'.$filaI)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle('G'.$filaI)->getAlignment()->setWrapText(true);


			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);

			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(16);

			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(19);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(19);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(19);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(13);

			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(18);

			$filaI++;
			
			$colorEstado = ["ALTO"=>["B22222","FFFFFF"], "MEDIO"=>["FFD700","000000"], "BAJO"=>["008000","FFFFFF"]];



			foreach ($cuerpo as $key => $value) {
					$objPHPExcel->getActiveSheet()
						->setCellValue('A'.$filaI, $value["fecha"] )
						->setCellValue('B'.$filaI, $value["numero_evaluacion"] )
						->setCellValue('C'.$filaI, $value["nombre_campo"])
						->setCellValue('D'.$filaI, $value["variedad"])
						->setCellValue('E'.$filaI, $value["modulo_jiron"])
						->setCellValue('F'.$filaI, $value["id_tipo_riego"] == "1" ? $value["turno"] : "")
						->setCellValue('G'.$filaI, $value["valvula_cuartel"])
						->setCellValue('H'.$filaI, $value["area"])
						->setCellValue('I'.$filaI, $value["indice_poblacion_calculada"])
						->setCellValue('J'.$filaI, $value["nivel_infestacion"])
						->setCellValue('K'.$filaI, $value["intensidad_daño"])
						->setCellValue('L'.$filaI, $value["tipo_riesgo"])
						->setCellValue('M'.$filaI, $value["n_tallos"])
						->setCellValue('N'.$filaI, $value["n_entrenudos"])
						->setCellValue('O'.$filaI, $value["n_entrenudos_dañados"])
						->setCellValue('P'.$filaI, $value["n_barreno_larva"])
						->setCellValue('Q'.$filaI, $value["n_barreno_crisalida"])
						->setCellValue('R'.$filaI, $value["n_mosca_larva_parasito"])
						->setCellValue('S'.$filaI, $value["n_mosca_larva"])
						->setCellValue('T'.$filaI, $value["n_mosca_pupa"]);

					$objThisColor = $colorEstado[$value["tipo_riesgo"]];
					$objPHPExcel->getActiveSheet()->getStyle('L'.$filaI)->applyFromArray(
					    array(
					        'fill' => array(
					            'type' => PHPExcel_Style_Fill::FILL_SOLID,
					            'color' => array('rgb' => $objThisColor[0])
					        ),
					        'font' => array(
					        	"bold"=>true,
					        	'color' => array('rgb' => $objThisColor[1])),
					        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
					    )
					);

					$filaI++;
			}			

		$headerTablaStyle = array('font' => array('bold' => true,'size'=>9),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.$ultimaColumna.'4')->applyFromArray($headerTablaStyle);
		$objPHPExcel->getActiveSheet()->setTitle('RESULTADO EVALUACIONES');
		 
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="resultado-evaluaciones.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;

    } catch (Exception $exc) {
        json_encode(["state"=>500,"msj"=>$exc->getMessage()]);
    }   
  } else {
  	json_encode(["state"=>400,"msj"=>"Faltan parámetros"]);
  }
    

