<?php
class usuarios_model {
    private $db;

    public function __construct() {
        $this->db = Conectar::conexion();
    }

    // PROCESO: AUTENTICACIÓN

    // Valida la existencia de un usuario mediante su CI
    public function buscar_por_ci($ci) {
        $ci = $this->db->real_escape_string($ci);
        $res = $this->db->query("SELECT * FROM usuario WHERE ci = '$ci'");
        return $res->fetch_assoc();
    }

    // Gestion de Personal

    // Obtiene el listado de técnicos con sus especialidades y predio
    public function get_usuarios_gestion() {
        $sql = "SELECT u.*, p.nombre AS nombre_predio, 
                GROUP_CONCAT(c.nombre SEPARATOR ', ') as nombres_especialidades
                FROM usuario u
                LEFT JOIN predio p ON u.id_predio = p.id_predio
                LEFT JOIN usuario_categoria uc ON u.id_usuario = uc.id_usuario
                LEFT JOIN categoria c ON uc.id_categoria = c.id_categoria
                WHERE u.rol != 'estudiante'
                GROUP BY u.id_usuario
                ORDER BY u.ci ASC";
        
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // Registra un nuevo encargado y vincula sus categorías técnicas

    public function guardar_usuario($datos, $especialidades) {
        $ci   = $this->db->real_escape_string($datos['ci']);
        $nom  = $this->db->real_escape_string($datos['nombre']);
        $ape  = $this->db->real_escape_string($datos['apellido']);
        $pass = $datos['pass']; 
        $rol  = $datos['rol'];   
        $id_p = !empty($datos['id_predio']) ? $datos['id_predio'] : "NULL";
        
        $sql_u = "INSERT INTO usuario (nombre, apellido, ci, pass, rol, id_predio) 
                  VALUES ('$nom', '$ape', '$ci', '$pass', '$rol', $id_p)";
        
        if ($this->db->query($sql_u)) {
            $nuevo_id = $this->db->insert_id;

            // Vinculación en tabla intermedia (Relación N:M)
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