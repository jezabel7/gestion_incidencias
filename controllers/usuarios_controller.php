<?php
require_once("models/usuarios_model.php");
require_once("models/reportes_model.php");

$u_model = new usuarios_model();
$r_model = new reportes_model();

$accion = isset($_GET['a']) ? $_GET['a'] : 'login';

switch($accion) {

    // PROCESO: Autenticación de personal autorizado (Admin y Encargados)
    case 'ingresar':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ci = $_POST['ci'];
            $pass = $_POST['password'];

            $usuario = $u_model->buscar_por_ci($ci); 

            // Validación segura mediante verificación de hash
            if ($usuario && password_verify($pass, $usuario['pass'])) { 
                $_SESSION['user_id']   = $usuario['id_usuario'];
                $_SESSION['nombre']    = $usuario['nombre'];
                $_SESSION['rol']       = $usuario['rol']; 
                $_SESSION['id_predio'] = $usuario['id_predio']; 

                header("Location: index.php?c=admin&a=dashboard");
                exit();
            } else {
                $error = "CI o contraseña incorrectos.";
                require_once("views/login_view.phtml");
            }
        }
        break;

    // PROCESO: Finalización de sesión segura
    case 'logout':
        session_destroy(); 
        header("Location: index.php");
        exit();

    // Gestión de Usuarios - Listado administrativo de personal
    case 'gestion':
        // Filtro de seguridad: Acceso restringido a nivel Administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
            header("Location: index.php?c=admin&a=dashboard");
            exit();
        }
        
        $lista_usuarios = $u_model->get_usuarios_gestion();
        $predios = $r_model->get_predios(); 
        $categorias = $r_model->get_categorias();
        
        $titulo_panel = "Gestión de Personal Autorizado";
        require_once("views/usuarios_view.phtml");
        break;    

    // Gestión de Usuarios - Registro de nuevo personal con valores predefinidos
    case 'guardar':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = $_POST;

            // Se establece una contraseña estándar para el primer acceso del personal
            $datos['pass'] = password_hash('password123', PASSWORD_DEFAULT);
            
            // Restricción de rol: Los nuevos registros siempre se crean como encargados
            $datos['rol'] = 'encargado';
            
            $especialidades = isset($_POST['especialidades']) ? $_POST['especialidades'] : [];
            
            if ($u_model->guardar_usuario($datos, $especialidades)) {
                header("Location: index.php?c=usuarios&a=gestion");
                exit();
            }
        }
        break;

    case 'login':
    default:
        require_once("views/login_view.phtml");
        break;    
}