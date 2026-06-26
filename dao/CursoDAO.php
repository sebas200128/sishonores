<?php
// dao/CursoDAO.php

require_once __DIR__ . '/../bean/CursoBean.php';

class CursoDAO {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getById($id) {
        $query = "SELECT * FROM cursos WHERE id_curso = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $this->mapRowToBean($row);
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

    public function guardar(CursoBean $curso) {
        $id = $curso->getIdCurso();
        $nombre = $curso->getNombreCurso();
        $codigo = $curso->getCodigoCurso();
        $horas = $curso->getHorasSemanales();
        $id_nivel = $curso->getIdNivel();

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
            $curso->setIdCurso((int)$this->db->lastInsertId());
        }
        return $success;
    }

    public function eliminar($id) {
        $query = "DELETE FROM cursos WHERE id_curso = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function mapRowToBean(array $row) {
        $bean = new CursoBean();
        $bean->setIdCurso((int)$row['id_curso']);
        $bean->setNombreCurso($row['nombre_curso']);
        $bean->setCodigoCurso($row['codigo_curso']);
        $bean->setHorasSemanales((int)$row['horas_semanales']);
        $bean->setIdNivel((int)$row['id_nivel']);
        return $bean;
    }
}
?>
