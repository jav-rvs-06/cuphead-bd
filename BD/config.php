<?php
define('HOST_BD', 'localhost');
define('USUARIO_BD', 'root');
define('CONTRASENA_BD', '');
define('NOMBRE_BD', 'cuphead_guia');

$conexion = new mysqli(HOST_BD, USUARIO_BD, CONTRASENA_BD, NOMBRE_BD);

if ($conexion->connect_error) {
    die("Error de conexión a la BD: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
