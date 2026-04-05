<?php
require_once("models/reportes_model.php");
$model = new reportes_model();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['titulo'])) {
    
    // 1. Validación Pro de Imagen
    $foto_nombre = "";
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $permitidos = ['image/jpeg', 'image/png', 'image/jpg'];
        $limite_kb = 2048; // 2MB

        if (in_array($_FILES['foto']['type'], $permitidos) && $_FILES['foto']['size'] <= $limite_kb * 1024) {
            $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto_nombre = "img_" . time() . "_" . uniqid() . "." . $extension;
            move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto_nombre);
        } else {
            die("Error: El archivo no es una imagen válida o supera los 2MB.");
        }
    }

    // 2. Procesar Ubicación
    $detalle_u = "Predio: " . $_POST['predio_txt'] . " | Mod: " . $_POST['modulo_txt'] . " | Ref: " . $_POST['aula'];
    $id_ubi = $model->crear_ubicacion($detalle_u);

    // 3. Insertar Reporte
    $id_reporte = $model->insertar_reporte($_POST['titulo'], $_POST['descripcion'], $foto_nombre, $_POST['ci_denunciante'], $id_ubi);

    // 4. Procesar Categorías (Muchos a Muchos)
    if ($id_reporte && isset($_POST['categorias'])) {
        $model->vincular_categorias($id_reporte, $_POST['categorias']);
    }

    echo "<script>alert('¡Reporte enviado con éxito!'); window.location='index.php';</script>";
}

// Datos para la vista
$predios = $model->get_predios();
$modulos = $model->get_modulos();
$categorias = $model->get_categorias();

require_once("views/reportes_view.phtml");
?>