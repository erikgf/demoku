

<?php 

require_once '../datos/local_config_web.php';
require_once MODELO_FUNCIONES;

$modelo  = "Campaña";
require_once MODELO . "/".utf8_decode($modelo).".clase.php";
$obj = new $modelo;
$metodo = "registrarCampaña";
$data_in = isset($_POST["data_in"]) ? json_decode($_POST["data_in"]) : null; //parametros que son parte de la clase.
$data_out = isset($_POST["data_out"]) ? json_decode($_POST["data_out"]) : null; //parámetros q no son parte de la clase.

if(is_callable(array($obj, $metodo))){
	if ($data_in != null){
			//recorrer el arreglo y asignar todo lo posible.
			foreach ($data_in as $key=>$valor) {
				$str = "set".ucfirst(substr($key, 2));
                    $obj->$str($valor);            
			}
	}

	/* Checkear qué tipo de riego es la siembra.
		Cuantas cmapañas van.*/
	$predata = $obj->getPredataRegistro();

	if ($predata["n_campaña"] == 0){
		if ($_FILES){
			$archivo = $_FILES["p_archivo"]["tmp_name"];

			$handle = fopen($archivo, "r");
			if ($handle) {
				$indexLine = 0;
				$umdArreglo = [];
				$tempUmd = null;

			    while (($linea = fgets($handle)) !== false) {
			    	$itemLinea =  explode("|",$linea);			    	
			    	if ($indexLine == 0){
			    		$coordenadaCampo = explode(",",$itemLinea[0]);			    		
			    	} else{

			    		if ($itemLinea[0] == "*") {//Si el primer espacio es un "*", se trata de un UMD cabecera.

			    			if ($tempUmd != null){
			    				//si ya viene con algo, entonces pushealo al arreglo umd;
			    				array_push($umdArreglo,$tempUmd);
			    			}			    			

			    		 	$tempUmd = ["nivel_uno"=>trim($itemLinea[1]),
			    		 				"nivel_dos"=>trim($itemLinea[2]),
			    		 				"nivel_tres"=>trim($itemLinea[3]) == "" ? NULL : trim($itemLinea[3]),  
			    		 				"hectarea"=>trim($itemLinea[4]),
			    		 				"coordenadas"=>[] ];			    		 			
			    		} else {
			    			if ($tempUmd != null){
			    			 	array_push($tempUmd["coordenadas"], 
			    			 				[	"numero_vertice"=>trim($itemLinea[0]),
			    			 					"latitud"=>trim($itemLinea[2]), /*1= segunda caja*/
			    			 					"longitud"=>trim($itemLinea[3]) /*2= tercer caja */
			    			 				]);
			    			}
			    		}

			    	}
			        //armar el json.
			        /*
					  1.- registrar en umd.
					  2.- registrar en umd_coordenadas
					  3.- registrar en campaña_umd.
						  [
							0: {k0, k1, k2, size, 
								coordenadas : [ 
										{a, b},
										{c, d} ]
								}
						  ]
			        */
					$indexLine++;
			    }
			    array_push($umdArreglo,$tempUmd);

			    fclose($handle);
			    array_push($data_out, json_encode($umdArreglo));
			    array_push($data_out, $coordenadaCampo);

			} else {
			    var_dump("Error en archivo");
			} 
		} else {
			//mensaje, oe brother es obligatorio subir un archivo con distribución.
		}
	} else {
		//usar las de la ultima campaña.
	}

	$rpta = call_user_func_array(
	    array($obj, $metodo), $data_out == null ? array() : $data_out
	);

	Funciones::imprimeJSON(200,"OK",$rpta);
}else{
	Funciones::imprimeJSON(500, "El método $metodo de la clase $modelo no existe.", "");
}