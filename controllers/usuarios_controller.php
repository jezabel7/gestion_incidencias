<?php
require_once("models/usuarios_model.php");
$u_model = new usuarios_model();

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
                $_SESSION['id_predio'] = $usuario['id_predio']; // NULL si es Admin 

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

    default:
        require_once("views/login_view.phtml");
        break;
}
?>