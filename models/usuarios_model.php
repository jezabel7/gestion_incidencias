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
        $res = $this->db->query("SELECT * FROM usuario WHERE ci = '$ci'");
        return $res->fetch_assoc();
    }

    // Obtiene todos los técnicos (encargados) para el CRUD del Admin
    public function get_usuarios_gestion() {
        $sql = "SELECT u.*, p.nombre AS nombre_predio 
                FROM usuario u
                LEFT JOIN predio p ON u.id_predio = p.id_predio
                WHERE u.rol != 'estudiante' 
                /* 👈 CAMBIO AQUÍ: Ordenamos por el ID (que es el CI) */
                ORDER BY u.id_usuario ASC"; 
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // Para borrar un usuario (Cuidado: verifica si tiene reportes asignados)
    public function eliminar_usuario($id) {
        $id = (int)$id;
        return $this->db->query("DELETE FROM usuario WHERE id_usuario = $id");
    }
}
?>