<?php 
	if( ! isset($_SESSION["usuario"])){
	  	header("location:index.php");
	}	

	$nombreUsuario = ucwords(strtolower($_SESSION["usuario"]["nombres_usuario"]));
	$perfil = ucwords(strtolower($_SESSION["usuario"]["perfil"]));
 ?>

