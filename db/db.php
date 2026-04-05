<?php
class Conectar {
    public static function conexion() {
        try {

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            
            $conexion = new mysqli("localhost", "root", "", "uagrm_incidencias");
            $conexion->set_charset("utf8mb4");
            
            return $conexion;
            
        } catch (mysqli_sql_exception $e) {
            
            die("Error crítico de conexión: " . $e->getMessage());
        }
    }
}
?>