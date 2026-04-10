<?php
class usuarios_model {
    private $db;

    public function __construct() {
        $this->db = Conectar::conexion();
    }

    /**
     * Busca un usuario por su CI para validar sus credenciales
     */
    public function buscar_por_ci($ci) {
        $ci = $this->db->real_escape_string($ci);
        // Buscamos específicamente en la columna 'ci' que viste en tu DB
        $res = $this->db->query("SELECT * FROM usuario WHERE ci = '$ci'");
        return $res->fetch_assoc();
    }

    /**
     * Obtiene la lista de técnicos para el Admin
     * Incluye especialidades concatenadas y el nombre del predio
     */
    public function get_usuarios_gestion() {
        $sql = "SELECT u.*, p.nombre AS nombre_predio, 
                GROUP_CONCAT(c.nombre SEPARATOR ', ') as nombres_especialidades
                FROM usuario u
                LEFT JOIN predio p ON u.id_predio = p.id_predio
                LEFT JOIN usuario_categoria uc ON u.id_usuario = uc.id_usuario
                LEFT JOIN categoria c ON uc.id_categoria = c.id_categoria
                WHERE u.rol != 'estudiante'
                GROUP BY u.id_usuario
                ORDER BY u.ci ASC"; // Ordenamos por CI como pediste
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Inserta un nuevo encargado y sus especialidades
     */
    public function guardar_usuario($datos, $especialidades) {
        $ci = $this->db->real_escape_string($datos['ci']);
        $nom = $this->db->real_escape_string($datos['nombre']);
        $ape = $this->db->real_escape_string($datos['apellido']);
        $pass = $datos['pass']; // Ya vendrá hasheada del controlador
        $rol = $datos['rol'];   // Siempre será 'encargado' según tu flujo
        $id_p = !empty($datos['id_predio']) ? $datos['id_predio'] : "NULL";
        
        // 1. INSERT puro: Dejamos que el id_usuario se genere solo
        // Insertamos el CI en su columna correspondiente
        $sql_u = "INSERT INTO usuario (nombre, apellido, ci, pass, rol, id_predio) 
                  VALUES ('$nom', '$ape', '$ci', '$pass', '$rol', $id_p)";
        
        if ($this->db->query($sql_u)) {
            // Obtenemos el ID que se acaba de generar para el nuevo usuario
            $nuevo_id = $this->db->insert_id;

            // 2. Insertar Especialidades en la tabla intermedia
            if (!empty($especialidades)) {
                foreach ($especialidades as $id_cat) {
                    $id_cat = (int)$id_cat;
                    $this->db->query("INSERT INTO usuario_categoria (id_usuario, id_categoria) 
                                      VALUES ($nuevo_id, $id_cat)");
                }
            }
            return true;
        }
        return false;
    }
}
?>