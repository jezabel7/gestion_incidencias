<?php
// Librerías de generación de reportes
require_once 'lib/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

require_once("models/reportes_model.php");
$r_model = new reportes_model();

// SEGURIDAD: Control de acceso para personal autorizado (Admin y Encargados)
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'encargado')) {
    header("Location: index.php?c=usuarios");
    exit();
}

$accion = isset($_GET['a']) ? $_GET['a'] : 'dashboard';

switch($accion) {

    // Dashboard Principal - Visualización de incidencias por rol
    case 'dashboard':
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

    // Gestión de Incidencias - Actualización de estado y asignación técnica
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

    // Generación de Reportes PDF - Reporte General Institucional
    case 'exportar_pdf_general':
        $reportes = $r_model->get_todos_los_reportes();
        $titulo_reporte = "Reporte General de Incidencias UAGRM";

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); 
        $dompdf = new Dompdf($options);

        ob_start();
        include 'views/pdf_reporte_general_view.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $dompdf->stream("Reporte_UAGRM_".date('d-m-Y').".pdf", ["Attachment" => false]);
        exit();
        break;

    default:
        header("Location: index.php?c=admin&a=dashboard");
        break;
}