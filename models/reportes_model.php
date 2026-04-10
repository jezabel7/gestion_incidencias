<?php
class reportes_model {
    private $db;

    public function __construct() {
        $this->db = Conectar::conexion();
    }

    // Selector De Catalogos

    public function get_predios() {
        return $this->db->query("SELECT * FROM predio")->fetch_all(MYSQLI_ASSOC);
    }

    public function get_modulos() {
        return $this->db->query("SELECT * FROM modulo")->fetch_all(MYSQLI_ASSOC);
    }

    public function get_categorias() {
        return $this->db->query("SELECT * FROM categoria")->fetch_all(MYSQLI_ASSOC);
    }

    // Regitro De Incidencias

    public function insertar_reporte($titulo, $desc, $foto, $ci, $id_ubi) {
        $titulo = $this->db->real_escape_string($titulo);
        $desc   = $this->db->real_escape_string($desc);
        $ci     = $this->db->real_escape_string($ci);

        $sql = "INSERT INTO reportaje (titulo, descripcion, foto, denunciante, id_usuario, id_estado, id_ubicacion) 
                VALUES ('$titulo', '$desc', '$foto', '$ci', 1, 1, $id_ubi)";
        
        return ($this->db->query($sql)) ? $this->db->insert_id : false;
    }

    public function vincular_categorias($id_reportaje, $categorias) {
        foreach ($categorias as $id_cat) {
            $id_cat = (int)$id_cat;
            $this->db->query("INSERT INTO reportaje_categoria (id_reportaje, id_categoria) VALUES ($id_reportaje, $id_cat)"); 
        }
    }

    // Consultas De Seguimiento

    public function get_reportes_por_ci($ci) {
        $ci = $this->db->real_escape_string($ci);
        $sql = "SELECT r.*, e.descripcion AS estado_nombre, 
                COALESCE(m.numero, p.nombre) AS ubicacion_info 
                FROM reportaje r
                JOIN estado e ON r.id_estado = e.id_estado
                JOIN ubicacion u ON r.id_ubicacion = u.id_ubicacion
                LEFT JOIN modulo m ON u.id_ubicacion = m.id_modulo
                LEFT JOIN predio p ON u.id_ubicacion = p.id_predio
                WHERE r.denunciante = '$ci'
                ORDER BY r.fecha_creacion DESC";
        
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function get_todos_los_reportes() {
        $sql = "SELECT r.*, e.descripcion AS estado_nombre, 
                COALESCE(m.numero, p.nombre) AS nombre_lugar,
                COALESCE(m.id_predio, p.id_predio) AS id_predio_reporte,
                (SELECT GROUP_CONCAT(id_categoria) FROM reportaje_categoria WHERE id_reportaje = r.id_reportaje) AS ids_categorias_reporte
                FROM reportaje r
                JOIN estado e ON r.id_estado = e.id_estado
                JOIN ubicacion u ON r.id_ubicacion = u.id_ubicacion
                LEFT JOIN modulo m ON u.id_ubicacion = m.id_modulo
                LEFT JOIN predio p ON u.id_ubicacion = p.id_predio
                ORDER BY r.fecha_creacion DESC";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function get_reportes_por_predio($id_predio) {
        $id_predio = (int)$id_predio;
        $sql = "SELECT r.*, e.descripcion AS estado_nombre, 
                COALESCE(m.numero, p.nombre) AS nombre_lugar,
                COALESCE(m.id_predio, p.id_predio) AS id_predio_reporte,
                (SELECT GROUP_CONCAT(id_categoria) FROM reportaje_categoria WHERE id_reportaje = r.id_reportaje) AS ids_categorias_reporte
                FROM reportaje r
                JOIN estado e ON r.id_estado = e.id_estado
                JOIN ubicacion u ON r.id_ubicacion = u.id_ubicacion
                LEFT JOIN modulo m ON u.id_ubicacion = m.id_modulo
                LEFT JOIN predio p ON u.id_ubicacion = p.id_predio
                WHERE (m.id_predio = $id_predio OR p.id_predio = $id_predio)
                ORDER BY r.fecha_creacion DESC";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // Consulas de Gestion y Asignacion

    public function get_todos_los_tecnicos() {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido, u.id_predio, 
                GROUP_CONCAT(c.nombre SEPARATOR ', ') AS nombres_especialidades,
                GROUP_CONCAT(c.id_categoria SEPARATOR ',') AS ids_especialidades
                FROM usuario u
                LEFT JOIN usuario_categoria uc ON u.id_usuario = uc.id_usuario
                LEFT JOIN categoria c ON uc.id_categoria = c.id_categoria
                WHERE u.rol = 'encargado'
                GROUP BY u.id_usuario";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function actualizar_gestion($id_reporte, $id_estado, $id_usuario) {
        $id_reporte = (int)$id_reporte;
        $id_estado  = (int)$id_estado;
        $id_usuario = (int)$id_usuario;

        $sql = "UPDATE reportaje SET id_estado = $id_estado, id_usuario = $id_usuario 
                WHERE id_reportaje = $id_reporte";
        return $this->db->query($sql);
    }

    public function get_tecnicos_por_predio($id_predio) {
        $id_predio = (int)$id_predio;
        $sql = "SELECT id_usuario, nombre, apellido FROM usuario 
                WHERE rol = 'encargado' AND id_predio = $id_predio";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function get_reportes_por_usuario($id_usuario) {
        $id_usuario = (int)$id_usuario;
        $sql = "SELECT r.*, e.descripcion AS estado_nombre, 
                COALESCE(m.numero, p.nombre) AS nombre_lugar
                FROM reportaje r
                JOIN estado e ON r.id_estado = e.id_estado
                JOIN ubicacion u ON r.id_ubicacion = u.id_ubicacion
                LEFT JOIN modulo m ON u.id_ubicacion = m.id_modulo
                LEFT JOIN predio p ON u.id_ubicacion = p.id_predio
                WHERE r.id_usuario = $id_usuario
                ORDER BY r.id_estado ASC, r.fecha_creacion DESC";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function actualizar_estado_simple($id_reporte, $id_estado) {
        $id_reporte = (int)$id_reporte;
        $id_estado = (int)$id_estado;
        return $this->db->query("UPDATE reportaje SET id_estado = $id_estado WHERE id_reportaje = $id_reporte");
    }
}