<?php
require_once("models/reportes_model.php");
$model = new reportes_model();

$accion = isset($_GET['a']) ? $_GET['a'] : 'home';

switch($accion) {
    case 'nuevo':
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['titulo'])) {
            
            // 1. Imagen
            $foto_nombre = "";
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $foto_nombre = "img_" . time() . "_" . uniqid() . "." . $extension;
                move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto_nombre);
            }

            // 2. Procesar Ubicación: El id_modulo YA ES un id_ubicacion en la nueva DB 
            $id_ubi = $_POST['id_modulo_real']; 
            $descripcion_final = $_POST['descripcion'] . " (Ref: " . $_POST['aula'] . ")";

            // 3. Insertar Reporte
            $id_reporte = $model->insertar_reporte($_POST['titulo'], $descripcion_final, $foto_nombre, $_POST['ci_denunciante'], $id_ubi);

            // 4. Categorías
            if ($id_reporte && isset($_POST['categorias'])) {
                $model->vincular_categorias($id_reporte, $_POST['categorias']); 
            }

            echo "<script>alert('¡Reporte enviado con éxito!'); window.location='index.php';</script>";
            exit();
        }

        $predios = $model->get_predios(); 
        $modulos = $model->get_modulos(); 
        $categorias = $model->get_categorias(); 
        require_once("views/reportes_view.phtml");
        break;

    case 'consulta':
        $mis_reportes = null;
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ci_busqueda'])) {
            $mis_reportes = $model->get_reportes_por_ci($_POST['ci_busqueda']);
        }
        require_once("views/seguimiento_view.phtml");
        break;

    default:
        require_once("views/home_view.phtml");
        break;
}
?>