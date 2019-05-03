<?php

/*
1.- Crear el archivo html/js
	parciales
		top
	foreach pages
		bot	
*/

class Compilador{
	private $_root = "js/templates";
	public function generate($nombreArchivo){
		try {
			if (file_exists($nombreArchivo)) unlink($nombreArchivo);
			$achivo = fopen($nombreArchivo, "w") or die("Unable to open file!");

			$files = scandir($this->_root);
			$fileContentPagesScript = "";
		    foreach($files as $key => $value){
		        $path = realpath($this->_root."/".$value);
		        if(!is_dir($path) && substr($value, -3) == "hbs"){ 
		            $results[] = $path;
		            $fileContentPagesScript .= " <script id='".str_replace(".", "-", $value)."' type='text/template'>".file_get_contents($path, true)."</script>";		            
		        }
		    }

		    fwrite($achivo,$fileContentPagesScript);
			fclose($achivo);

		    return true;
		} catch (Exception $e) {
			throw new $e;	
		}
	}
}

$indexHTML = "template.master.hbs";
//$appJS = "js/app.js";
$obj  = new Compilador;
//echo $obj->generate();
if ($obj->generate($indexHTML)){
	echo "index.html Generado...\n";
}
/*
if ($obj->generate($appJS,"js")){
	echo "app.js Generado...\n";
}*/




