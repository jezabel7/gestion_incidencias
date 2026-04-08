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
}
?>