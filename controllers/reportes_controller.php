<?php
require_once("models/reportes_model.php");
$model = new reportes_model();

$accion = isset($_GET['a']) ? $_GET['a'] : 'home';

switch($accion) {

    // Reportar Daño - Registro de nuevas incidencias por parte del denunciante
    case 'nuevo':
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['titulo'])) {
            
            // Procesamiento de evidencia fotográfica
            $foto_nombre = "";
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $foto_nombre = "img_" . time() . "_" . uniqid() . "." . $extension;
                move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto_nombre);
            }

            // Preparación de datos de ubicación y descripción
            $id_ubi = $_POST['id_modulo_real']; 
            $descripcion_final = $_POST['descripcion'] . " (Ref: " . $_POST['aula'] . ")";

            // Registro principal de la incidencia
            $id_reporte = $model->insertar_reporte(
                $_POST['titulo'], 
                $descripcion_final, 
                $foto_nombre, 
                $_POST['ci_denunciante'], 
                $id_ubi
            );

            // Vinculación de categorías para la gestión posterior
            if ($id_reporte && isset($_POST['categorias'])) {
                $model->vincular_categorias($id_reporte, $_POST['categorias']); 
            }

            echo "<script>alert('¡Reporte enviado con éxito!'); window.location='index.php';</script>";
            exit();
        }

        // Carga de catálogos para el formulario
        $predios = $model->get_predios(); 
        $modulos = $model->get_modulos(); 
        $categorias = $model->get_categorias(); 
        require_once("views/reportes_view.phtml");
        break;

    // Seguimiento de Incidencias - Consulta de estado mediante CI
    case 'consulta':
        $mis_reportes = null;
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ci_busqueda'])) {
            $mis_reportes = $model->get_reportes_por_ci($_POST['ci_busqueda']);
        }
        require_once("views/seguimiento_view.phtml");
        break;

    case 'home':
    default:
        require_once("views/home_view.phtml");
        break;
}