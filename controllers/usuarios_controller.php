<?php
require_once("models/usuarios_model.php");
require_once("models/reportes_model.php");

$u_model = new usuarios_model();
$r_model = new reportes_model();

$accion = isset($_GET['a']) ? $_GET['a'] : 'login';

switch($accion) {
    case 'ingresar':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ci = $_POST['ci'];
            $pass = $_POST['password'];

            $usuario = $u_model->buscar_por_ci($ci); 

            if ($usuario && password_verify($pass, $usuario['pass'])) { 
                // 🔐 Iniciamos la sesión y guardamos datos clave
                $_SESSION['user_id'] = $usuario['id_usuario'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = $usuario['rol']; 
                $_SESSION['id_predio'] = $usuario['id_predio']; 

                header("Location: index.php?c=admin&a=dashboard");
                exit();
            } else {
                $error = "CI o contraseña incorrectos.";
                require_once("views/login_view.phtml");
            }
        }
        break;

    case 'logout':
        session_destroy(); 
        header("Location: index.php");
        exit();

    case 'gestion':
        // 🛡️ SEGURIDAD: Solo el admin puede gestionar usuarios
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
            header("Location: index.php?c=admin&a=dashboard");
            exit();
        }
        
        // Cargamos los datos necesarios para la vista
        $lista_usuarios = $u_model->get_usuarios_gestion();
        $predios = $r_model->get_predios(); 
        $categorias = $r_model->get_categorias();
        
        $titulo_panel = "Gestión de Personal Autorizado";
        require_once("views/usuarios_view.phtml");
        break;    

    case 'guardar':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = $_POST;

            // 🛠️ LÓGICA DE NEGOCIO:
            // 1. La contraseña inicial es estándar para todos los nuevos encargados
            $datos['pass'] = password_hash('password123', PASSWORD_DEFAULT);
            
            // 2. El rol se define internamente como 'encargado' para evitar errores
            $datos['rol'] = 'encargado';
            
            $especialidades = isset($_POST['especialidades']) ? $_POST['especialidades'] : [];
            
            if ($u_model->guardar_usuario($datos, $especialidades)) {
                header("Location: index.php?c=usuarios&a=gestion");
                exit();
            }
        }
        break;

    default:
        require_once("views/login_view.phtml");
        break;    
}
?>