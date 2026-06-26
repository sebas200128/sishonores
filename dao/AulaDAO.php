<?php
// dao/AulaDAO.php

require_once __DIR__ . '/../bean/AulaBean.php';

class AulaDAO {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getById($id) {
        $query = "SELECT * FROM aulas_asignadas WHERE id_aula = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $this->mapRowToBean($row);
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

    public function guardar(AulaBean $aula) {
        $id = $aula->getIdAula();
        $id_grado = $aula->getIdGrado();
        $id_seccion = $aula->getIdSeccion();
        $vacantes = $aula->getVacantes();
        $anio = $aula->getAnio();

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
            $aula->setIdAula((int)$this->db->lastInsertId());
        }
        return $success;
    }

    public function eliminar($id) {
        $query = "DELETE FROM aulas_asignadas WHERE id_aula = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function mapRowToBean(array $row) {
        $bean = new AulaBean();
        $bean->setIdAula((int)$row['id_aula']);
        $bean->setIdGrado((int)$row['id_grado']);
        $bean->setIdSeccion((int)$row['id_seccion']);
        $bean->setVacantes((int)$row['vacantes']);
        $bean->setAnio((int)$row['anio']);
        return $bean;
    }
}
?>
