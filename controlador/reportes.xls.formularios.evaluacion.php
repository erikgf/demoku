<?php 

/** Incluye PHPExcel */
require_once '../plugin/Classes/PHPExcel.php';
require_once '../datos/local_config_web.php';
require_once MODELO. '/util/Funciones.php';           
require_once MODELO . '/ReporteadorFormulario.clase.php';

function indiceALetra ($indice){
   	$colText = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   	$colTextLimite = strlen($colText);
	$extraColText = ['AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO'];
	
	return ($indice >= $colTextLimite) ? $extraColText[$indice - $colTextLimite] : $colText[$indice];
}

	if (isset($_GET['p_fi']) && isset($_GET['p_ff'])) {  
   	 try {
			// Crear nuevo objeto PHPExcel
			$fi = $_GET['p_fi'];
	        $ff = $_GET['p_ff'];

   	 		$CREADOR = "CompuCodex";
			$NOMBRE_EXCEL = 'reporte-formularios-evaluacion-'.date('dmYHis').'.xlsx';
			$TITULO = "Reporte Formularios Evaluación";
			$EMPRESA = "CAYALTI EAI";
			$MODIFICADO_POR = $EMPRESA;
			$DIA = date('d-m-Y');
			$HORA = date('H:i:s');

			$objPHPExcel = new PHPExcel();    
			$objReporteador = new ReporteadorFormulario();

			$objReporte = $objReporteador->obtenerReporte($fi,$ff);

			if ($objReporte["rpt"] == false){
				print($objReporte["msj"]);
				exit;
			}

			$dataReporte = $objReporte["data"];
	       	$dataSheetDiatraea = $dataReporte["diatraea"];
	       	$dataSheetDiatraeaResumen = $dataReporte["diatraea_resumen"];
			$dataSheetCarbon = $dataReporte["carbon"];
	       	$dataSheetCarbonResumen = $dataReporte["carbon_resumen"];
	       	/*
	       	$dataSheetElasmopalpus = $dataReporte["data_elasmopalpus"];
	       	$dataSheetCarbon = $dataReporte["data_carbon"];
	       	$dataSheetMetamasius = $dataReporte["data_metamasius"];
	       	$dataSheetRoya = $dataReporte["data_roya"];
	       	*/

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
			
			$celdaFilaFinal = "AB";

			if ($fi == $ff){
				$rango_fecha_desc = Funciones::fechear($fi);
			} else {
				$rango_fecha_desc = "Del ".Funciones::fechear($fi)." al ".Funciones::fechear($ff);
			}

			/*	INICIO SHEET 0 - DIATRAEA */

			$objPHPExcel->setActiveSheetIndex(0);
			$actualSheet = $objPHPExcel->getActiveSheet();
			$actualSheet->setCellValue('A1',$EMPRESA)
						->mergeCells('A1:B1')
						->mergeCells('A2:'.$celdaFilaFinal.'2')
						->setCellValue($celdaFilaFinal.'1', 'Fecha: '.$DIA.' Hora: '.$HORA)
						->setCellValue('A2', "REPORTE DE EVALUACIÓN DIATRAEA")
						->setCellValue('A3', "RANGO FECHAS REPORTE: ".$rango_fecha_desc)
						->mergeCells('A3:H3');

			$actualSheet->getStyle('A1')->applyFromArray($empresaEstilo);
			$actualSheet->getStyle('A2:'.$celdaFilaFinal.'2')->applyFromArray($tituloEstilo);
			$actualSheet->getStyle('A3')->applyFromArray($fechaEstilo);
			$actualSheet->getStyle($celdaFilaFinal.'1')->applyFromArray($fechaHoraEstilo);
							 
			//Inicio tabla CABECERA: A3-D3
			$filaI = 5;

			$columnas = [
				//nombre coklumna => ancho
				'CAMPO'=>25,
		 		'INICIO CAMPAÑA'=>13,
		 		'FECHA EVALUACIÓN'=>14,
		 		'N° CAMPAÑA'=>11,
		 		'N° EVALUACIÓN'=>11,
		 		'MÓDULO/JIRÓN'=>11,
		 		'TURNO'=>11,
		 		'VÁLVULA/CUARTEL'=>14,
		 		'TALLOS EVALUADOS'=>14,
		 		'TALLOS INFESTADOS'=>14,
		 		'% INFESTACIÓN'=>14,
		 		'ENTRENUDOS EVALUADOS'=>18,
		 		'ENTRENUDOS INFESTADOS'=>18,
		 		'% INTENSIDAD DAÑO'=>18,
		 		'LARVAS ESTADO 1'=>13,
		 		'LARVAS ESTADO 2'=>13,
		 		'LARVAS ESTADO 3'=>13,
		 		'LARVAS ESTADO 4'=>13,
		 		'LARVAS ESTADO 5'=>13,
		 		'LARVAS ESTADO 6'=>13,
		 		'ÍNDICE POBLACIÓN'=>16,
		 		'CRISÁLIDAS'=>12,
		 		'LARVAS PARASITADAS'=>16,
		 		'BILLAEA LARVAS'=>13,
		 		'BILLAEA PUPAS'=>13,
		 		'% PARASITISMO'=>13,
		 		'EVALUADOR'=>33
			];

			$i = 0;
			foreach ($columnas as $nombreColumna => $anchoColumna) {
				$letra = indiceALetra($i);
				$actualSheet->setCellValue($letra.$filaI, $nombreColumna);
				$actualSheet->getColumnDimension($letra)->setWidth($anchoColumna);
				$i++;
			}

			$rangoColumnas = 'A'.$filaI.':'.$letra.$filaI;

			$actualSheet->getStyle($rangoColumnas)->applyFromArray($cabeceraEstilo);
			$actualSheet->getStyle($rangoColumnas)->applyFromArray($cabeceraEstilo);


			$colorRiesgo= ["ALTO"=>["B22222","FFFFFF"], "MEDIO"=>["FFD700","000000"], "BAJO"=>["008000","FFFFFF"]];

			$filaInit = $filaI + 1;

			if (count($dataSheetDiatraea) > 0){
				foreach ($dataSheetDiatraea as $_ => $value) {
					/*INIT */
					$filaI++;
					$indice = 0;
					$actualSheet
								->setCellValue(indiceALetra($indice++).$filaI, $value["nombre_campo"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["fecha_inicio_campaña"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["fecha_evaluacion"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["numero_campaña"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["numero_evaluacion"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["numero_nivel_1"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["numero_nivel_2"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["numero_nivel_3"]);

					$indiceTallos = indiceALetra($indice++);
					$indiceTallosInfestados = indiceALetra($indice++);	
					$porcentajeInfestacion = indiceALetra($indice++);	

					$actualSheet			
								->setCellValue($indiceTallos.$filaI, $value["dia_tallos"])
								->setCellValue($indiceTallosInfestados.$filaI, $value["dia_tallos_infestados"])
								->setCellValue($porcentajeInfestacion.$filaI,'='.$indiceTallosInfestados.$filaI.'/'.$indiceTallos.$filaI.'*100');

					$indiceEntrenudos = indiceALetra($indice++);			
					$indiceEntrenudosInfestados = indiceALetra($indice++);
					$intensidadDaño = indiceALetra($indice++);

					$actualSheet
								->setCellValue($indiceEntrenudos.$filaI, $value["dia_entrenudos"])
								->setCellValue($indiceEntrenudosInfestados.$filaI, $value["dia_entrenudos_infestados"])
								->setCellValue($intensidadDaño.$filaI,'='.$indiceEntrenudosInfestados.$filaI.'/'.$indiceEntrenudos.$filaI.'*100');

					$inicioLarvas = indiceALetra($indice++);
					$actualSheet	
								->setCellValue($inicioLarvas.$filaI, $value["dia_larvas_estadio_1"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["dia_larvas_estadio_2"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["dia_larvas_estadio_3"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["dia_larvas_estadio_4"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["dia_larvas_estadio_5"]);

					$finLarvas = indiceALetra($indice++);
					$actualSheet
								->setCellValue($finLarvas.$filaI, $value["dia_larvas_estadio_6"]);

					$indiceIndiceInfestacion = indiceALetra($indice++);
					$actualSheet	
								->setCellValue($indiceIndiceInfestacion.$filaI, '=SUM('.$inicioLarvas.$filaI.':'.$finLarvas.$filaI.')/'.$indiceTallos.$filaI);
					
					$indiceLarvas = $value["dia_larvas_estadio_1"] + $value["dia_larvas_estadio_2"] + $value["dia_larvas_estadio_3"] 
									+  $value["dia_larvas_estadio_4"] +  $value["dia_larvas_estadio_5"] + $value["dia_larvas_estadio_6"];

					$indiceLarvas = $indiceLarvas / $value["dia_tallos"];
					$riesgo = "ALTO";

					if ($indiceLarvas <= 0.05){
						$riesgo = "BAJO";
					} else if($indiceLarvas >= 0.06 && $indiceLarvas <= 0.15){
						$riesgo = "MEDIO";
					}

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
					$indiceCrisalidas = indiceALetra($indice++);
					$indiceLarvasParasitadas = indiceALetra($indice++);
					$indiceBillaeaLarvas = indiceALetra($indice++);
					$indiceBillaeaPupas = indiceALetra($indice++);

					$actualSheet			
								->setCellValue($indiceCrisalidas.$filaI, $value["dia_crisalidas"])
								->setCellValue($indiceLarvasParasitadas.$filaI, $value["dia_larvas_parasitadas"])
								->setCellValue($indiceBillaeaLarvas.$filaI, $value["dia_billaea_larvas"])
								->setCellValue($indiceBillaeaPupas.$filaI, $value["dia_billaea_pupas"]);

					$porcentajeParasitismo = indiceALetra($indice++);
					$formulaNumerador = 'SUM('.$indiceLarvasParasitadas.$filaI.':'.$indiceBillaeaPupas.$filaI.')';
					$formulaDenominador = 'SUM('.$inicioLarvas.$filaI.':'.$finLarvas.$filaI.','.$indiceCrisalidas.$filaI.':'.$indiceBillaeaPupas.$filaI.')';
					$formulaParasitismo = "=IF(".$formulaDenominador."=0,0,".$formulaNumerador."/".$formulaDenominador." * 100)";

					$actualSheet				
								->setCellValue($porcentajeParasitismo.$filaI, $formulaParasitismo)
								->setCellValue(indiceALetra($indice++).$filaI, $value["colaborador"]);
				}

				$actualSheet->getStyle($porcentajeParasitismo.$filaInit.':'.$porcentajeParasitismo.$filaI)->getNumberFormat()->setFormatCode('#,##0.00');	
				$actualSheet->getStyle($intensidadDaño.$filaInit.':'.$intensidadDaño.$filaI)->getNumberFormat()->setFormatCode('#,##0.00');	
				$actualSheet->getStyle($indiceIndiceInfestacion.$filaInit.':'.$indiceIndiceInfestacion.$filaI)->getNumberFormat()->setFormatCode('#,##0.0000');	
				$actualSheet->getStyle($porcentajeInfestacion.$filaInit.':'.$porcentajeInfestacion.$filaI)->getNumberFormat()->setFormatCode('#,##0.00');	
			}
			//$actualSheet->getStyle('D'.$filaInit.':F'.$filaI)->applyFromArray($celdaNegativaEstilo);
			$actualSheet->setTitle('REPORTE DIATRAEA');

			/**FIN SHEET 0. */


			/*INICIO SHEET 1 DATRAEA RESUMEN*/
			$celdaFilaFinal = "N";
			$actualSheet = $objPHPExcel->createSheet();

			$actualSheet->setCellValue('A1',$EMPRESA)
						->mergeCells('A1:B1')
						->mergeCells('A2:'.$celdaFilaFinal.'2')
						->setCellValue($celdaFilaFinal.'1', 'Fecha: '.$DIA.' Hora: '.$HORA)
						->setCellValue('A2', "REPORTE DE EVALUACIÓN DIATRAEA RESUMEN")
						->setCellValue('A3', "RANGO FECHAS REPORTE: ".$rango_fecha_desc)
						->mergeCells('A3:H3');

			$actualSheet->getStyle('A1')->applyFromArray($empresaEstilo);
			$actualSheet->getStyle('A2:'.$celdaFilaFinal.'2')->applyFromArray($tituloEstilo);
			$actualSheet->getStyle('A3')->applyFromArray($fechaEstilo);
			$actualSheet->getStyle($celdaFilaFinal.'1')->applyFromArray($fechaHoraEstilo);
							 
			//Inicio tabla CABECERA: A3-D3
			$filaI = 5;

			$columnas = [
				//nombre coklumna => ancho
				'CAMPO'=>25,
		 		'INICIO CAMPAÑA'=>13,
		 		'FECHA EVALUACIÓN'=>14,
		 		'N° CAMPAÑA'=>11,
		 		'N° EVALUACIÓN'=>11,
		 		'TALLOS EVALUADOS'=>14,
		 		'TALLOS INFESTADOS'=>14,
		 		'% INFESTACIÓN'=>14,
		 		'ENTRENUDOS EVALUADOS'=>18,
		 		'ENTRENUDOS INFESTADOS'=>18,
		 		'% INTENSIDAD DAÑO'=>18,
		 		'TOTAL LARVAS'=>13,
		 		'ÍNDICE POBLACIÓN'=>16,
		 		'% PARASITISMO'=>13
			];

			$i = 0;
			foreach ($columnas as $nombreColumna => $anchoColumna) {
				$letra = indiceALetra($i);
				$actualSheet->setCellValue($letra.$filaI, $nombreColumna);
				$actualSheet->getColumnDimension($letra)->setWidth($anchoColumna);
				$i++;
			}

			$rangoColumnas = 'A'.$filaI.':'.$letra.$filaI;

			$actualSheet->getStyle($rangoColumnas)->applyFromArray($cabeceraEstilo);
			$actualSheet->getStyle($rangoColumnas)->applyFromArray($cabeceraEstilo);

			$colorRiesgo= ["ALTO"=>["B22222","FFFFFF"], "MEDIO"=>["FFD700","000000"], "BAJO"=>["008000","FFFFFF"]];

			$filaInit = $filaI + 1;

			if (count($dataSheetDiatraeaResumen) > 0){
				foreach ($dataSheetDiatraeaResumen as $_ => $value) {
					/*INIT */
					$filaI++;
					$indice = 0;
					$actualSheet
								->setCellValue(indiceALetra($indice++).$filaI, $value["nombre_campo"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["fecha_inicio_campaña"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["fecha_evaluacion"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["numero_campaña"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["numero_evaluacion"]);

					$indiceTallos = indiceALetra($indice++);
					$indiceTallosInfestados = indiceALetra($indice++);	
					$porcentajeInfestacion = indiceALetra($indice++);	

					$actualSheet			
								->setCellValue($indiceTallos.$filaI, $value["dia_tallos"])
								->setCellValue($indiceTallosInfestados.$filaI, $value["dia_tallos_infestados"])
								->setCellValue($porcentajeInfestacion.$filaI,'='.$indiceTallosInfestados.$filaI.'/'.$indiceTallos.$filaI.'*100');

					$indiceEntrenudos = indiceALetra($indice++);			
					$indiceEntrenudosInfestados = indiceALetra($indice++);
					$intensidadDaño = indiceALetra($indice++);

					$actualSheet
								->setCellValue($indiceEntrenudos.$filaI, $value["dia_entrenudos"])
								->setCellValue($indiceEntrenudosInfestados.$filaI, $value["dia_entrenudos_infestados"])
								->setCellValue($intensidadDaño.$filaI,'='.$indiceEntrenudosInfestados.$filaI.'/'.$indiceEntrenudos.$filaI.'*100');

					$totalLarvasSuma = 	$value["larvas_totales"];

					$totalLarvas = indiceALetra($indice++);
					$actualSheet	
								->setCellValue($totalLarvas.$filaI, $totalLarvasSuma);

					$indiceIndiceInfestacion = indiceALetra($indice++);
					$actualSheet	
								->setCellValue($indiceIndiceInfestacion.$filaI, '='.$totalLarvas.$filaI.'/'.$indiceTallos.$filaI);
					
					$indiceLarvas = $totalLarvasSuma / $value["dia_tallos"];
					$riesgo = "ALTO";

					if ($indiceLarvas <= 0.05){
						$riesgo = "BAJO";
					} else if($indiceLarvas >= 0.06 && $indiceLarvas <= 0.15){
						$riesgo = "MEDIO";
					}

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

					$porcentajeParasitismo = indiceALetra($indice++);

					$numerador =  $value["dia_larvas_parasitadas"] + $value["dia_billaea_larvas"] + $value["dia_billaea_pupas"];
					$formulaDenominador = 'SUM('.$totalLarvas.$filaI.','.($numerador + $value["dia_crisalidas"]).')';
					$formulaParasitismo = "=IF(".$formulaDenominador."=0,0,".$numerador."/".$formulaDenominador." * 100)";

					$actualSheet				
								->setCellValue($porcentajeParasitismo.$filaI, $formulaParasitismo);
				}

				$actualSheet->getStyle($porcentajeParasitismo.$filaInit.':'.$porcentajeParasitismo.$filaI)->getNumberFormat()->setFormatCode('#,##0.00');	
				$actualSheet->getStyle($intensidadDaño.$filaInit.':'.$intensidadDaño.$filaI)->getNumberFormat()->setFormatCode('#,##0.00');	
				$actualSheet->getStyle($indiceIndiceInfestacion.$filaInit.':'.$indiceIndiceInfestacion.$filaI)->getNumberFormat()->setFormatCode('#,##0.0000');	
				$actualSheet->getStyle($porcentajeInfestacion.$filaInit.':'.$porcentajeInfestacion.$filaI)->getNumberFormat()->setFormatCode('#,##0.00');	
			}
			//$actualSheet->getStyle('D'.$filaInit.':F'.$filaI)->applyFromArray($celdaNegativaEstilo);
			$actualSheet->setTitle('REPORTE DIATRAEA RESUMEN');

			/*FIN SHEET 1 : DIATRAEA RESUMEN*/


			/*INICIO SHEET 2 CARBON*/
			$celdaFilaFinal = "N";
			$actualSheet = $objPHPExcel->createSheet();

			$actualSheet->setCellValue('A1',$EMPRESA)
						->mergeCells('A1:B1')
						->mergeCells('A2:'.$celdaFilaFinal.'2')
						->setCellValue($celdaFilaFinal.'1', 'Fecha: '.$DIA.' Hora: '.$HORA)
						->setCellValue('A2', "REPORTE DE EVALUACIÓN CARBÓN")
						->setCellValue('A3', "RANGO FECHAS REPORTE: ".$rango_fecha_desc)
						->mergeCells('A3:H3');

			$actualSheet->getStyle('A1')->applyFromArray($empresaEstilo);
			$actualSheet->getStyle('A2:'.$celdaFilaFinal.'2')->applyFromArray($tituloEstilo);
			$actualSheet->getStyle('A3')->applyFromArray($fechaEstilo);
			$actualSheet->getStyle($celdaFilaFinal.'1')->applyFromArray($fechaHoraEstilo);
							 
			//Inicio tabla CABECERA: A3-D3
			$filaI = 5;

			$columnas = [
				//nombre coklumna => ancho
				'CAMPO'=>25,
		 		'INICIO CAMPAÑA'=>13,
		 		'FECHA EVALUACIÓN'=>14,
		 		'N° CAMPAÑA'=>11,
		 		'N° EVALUACIÓN'=>11,
		 		'MÓDULO/JIRÓN'=>11,
		 		'TURNO'=>11,
		 		'VÁLVULA/CUARTEL'=>14,
		 		'ÁREA (ha)'=>14,
		 		'N° CEPAS'=>14,
		 		'N° LATIGOS'=>14,
		 		'LATIGOS POR CEPA'=>14,
		 		'LATIGOS POR HA'=>18,
				'EVALUADOR'=>33
			];

			$i = 0;
			foreach ($columnas as $nombreColumna => $anchoColumna) {
				$letra = indiceALetra($i);
				$actualSheet->setCellValue($letra.$filaI, $nombreColumna);
				$actualSheet->getColumnDimension($letra)->setWidth($anchoColumna);
				$i++;
			}

			$rangoColumnas = 'A'.$filaI.':'.$letra.$filaI;

			$actualSheet->getStyle($rangoColumnas)->applyFromArray($cabeceraEstilo);
			$actualSheet->getStyle($rangoColumnas)->applyFromArray($cabeceraEstilo);

			$filaInit = $filaI + 1;

			if (count($dataSheetCarbon) > 0){
				foreach ($dataSheetCarbon as $_ => $value) {
					/*INIT */
					$filaI++;
					$indice = 0;
					$actualSheet
								->setCellValue(indiceALetra($indice++).$filaI, $value["nombre_campo"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["fecha_inicio_campaña"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["fecha_evaluacion"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["numero_campaña"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["numero_evaluacion"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["numero_nivel_1"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["numero_nivel_2"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["numero_nivel_3"]);

					$area = indiceALetra($indice++);
					$indiceCepas = indiceALetra($indice++);
					$indiceLatigos = indiceALetra($indice++);	
					$indicelatigosCepa = indiceALetra($indice++);	
					$indicelatigosHa = indiceALetra($indice++);	

					$actualSheet			
								->setCellValue($area.$filaI, $value["area"])
								->setCellValue($indiceCepas.$filaI, $value["car_tallos"])
								->setCellValue($indiceLatigos.$filaI, $value["car_tallos_latigo"])
								->setCellValue($indicelatigosCepa.$filaI,'='.$indiceLatigos.$filaI.'/'.$indiceCepas.$filaI)
								->setCellValue($indicelatigosHa.$filaI,'='.$indiceLatigos.$filaI.'/'.$area.$filaI)
								->setCellValue(indiceALetra($indice++).$filaI, $value["colaborador"]);
					
				}

				$actualSheet->getStyle($indiceLatigos.$filaInit.':'.$indiceLatigos.$filaI)->getNumberFormat()->setFormatCode('#,##0.00');	
				$actualSheet->getStyle($indicelatigosHa.$filaInit.':'.$indicelatigosHa.$filaI)->getNumberFormat()->setFormatCode('#,##0.00');	
			}
			//$actualSheet->getStyle('D'.$filaInit.':F'.$filaI)->applyFromArray($celdaNegativaEstilo);
			$actualSheet->setTitle('REPORTE CARBON');

			/*DATA SHEET 2 : REPORTE CARBON*/

			/*INICIO SHEET 2 CARBON*/
			$celdaFilaFinal = "J";
			$actualSheet = $objPHPExcel->createSheet();

			$actualSheet->setCellValue('A1',$EMPRESA)
						->mergeCells('A1:B1')
						->mergeCells('A2:'.$celdaFilaFinal.'2')
						->setCellValue($celdaFilaFinal.'1', 'Fecha: '.$DIA.' Hora: '.$HORA)
						->setCellValue('A2', "REPORTE DE EVALUACIÓN CARBÓN RESUMEN")
						->setCellValue('A3', "RANGO FECHAS REPORTE: ".$rango_fecha_desc)
						->mergeCells('A3:H3');

			$actualSheet->getStyle('A1')->applyFromArray($empresaEstilo);
			$actualSheet->getStyle('A2:'.$celdaFilaFinal.'2')->applyFromArray($tituloEstilo);
			$actualSheet->getStyle('A3')->applyFromArray($fechaEstilo);
			$actualSheet->getStyle($celdaFilaFinal.'1')->applyFromArray($fechaHoraEstilo);
							 
			//Inicio tabla CABECERA: A3-D3
			$filaI = 5;

			$columnas = [
				//nombre coklumna => ancho
				'CAMPO'=>25,
		 		'INICIO CAMPAÑA'=>13,
		 		'FECHA EVALUACIÓN'=>14,
		 		'N° CAMPAÑA'=>11,
		 		'N° EVALUACIÓN'=>11,
		 		'ÁREA (ha)'=>14,
		 		'N° CEPAS'=>14,
		 		'N° LATIGOS'=>14,
		 		'LATIGOS POR CEPA'=>14,
		 		'LATIGOS POR HA'=>18
			];

			$i = 0;
			foreach ($columnas as $nombreColumna => $anchoColumna) {
				$letra = indiceALetra($i);
				$actualSheet->setCellValue($letra.$filaI, $nombreColumna);
				$actualSheet->getColumnDimension($letra)->setWidth($anchoColumna);
				$i++;
			}

			$rangoColumnas = 'A'.$filaI.':'.$letra.$filaI;

			$actualSheet->getStyle($rangoColumnas)->applyFromArray($cabeceraEstilo);
			$actualSheet->getStyle($rangoColumnas)->applyFromArray($cabeceraEstilo);

			$filaInit = $filaI + 1;

			if (count($dataSheetCarbon) > 0){
				foreach ($dataSheetCarbon as $_ => $value) {
					/*INIT */
					$filaI++;
					$indice = 0;
					$actualSheet
								->setCellValue(indiceALetra($indice++).$filaI, $value["nombre_campo"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["fecha_inicio_campaña"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["fecha_evaluacion"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["numero_campaña"])
								->setCellValue(indiceALetra($indice++).$filaI, $value["numero_evaluacion"]);

					$area = indiceALetra($indice++);
					$indiceCepas = indiceALetra($indice++);
					$indiceLatigos = indiceALetra($indice++);	
					$indicelatigosCepa = indiceALetra($indice++);	
					$indicelatigosHa = indiceALetra($indice++);	

					$actualSheet			
								->setCellValue($area.$filaI, $value["area"])
								->setCellValue($indiceCepas.$filaI, $value["car_tallos"])
								->setCellValue($indiceLatigos.$filaI, $value["car_tallos_latigo"])
								->setCellValue($indicelatigosCepa.$filaI,'='.$indiceLatigos.$filaI.'/'.$indiceCepas.$filaI)
								->setCellValue($indicelatigosHa.$filaI,'='.$indiceLatigos.$filaI.'/'.$area.$filaI);
					
				}

				$actualSheet->getStyle($indiceLatigos.$filaInit.':'.$indiceLatigos.$filaI)->getNumberFormat()->setFormatCode('#,##0.00');	
				$actualSheet->getStyle($indicelatigosHa.$filaInit.':'.$indicelatigosHa.$filaI)->getNumberFormat()->setFormatCode('#,##0.00');	
			}
			//$actualSheet->getStyle('D'.$filaInit.':F'.$filaI)->applyFromArray($celdaNegativaEstilo);
			$actualSheet->setTitle('REPORTE CARBÓN RESUMEN');

			/*DATA SHEET 3 : CARBON RESUMEN*/
		 	
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="reporte-formularios-evaluacion-'.date('dmYHis').'.xlsx"');
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

