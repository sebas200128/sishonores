<?php
// model/Aula.php

class Aula {
    private $db;

    private $id_aula;
    private $id_grado;
    private $id_seccion;
    private $anio;
    private $vacantes;

    public function __construct($db = null) {
        $this->db = $db;
    }

    // Getters y Setters
    public function getIdAula() { return $this->id_aula; }
    public function setIdAula($id_aula) { $this->id_aula = $id_aula; }

    public function getIdGrado() { return $this->id_grado; }
    public function setIdGrado($id_grado) { $this->id_grado = $id_grado; }

    public function getIdSeccion() { return $this->id_seccion; }
    public function setIdSeccion($id_seccion) { $this->id_seccion = $id_seccion; }

    public function getAnio() { return $this->anio; }
    public function setAnio($anio) { $this->anio = $anio; }

    public function getVacantes() { return $this->vacantes; }
    public function setVacantes($vacantes) { $this->vacantes = $vacantes; }

    // Métodos de Persistencia

    public function getById($id) {
        $query = "SELECT * FROM aulas_asignadas WHERE id_aula = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $this->mapRowToModel($row);
        }
        return null;
    }

    public function listarTodas() {
        $query = "SELECT aa.*, g.nombre_grado, s.nombre_seccion, n.nombre_nivel
                  FROM aulas_asignadas aa
                  JOIN grados g ON aa.id_grado = g.id_grado
                  JOIN secciones s ON aa.id_seccion = s.id_seccion
                  JOIN niveles n ON g.id_nivel = n.id_nivel
                  ORDER BY n.nombre_nivel, g.nombre_grado, s.nombre_seccion";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkAulaDuplicada($id_grado, $id_seccion, $anio, $id_aula_excluir) {
        $query = "SELECT COUNT(*) FROM aulas_asignadas 
                  WHERE id_grado = :grado AND id_seccion = :seccion AND anio = :anio AND id_aula != :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':grado', $id_grado, PDO::PARAM_INT);
        $stmt->bindValue(':seccion', $id_seccion, PDO::PARAM_INT);
        $stmt->bindValue(':anio', $anio, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id_aula_excluir, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    public function guardar() {
        $id = $this->id_aula;
        $id_grado = $this->id_grado;
        $id_seccion = $this->id_seccion;
        $vacantes = $this->vacantes;
        $anio = $this->anio;

        if ($id > 0) {
            $query = "UPDATE aulas_asignadas 
                      SET id_grado=:grado, id_seccion=:seccion, vacantes=:vacantes, anio=:anio 
                      WHERE id_aula=:id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        } else {
            $query = "INSERT INTO aulas_asignadas (id_grado, id_seccion, vacantes, anio) 
                      VALUES (:grado, :seccion, :vacantes, :anio)";
            $stmt = $this->db->prepare($query);
        }

        $stmt->bindValue(':grado', $id_grado, PDO::PARAM_INT);
        $stmt->bindValue(':seccion', $id_seccion, PDO::PARAM_INT);
        $stmt->bindValue(':vacantes', $vacantes, PDO::PARAM_INT);
        $stmt->bindValue(':anio', $anio, PDO::PARAM_INT);

        $success = $stmt->execute();
        if ($success && $id <= 0) {
            $this->setIdAula((int)$this->db->lastInsertId());
        }
        return $success;
    }

    public function eliminar($id) {
        $query = "DELETE FROM aulas_asignadas WHERE id_aula = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function mapRowToModel(array $row) {
        $model = new Aula($this->db);
        $model->setIdAula((int)$row['id_aula']);
        $model->setIdGrado((int)$row['id_grado']);
        $model->setIdSeccion((int)$row['id_seccion']);
        $model->setVacantes((int)$row['vacantes']);
        $model->setAnio((int)$row['anio']);
        return $model;
    }
}
?>
