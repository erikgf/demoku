<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//SERVIDOR

 date_default_timezone_set('America/Lima');

 define("MODELO", "../negocio");
 define("MODELO_UTIL", MODELO."/util");
 define("MODELO_FUNCIONES",MODELO_UTIL."/Funciones.php");

 define("SW_NOMBRE","AgriCayaltí");
 define("SW_NOMBRE_COMPLETO","Sistema de Biometría y Sanidad CAYALTI");
 define("SW_VERSION","2.5.0 BETA");

 define("MODO_PRODUCCION", 1);

 //SESION
 define("_SESION_","_sanidad_cayalti_web_");
 session_name(_SESION_);
 session_start();
