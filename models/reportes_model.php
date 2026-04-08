<?php
class reportes_model {
    private $db;

    public function __construct() {
        $this->db = Conectar::conexion();
    }

    public function get_predios() {
        $res = $this->db->query("SELECT * FROM predio"); 
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function get_modulos() {
        $res = $this->db->query("SELECT * FROM modulo"); 
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function get_categorias() {
        $res = $this->db->query("SELECT * FROM categoria"); 
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function insertar_reporte($titulo, $desc, $foto, $ci, $id_ubi) {
        $titulo = $this->db->real_escape_string($titulo);
        $desc = $this->db->real_escape_string($desc);
        $ci = $this->db->real_escape_string($ci);

        // Insertamos usando el ID de ubicación (que puede ser un modulo o predio)
        $sql = "INSERT INTO reportaje (titulo, descripcion, foto, denunciante, id_usuario, id_estado, id_ubicacion) 
                VALUES ('$titulo', '$desc', '$foto', '$ci', 1, 1, $id_ubi)";
        
        if ($this->db->query($sql)) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function vincular_categorias($id_reportaje, $categorias) {
        foreach ($categorias as $id_cat) {
            $id_cat = (int)$id_cat;
            $this->db->query("INSERT INTO reportaje_categoria (id_reportaje, id_categoria) VALUES ($id_reportaje, $id_cat)"); 
        }
    }

    public function get_reportes_por_ci($ci) {
        $ci = $this->db->real_escape_string($ci);
        /**
         * JOIN AVANZADO: Buscamos el nombre del módulo o predio según la herencia.
         * Usamos COALESCE para mostrar el nombre del módulo si existe, o el del predio.
         */
        $sql = "SELECT r.*, e.descripcion AS estado_nombre, 
                COALESCE(m.numero, p.nombre) AS ubicacion_info 
                FROM reportaje r
                JOIN estado e ON r.id_estado = e.id_estado
                JOIN ubicacion u ON r.id_ubicacion = u.id_ubicacion
                LEFT JOIN modulo m ON u.id_ubicacion = m.id_modulo
                LEFT JOIN predio p ON u.id_ubicacion = p.id_predio
                WHERE r.denunciante = '$ci'
                ORDER BY r.fecha_creacion DESC";
        
        $res = $this->db->query($sql);
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function get_todos_los_reportes() {
        $sql = "SELECT r.*, e.descripcion AS estado_nombre, 
                COALESCE(m.numero, p.nombre) AS nombre_lugar,
                pr.nombre AS nombre_predio_padre
                FROM reportaje r
                JOIN estado e ON r.id_estado = e.id_estado
                JOIN ubicacion u ON r.id_ubicacion = u.id_ubicacion
                LEFT JOIN modulo m ON u.id_ubicacion = m.id_modulo
                LEFT JOIN predio pr ON m.id_predio = pr.id_predio
                LEFT JOIN predio p ON u.id_ubicacion = p.id_predio
                ORDER BY r.fecha_creacion DESC";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // Filtra reportes por predio para los Encargados (Ciudad o Campus).
    public function get_reportes_por_predio($id_predio) {
        $id_predio = (int)$id_predio;
        $sql = "SELECT r.*, e.descripcion AS estado_nombre, m.numero AS nombre_lugar
                FROM reportaje r
                JOIN estado e ON r.id_estado = e.id_estado
                JOIN modulo m ON r.id_ubicacion = m.id_modulo
                WHERE m.id_predio = $id_predio
                ORDER BY r.fecha_creacion DESC";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
}
?>