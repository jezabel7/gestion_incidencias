<?php
require_once("models/reportes_model.php");
$r_model = new reportes_model();

// 🛡️ SEGURIDAD: Solo personal logueado entra aquí
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'encargado')) {
    header("Location: index.php?c=usuarios");
    exit();
}

$accion = isset($_GET['a']) ? $_GET['a'] : 'dashboard';

switch($accion) {
    case 'dashboard':
        // Lógica de filtro automático por Predio
        if ($_SESSION['rol'] == 'admin') {
            $reportes = $r_model->get_todos_los_reportes();
            $titulo_panel = "Panel General - Todos los Predios";
        } else {
            $reportes = $r_model->get_reportes_por_predio($_SESSION['id_predio']);
            $titulo_panel = "Gestión de Incidencias - Mi Predio";
        }
        $tecnicos = $r_model->get_todos_los_tecnicos();
        require_once("views/admin_dashboard_view.phtml");
        break;

    case 'guardar_gestion':
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_rep = $_POST['id_reporte'];
        $nuevo_estado = $_POST['nuevo_estado'];
        $tecnico_asig = $_POST['id_tecnico'];

        if ($r_model->actualizar_gestion($id_rep, $nuevo_estado, $tecnico_asig)) {
            echo "<script>alert('Gestión actualizada correctamente'); window.location='index.php?c=admin&a=dashboard';</script>";
        } else {
            echo "<script>alert('Error al actualizar'); window.history.back();</script>";
        }
        exit();
    }
    break;
}
?>