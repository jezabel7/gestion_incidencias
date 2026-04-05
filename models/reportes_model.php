<?php
class reportes_model {
    private $db;

    public function __construct() {
        $this->db = Conectar::conexion();
    }

    // Métodos Get para cargar los selectores del formulario
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

    // Primero insertamos la ubicación, luego el reporte
    public function insertar_reporte($titulo, $desc, $foto, $ci, $id_ubi) {
        $titulo = $this->db->real_escape_string($titulo);
        $desc = $this->db->real_escape_string($desc);
        $ci = $this->db->real_escape_string($ci);

        // id_estado = 1 (Pendiente), id_usuario = 1 (Admin por defecto)
        $sql = "INSERT INTO reportaje (titulo, descripcion, foto, denunciante, id_usuario, id_estado, id_ubicacion) 
                VALUES ('$titulo', '$desc', '$foto', '$ci', 1, 1, $id_ubi)";
        
        if ($this->db->query($sql)) {
            return $this->db->insert_id; // Retornamos el ID para la tabla intermedia
        }
        return false;
    }

    public function crear_ubicacion($detalle) {
        $detalle = $this->db->real_escape_string($detalle);
        $this->db->query("INSERT INTO ubicacion (nombre_generico) VALUES ('$detalle')");
        return $this->db->insert_id;
    }

    // Relación Muchos a Muchos (Tabla Intermedia)
    public function vincular_categorias($id_reportaje, $categorias) {
        foreach ($categorias as $id_cat) {
            $id_cat = (int)$id_cat;
            $this->db->query("INSERT INTO reportaje_categoria (id_reportaje, id_categoria) VALUES ($id_reportaje, $id_cat)");
        }
    }
}
?>