<?php 

/** Incluye PHPExcel */
require_once '../plugin/Classes/PHPExcel.php';
require_once '../datos/local_config_web.php';
require_once MODELO. '/util/Funciones.php';           
require_once MODELO . '/Asistencia.clase.php';

function indiceALetra ($indice){
   	$colText = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   	$colTextLimite = strlen($colText);
	$extraColText = ['AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO'];
	
	return ($indice >= $colTextLimite) ? $extraColText[$indice - $colTextLimite] : $colText[$indice];
}

	if (isset($_GET['p_f']) && isset($_GET['p_ipa']) && isset($_GET['p_pa'])) {  
   	 try {
			// Crear nuevo objeto PHPExcel
			$f = $_GET['p_f'];
			$idPuntoAcceso  = $_GET['p_ipa'];
			$puntoAcceso = $_GET['p_pa'];

   	 		$CREADOR = "CompuCodex";
			$NOMBRE_EXCEL = 'reporte-asistencia-puntoacceso-'.date('dmYHis').'.xlsx';
			$TITULO = "Reporte Asistencias por Punto de Acceso";
			$EMPRESA = "CAYALTI EAI";
			$MODIFICADO_POR = $EMPRESA;
			$DIA = date('d-m-Y');
			$HORA = date('H:i:s');

			$objPHPExcel = new PHPExcel();    
			$objReporteador = new Asistencia();

			$objReporte = $objReporteador->listarFechasPuntoAccesoDetalle($f, $idPuntoAcceso);

			if ($objReporte["rpt"] == false){
				print($objReporte["msj"]);
				exit;
			}

			$dataReporte = $objReporte["data"];

	        //$rango_fecha = $dataReporte["rango_fecha_desc"];
	        //$cuerpo = $dataReporte["data_egresos"];
			$objPHPExcel->getProperties()->setCreator($CREADOR)
										 ->setTitle($TITULO)
										 ->setSubject($TITULO)
										 ->setLastModifiedBy($MODIFICADO_POR);

			$tituloEstilo = array('font' => array('bold' => true,'size' => 20),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
			
			$cabeceraEstilo =  array('font' => array('bold'=>true,'size' => 8),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
																										'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
			
			$fechaEstilo = array('font' => array('bold'=>true,'size' => 13),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT));

			$fechaHoraEstilo = array('font' => array('bold' => true, 'name' => 'Arial','size' => 8),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));

			$empresaEstilo = array('font' => array('bold' => true, 'name' => 'Arial','size' => 8),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT));
			

			$rango_fecha_desc = Funciones::fechear($f);
			
			/*	INICIO SHEET 0 */
			$objPHPExcel->setActiveSheetIndex(0);
			$actualSheet = $objPHPExcel->getActiveSheet();
							 
			//Inicio tabla CABECERA: A3-D3
			$filaI = 6;
			$columnas = [
				//nombre coklumna => ancho
				'DNI'=>12,
		 		'APELLIDOS Y NOMBRES'=>40,
		 		'PLANILLA'=>40,
		 		'TURNO'=>15,
		 		'INGRESO'=>15,
		 		'SALIDA'=>15,
		 		'ID RESPONSABLE'=>13,
		 		'RESPONSABLE'=>40
			];

			$i = 0;
			foreach ($columnas as $nombreColumna => $anchoColumna) {
				$letra = indiceALetra($i);
				$actualSheet->setCellValue($letra.$filaI, $nombreColumna);
				$actualSheet->getColumnDimension($letra)->setWidth($anchoColumna);
				$i++;
			}

			$rangoColumnas = 'A'.$filaI.':'.$letra.$filaI;
			$celdaFilaFinal = $letra;

			/*CABECERA*/
			$actualSheet->setCellValue('A1',$EMPRESA)
						->mergeCells('A1:B1')
						->mergeCells('A2:'.$celdaFilaFinal.'2')
						->setCellValue($celdaFilaFinal.'1', 'Fecha: '.$DIA.' Hora: '.$HORA)
						->setCellValue('A2', "REPORTE DE ASISTENCIAS")
						->setCellValue('A3', "FECHA REPORTE: ".$rango_fecha_desc)
						->mergeCells('A3:'.$celdaFilaFinal.'3')
						->setCellValue('A4', "PUNTO DE ACCESO: ".$idPuntoAcceso." - ".$puntoAcceso);
					//	->mergeCells('B3:'.$celdaFilaFinal.'3');

			$actualSheet->getStyle('A1')->applyFromArray($empresaEstilo);
			$actualSheet->getStyle('A2:'.$celdaFilaFinal.'2')->applyFromArray($tituloEstilo);
			$actualSheet->getStyle('A3')->applyFromArray($fechaEstilo);
			$actualSheet->getStyle('A4')->applyFromArray($fechaEstilo);

			$actualSheet->getStyle($celdaFilaFinal.'1')->applyFromArray($fechaHoraEstilo);
			/*CABECERA*/
			$actualSheet->getStyle($rangoColumnas)->applyFromArray($cabeceraEstilo);
			$actualSheet->getStyle($rangoColumnas)->applyFromArray($cabeceraEstilo);

			$colorRiesgo= ["ALTA"=>["B22222","FFFFFF"], "LEVE"=>["FFD700","000000"],"ERROR"=>["FFFFFF","008000"]];

			$filaInit = $filaI + 1;
			$MINUTOS_TARDANZA_LEVE = 5;
			$MINUTOS_TARDANZA_ALTA = 15;

			if (count($dataReporte) > 0){
				foreach ($dataReporte as $_ => $value) {
					/*INIT */
					$filaI++;
					$indice = 0;
					$actualSheet
								->setCellValue(indiceALetra($indice++).$filaI, $value["dni_asistencia"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["apellidos_nombres"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["planilla"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["turno"]);

					$ingreso = indiceALetra($indice++);

					if ($value["nivel_tardanza"] > $MINUTOS_TARDANZA_LEVE){

						if ($value["nivel_tardanza"] > $MINUTOS_TARDANZA_ALTA){
							$riesgo = "ALTA";
						} else {
							$riesgo = "LEVE";
						}

						$objThisColor = $colorRiesgo[$riesgo];
						$objPHPExcel->getActiveSheet()->getStyle($ingreso.$filaI)->applyFromArray(
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
					}

					$actualSheet			
								->setCellValue($ingreso.$filaI, $value["ingreso"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["salida"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["idresponsable"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["responsable"]);
					/*
					$objThisColor = $colorRiesgo[$riesgo];
					
					$objPHPExcel->getActiveSheet()->getStyle($indiceIndiceInfestacion.$filaI)->applyFromArray(
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
					*/
				}

				//$actualSheet->getStyle($idPuntoAcceso.$filaInit.':'.$idPuntoAcceso.$filaI)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
			}

			$actualSheet->setTitle('REPORTE DE ASISTENCIAS');
			/**FIN SHEET 0. */
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="'.$NOMBRE_EXCEL.'"');
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
	    	print($exc->getMessage());
	    }   
	} else {
		print("Faltan parametros en el reporte");
	}

