<?php
// dao/AsignacionDocenteDAO.php

require_once __DIR__ . '/../bean/AsignacionDocenteBean.php';

class AsignacionDocenteDAO {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getCursosPorAula($id_aula) {
        $query = "SELECT c.id_curso, c.nombre_curso
                  FROM cursos c
                  JOIN grados g ON c.id_nivel = g.id_nivel
                  JOIN aulas_asignadas aa ON aa.id_grado = g.id_grado
                  WHERE aa.id_aula = :aula
                  ORDER BY c.nombre_curso";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':aula', $id_aula, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkDocenteActivo($id_usuario) {
        $query = "SELECT COUNT(*)
                  FROM usuarios u
                  JOIN roles r ON u.id_rol = r.id_rol
                  WHERE u.id_usuario = :user
                    AND r.nombre_rol = 'Docente'
                    AND u.activo = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    public function checkCursoNivelAula($id_curso, $id_aula) {
        $query = "SELECT COUNT(*)
                  FROM cursos c
                  JOIN grados g ON c.id_nivel = g.id_nivel
                  JOIN aulas_asignadas aa ON aa.id_grado = g.id_grado
                  WHERE c.id_curso = :curso
                    AND aa.id_aula = :aula";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':curso', $id_curso, PDO::PARAM_INT);
        $stmt->bindValue(':aula', $id_aula, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    public function checkAsignacionExiste($id_usuario, $id_curso, $id_aula, $anio) {
        $query = "SELECT COUNT(*)
                  FROM docente_curso_aula
                  WHERE id_usuario = :user
                    AND id_curso = :curso
                    AND id_aula = :aula
                    AND anio = :anio";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user', $id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(':curso', $id_curso, PDO::PARAM_INT);
        $stmt->bindValue(':aula', $id_aula, PDO::PARAM_INT);
        $stmt->bindValue(':anio', $anio, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    public function guardar(AsignacionDocenteBean $asignacion) {
        $id = $asignacion->getIdDocenteCursoAula();
        $id_usuario = $asignacion->getIdUsuario();
        $id_curso = $asignacion->getIdCurso();
        $id_aula = $asignacion->getIdAula();
        $anio = $asignacion->getAnio();

        if ($id > 0) {
            $query = "UPDATE docente_curso_aula 
                      SET id_usuario=:user, id_curso=:curso, id_aula=:aula, anio=:anio 
                      WHERE id_docente_curso_aula=:id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        } else {
            $query = "INSERT INTO docente_curso_aula (id_usuario, id_curso, id_aula, anio) 
                      VALUES (:user, :curso, :aula, :anio)";
            $stmt = $this->db->prepare($query);
        }

        $stmt->bindValue(':user', $id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(':curso', $id_curso, PDO::PARAM_INT);
        $stmt->bindValue(':aula', $id_aula, PDO::PARAM_INT);
        $stmt->bindValue(':anio', $anio, PDO::PARAM_INT);

        $success = $stmt->execute();
        if ($success && $id <= 0) {
            $asignacion->setIdDocenteCursoAula((int)$this->db->lastInsertId());
        }
        return $success;
    }

    public function listarAsignacionesAnioActual() {
        $query = "SELECT dca.id_docente_curso_aula, u.nombres, u.apellidos, c.nombre_curso,
                         g.nombre_grado, s.nombre_seccion
                  FROM docente_curso_aula dca
                  JOIN usuarios u ON dca.id_usuario = u.id_usuario
                  JOIN cursos c ON dca.id_curso = c.id_curso
                  JOIN aulas_asignadas aa ON dca.id_aula = aa.id_aula
                  JOIN grados g ON aa.id_grado = g.id_grado
                  JOIN secciones s ON aa.id_seccion = s.id_seccion
                  WHERE dca.anio = YEAR(NOW())
                  ORDER BY u.apellidos, u.nombres, c.nombre_curso";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminar($id) {
        $query = "DELETE FROM docente_curso_aula WHERE id_docente_curso_aula = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
