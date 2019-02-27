<?php 

/** Incluye PHPExcel */
require_once '../plugins/Classes/PHPExcel.php';
require_once '../datos/local_config_web.php';
// Crear nuevo objeto PHPExcel
require_once MODELO . "/".utf8_decode("Campaña").".clase.php";

    try {

		$objPHPExcel = new PHPExcel();    
		$objCampaña = new Campaña();

		$campañas = !isset($_GET["p_campanas"]) ? "[]" : "[".$_GET["p_campanas"]."]";
        $idCampañas = json_decode($campañas);

     	$cuerpo = $objCampaña->reporteResultadoRiesgos($idCampañas);

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
		$cadenaFecha = 'Reporte de '. !isset($_GET["p_campanas"]) ? 'Todos los años' : $_GET["p_campanas"];

		$ultimaColumna = "G";
		$objPHPExcel->getActiveSheet()
					->setCellValue('A1','Cayalti S.A.A.')
					->mergeCells('A2:'.$ultimaColumna.'2')
					->mergeCells('A3:'.$ultimaColumna.'3')
					->mergeCells('E1:'.$ultimaColumna.'1')
					->setCellValue('E1', 'Fecha: '.date('d-m-Y').' Hora: '.date('H:i:s'))
					->setCellValue('A2', 'REPORTE DE RESULTADO DE RIESGOS')
					->setCellValue('A3', $cadenaFecha);

		$objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($fechaHoraStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($fechaHoraStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A2:'.$ultimaColumna.'2')->applyFromArray($tituloStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A3:'.$ultimaColumna.'3')->applyFromArray($tituloStyle);
		/*Inicio tabla CABECERA: A7-D7*/
		$filaI = 4;
			$objPHPExcel->getActiveSheet()
					->setCellValue('A'.$filaI, 'FECHA')
					->setCellValue('B'.$filaI, 'N. EVALUACIÓN')
					->setCellValue('C'.$filaI, 'CAMPO')					
					->setCellValue('D'.$filaI, 'VARIEDAD')
					->setCellValue('E'.$filaI, 'AREA')
					->setCellValue('F'.$filaI, 'NIVEL INFESTACION (%)')
					->setCellValue('G'.$filaI, 'TIPO RIESGO');

			$objPHPExcel->getActiveSheet()->getStyle('E'.$filaI)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle('G'.$filaI)->getAlignment()->setWrapText(true);


			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);

			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(16);

			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);

			$filaI++;
			
			$colorEstado = ["ALTO"=>["B22222","FFFFFF"], "MEDIO"=>["FFD700","000000"], "BAJO"=>["008000","FFFFFF"]];

			foreach ($cuerpo as $key => $value) {
					$objPHPExcel->getActiveSheet()
						->setCellValue('A'.$filaI, $value["fecha"] )
						->setCellValue('B'.$filaI, $value["numero_evaluacion"] )
						->setCellValue('C'.$filaI, $value["nombre_campo"])
						->setCellValue('D'.$filaI, $value["variedad"])
						->setCellValue('E'.$filaI, $value["area"])
						->setCellValue('F'.$filaI, $value["nivel_infestacion"])
						->setCellValue('G'.$filaI, $value["tipo_riesgo"]);

					$objThisColor = $colorEstado[$value["tipo_riesgo"]];
					$objPHPExcel->getActiveSheet()->getStyle('G'.$filaI)->applyFromArray(
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
		$objPHPExcel->getActiveSheet()->setTitle('RESULTADO RIESGOS');
		 
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="resultado-campos-riesgos.xlsx"');
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


