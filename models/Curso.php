<?php
// model/Curso.php

class Curso {
    private $db;

    private $id_curso;
    private $nombre_curso;
    private $codigo_curso;
    private $horas_semanales;
    private $id_nivel;

    public function __construct($db = null) {
        $this->db = $db;
    }

    // Getters y Setters
    public function getIdCurso() { return $this->id_curso; }
    public function setIdCurso($id_curso) { $this->id_curso = $id_curso; }

    public function getNombreCurso() { return $this->nombre_curso; }
    public function setNombreCurso($nombre_curso) { $this->nombre_curso = $nombre_curso; }

    public function getCodigoCurso() { return $this->codigo_curso; }
    public function setCodigoCurso($codigo_curso) { $this->codigo_curso = $codigo_curso; }

    public function getHorasSemanales() { return $this->horas_semanales; }
    public function setHorasSemanales($horas_semanales) { $this->horas_semanales = $horas_semanales; }

    public function getIdNivel() { return $this->id_nivel; }
    public function setIdNivel($id_nivel) { $this->id_nivel = $id_nivel; }

    // Métodos de Persistencia

    public function getById($id) {
        $query = "SELECT * FROM cursos WHERE id_curso = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $this->mapRowToModel($row);
        }
        return null;
    }

    public function listarTodos() {
        $query = "SELECT c.*, n.nombre_nivel
                  FROM cursos c
                  JOIN niveles n ON c.id_nivel = n.id_nivel
                  ORDER BY n.nombre_nivel, c.nombre_curso";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkCodigoDuplicado($codigo, $id) {
        $query = "SELECT COUNT(*) FROM cursos WHERE codigo_curso = :codigo AND id_curso != :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    public function guardar() {
        $id = $this->id_curso;
        $nombre = $this->nombre_curso;
        $codigo = $this->codigo_curso;
        $horas = $this->horas_semanales;
        $id_nivel = $this->id_nivel;

        if ($id > 0) {
            $query = "UPDATE cursos 
                      SET nombre_curso=:nombre, codigo_curso=:codigo, horas_semanales=:horas, id_nivel=:nivel 
                      WHERE id_curso=:id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        } else {
            $query = "INSERT INTO cursos (nombre_curso, codigo_curso, horas_semanales, id_nivel) 
                      VALUES (:nombre, :codigo, :horas, :nivel)";
            $stmt = $this->db->prepare($query);
        }

        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->bindValue(':horas', $horas, PDO::PARAM_INT);
        $stmt->bindValue(':nivel', $id_nivel, PDO::PARAM_INT);

        $success = $stmt->execute();
        if ($success && $id <= 0) {
            $this->setIdCurso((int)$this->db->lastInsertId());
        }
        return $success;
    }

    public function eliminar($id) {
        $query = "DELETE FROM cursos WHERE id_curso = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function mapRowToModel(array $row) {
        $model = new Curso($this->db);
        $model->setIdCurso((int)$row['id_curso']);
        $model->setNombreCurso($row['nombre_curso']);
        $model->setCodigoCurso($row['codigo_curso']);
        $model->setHorasSemanales((int)$row['horas_semanales']);
        $model->setIdNivel((int)$row['id_nivel']);
        return $model;
    }
}
?>
