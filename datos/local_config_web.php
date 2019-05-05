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

 //SESION

 define("SW_NOMBRE","AgriCayaltí");
 define("SW_NOMBRE_COMPLETO","Sistema de Biometría y Sanidad CAYALTI");
 define("SW_VERSION","2.5.0 BETA");

 define("MODO_PRODUCCION", 1);

ini_set('session.save_handler', 'memcached');
ini_set('session.save_path', getenv('MEMCACHIER_SERVERS'));
if(version_compare(phpversion('memcached'), '3', '>=')) {
    ini_set('memcached.sess_persistent', 1);
    ini_set('memcached.sess_binary_protocol', 1);
} else {
    ini_set('session.save_path', 'PERSISTENT=myapp_session ' . ini_get('session.save_path'));
    ini_set('memcached.sess_binary', 1);
}
ini_set('memcached.sess_sasl_username', getenv('MEMCACHIER_USERNAME'));
ini_set('memcached.sess_sasl_password', getenv('MEMCACHIER_PASSWORD'));

 //SESION
 define("_SESION_","_sanidad_cayalti_web_");
 session_name(_SESION_);
 session_start();
