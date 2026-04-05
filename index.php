<?php
session_start();

require_once("db/db.php");

// si no se envía un controlador por la URL, se carga 'reportes' por defecto
$controller_name = isset($_GET['c']) ? $_GET['c'] : 'reportes';

// ruta del archivo siguiendo
$path = "controllers/" . $controller_name . "_controller.php";

if (file_exists($path)) {
    require_once($path);
} else {
    // si alguien intenta entrar a un controlador que no existe
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 - El módulo que buscas no existe en el sistema UAGRM.</h1>";
}
?>