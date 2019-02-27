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
	//private $_pages = "./pages";
	//private $templates = ["login", "registrar_solicitud", "mis_solicitudes","mis_eventos"];
	public function generate(){
		try {
			$files = scandir($this->_root);
			$fileContentPagesScript = "";
		    foreach($files as $key => $value){
		        $path = realpath($this->_root."/".$value);
		        if(!is_dir($path)) {
		            $results[] = $path;
		            $fileContentPagesScript .= " <script id='".str_replace(".", "-", $value)."' type='text/template'>".file_get_contents($path, true)."</script>";		            
		        }
		        /* else if($value != "." && $value != "..") {
		            getDirContents($path, $results);
		            $results[] = $path;
		        }*/
		    }

		    return $fileContentPagesScript;
		} catch (Exception $e) {
			throw new $e;	
		}

/*
		try {
			if (file_exists($nombreArchivo)) unlink($nombreArchivo);
			$achivo = fopen($nombreArchivo, "w") or die("Unable to open file!");
			$fileContent ="";
			$fileContent .= file_get_contents($this->_parciales.'/top/index.'.$extension, true);
			$fileContentPages = "";

			foreach ($this->PAGES as $key => $pagina) {
				$tmpFile = $this->_pages."/".$pagina."/index.".$extension;
				if (file_exists($tmpFile)){
					$fileContentPages .= file_get_contents($tmpFile);
				}
			}

			$fileContent .= $fileContentPages;
			$fileContent .= file_get_contents($this->_parciales.'/bottom/index.'.$extension, true);

			fwrite($achivo,$fileContent);
			fclose($achivo);

			return true;
		} catch (Exception $e) {
			throw new $e;
			return false;
		}
		*/
		
	}
}

//$indexHTML = "index.html";
//$appJS = "js/app.js";

$obj  = new Compilador;
echo $obj->generate();
/*
if ($obj->generate($indexHTML,"html")){
	echo "index.html Generado...\n";
}
if ($obj->generate($appJS,"js")){
	echo "app.js Generado...\n";
}*/




