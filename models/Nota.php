<?php
// model/Nota.php

class Nota {
    private $db;

    private $id_nota;
    private $id_alumno;
    private $id_competencia;
    private $id_docente_curso_aula;
    private $bimestre;
    private $nota;
    private $observacion;
    private $creado_en;

    public function __construct($db = null) {
        $this->db = $db;
    }

    // Getters y Setters
    public function getIdNota() { return $this->id_nota; }
    public function setIdNota($id_nota) { $this->id_nota = $id_nota; }

    public function getIdAlumno() { return $this->id_alumno; }
    public function setIdAlumno($id_alumno) { $this->id_alumno = $id_alumno; }

    public function getIdCompetencia() { return $this->id_competencia; }
    public function setIdCompetencia($id_competencia) { $this->id_competencia = $id_competencia; }

    public function getIdDocenteCursoAula() { return $this->id_docente_curso_aula; }
    public function setIdDocenteCursoAula($id_docente_curso_aula) { $this->id_docente_curso_aula = $id_docente_curso_aula; }

    public function getBimestre() { return $this->bimestre; }
    public function setBimestre($bimestre) { $this->bimestre = $bimestre; }

    public function getNota() { return $this->nota; }
    public function setNota($nota) { $this->nota = $nota; }

    public function getObservacion() { return $this->observacion; }
    public function setObservacion($observacion) { $this->observacion = $observacion; }

    public function getCreadoEn() { return $this->creado_en; }
    public function setCreadoEn($creado_en) { $this->creado_en = $creado_en; }

    // Métodos de Persistencia

    public function obtenerAlumnosYNotasPorCursoBimestre($id_docente_curso_aula, $bimestre) {
        // 1. Obtener competencias del curso
        $query_comp = "SELECT id_competencia, nombre_competencia 
                       FROM competencias 
                       WHERE id_curso = (
                           SELECT id_curso FROM docente_curso_aula WHERE id_docente_curso_aula = :curso
                       ) 
                       ORDER BY id_competencia LIMIT 3";
        $stmt_comp = $this->db->prepare($query_comp);
        $stmt_comp->bindValue(':curso', $id_docente_curso_aula, PDO::PARAM_INT);
        $stmt_comp->execute();
        $competencias = $stmt_comp->fetchAll(PDO::FETCH_ASSOC);

        // 2. Obtener alumnos del aula
        $query_alumnos = "SELECT a.id_alumno, a.nombres, a.apellidos 
                          FROM alumnos a 
                          JOIN docente_curso_aula dca ON dca.id_aula = a.id_aula 
                          WHERE dca.id_docente_curso_aula = :curso 
                            AND a.activo = 1 
                          ORDER BY a.apellidos, a.nombres";
        $stmt_alumnos = $this->db->prepare($query_alumnos);
        $stmt_alumnos->bindValue(':curso', $id_docente_curso_aula, PDO::PARAM_INT);
        $stmt_alumnos->execute();
        $alumnos = $stmt_alumnos->fetchAll(PDO::FETCH_ASSOC);

        $alumnos_data = [];
        foreach ($alumnos as $alumno) {
            $id_alumno = (int)$alumno['id_alumno'];
            $notas = [];
            $observacion = '';

            foreach ($competencias as $idx => $comp) {
                $query_nota = "SELECT nota, observacion 
                               FROM notas 
                               WHERE id_alumno = :alumno 
                                 AND id_competencia = :comp 
                                 AND id_docente_curso_aula = :curso 
                                 AND bimestre = :bimestre";
                $stmt_nota = $this->db->prepare($query_nota);
                $stmt_nota->bindValue(':alumno', $id_alumno, PDO::PARAM_INT);
                $stmt_nota->bindValue(':comp', (int)$comp['id_competencia'], PDO::PARAM_INT);
                $stmt_nota->bindValue(':curso', $id_docente_curso_aula, PDO::PARAM_INT);
                $stmt_nota->bindValue(':bimestre', $bimestre, PDO::PARAM_INT);
                $stmt_nota->execute();
                $nota_data = $stmt_nota->fetch(PDO::FETCH_ASSOC);

                $valor_nota = $nota_data ? $nota_data['nota'] : '';
                $notas[] = $valor_nota;

                // Capturar observación en la primera iteración o la que corresponda
                if ($idx === 0) {
                    $observacion = $nota_data ? $nota_data['observacion'] : '';
                }
            }

            // Calcular promedio
            $suma = 0;
            $cantidad = 0;
            foreach ($notas as $n) {
                if ($n !== '' && $n !== null && (float)$n > 0) {
                    $suma += (float)$n;
                    $cantidad++;
                }
            }
            $promedio = $cantidad > 0 ? round($suma / $cantidad, 1) : 0;

            $alumnos_data[] = [
                'id_alumno' => $id_alumno,
                'nombres' => $alumno['nombres'],
                'apellidos' => $alumno['apellidos'],
                'notas' => $notas,
                'promedio' => $promedio,
                'observacion' => $observacion
            ];
        }

        return [
            'competencias' => array_column($competencias, 'nombre_competencia'),
            'alumnos' => $alumnos_data
        ];
    }

    public function guardarNotasDeAlumnos($id_docente_curso_aula, $bimestre, array $datos) {
        // Obtener IDs de competencias correspondientes
        $query_comp = "SELECT id_competencia 
                       FROM competencias 
                       WHERE id_curso = (
                           SELECT id_curso FROM docente_curso_aula WHERE id_docente_curso_aula = :curso
                       ) 
                       ORDER BY id_competencia LIMIT 3";
        $stmt_comp = $this->db->prepare($query_comp);
        $stmt_comp->bindValue(':curso', $id_docente_curso_aula, PDO::PARAM_INT);
        $stmt_comp->execute();
        $competencias = $stmt_comp->fetchAll(PDO::FETCH_ASSOC);

        if (empty($competencias)) {
            throw new Exception("El curso no tiene competencias configuradas.");
        }

        try {
            $this->db->beginTransaction();

            foreach ($datos as $a) {
                $id_alumno = (int)$a['id_alumno'];
                $notas = $a['notas'];
                $observacion = $a['observacion'] ?? '';

                foreach ($competencias as $idx => $comp) {
                    $id_comp = (int)$comp['id_competencia'];
                    $nota = isset($notas[$idx]) && $notas[$idx] !== '' ? (float)$notas[$idx] : 0;

                    // Chequear si existe la nota
                    $query_check = "SELECT id_nota FROM notas 
                                    WHERE id_alumno=:alumno AND id_competencia=:comp 
                                      AND id_docente_curso_aula=:curso AND bimestre=:bimestre";
                    $stmt_check = $this->db->prepare($query_check);
                    $stmt_check->bindValue(':alumno', $id_alumno, PDO::PARAM_INT);
                    $stmt_check->bindValue(':comp', $id_comp, PDO::PARAM_INT);
                    $stmt_check->bindValue(':curso', $id_docente_curso_aula, PDO::PARAM_INT);
                    $stmt_check->bindValue(':bimestre', $bimestre, PDO::PARAM_INT);
                    $stmt_check->execute();
                    $id_nota = (int)$stmt_check->fetchColumn();

                    if ($id_nota > 0) {
                        // Actualizar
                        $query = "UPDATE notas SET nota=:nota, observacion=:obs 
                                  WHERE id_nota=:id_nota";
                        $stmt = $this->db->prepare($query);
                        $stmt->bindValue(':id_nota', $id_nota, PDO::PARAM_INT);
                    } else {
                        // Insertar
                        $query = "INSERT INTO notas (id_alumno, id_competencia, id_docente_curso_aula, bimestre, nota, observacion) 
                                  VALUES (:alumno, :comp, :curso, :bimestre, :nota, :obs)";
                        $stmt = $this->db->prepare($query);
                        $stmt->bindValue(':alumno', $id_alumno, PDO::PARAM_INT);
                        $stmt->bindValue(':comp', $id_comp, PDO::PARAM_INT);
                        $stmt->bindValue(':curso', $id_docente_curso_aula, PDO::PARAM_INT);
                        $stmt->bindValue(':bimestre', $bimestre, PDO::PARAM_INT);
                    }

                    $stmt->bindValue(':nota', $nota);
                    $stmt->bindValue(':obs', $observacion, PDO::PARAM_STR);
                    $stmt->execute();
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function listarCompetenciasPorDocenteCursoAula($id_docente_curso_aula) {
        $query = "SELECT c.* 
                  FROM competencias c 
                  JOIN docente_curso_aula dca ON c.id_curso = dca.id_curso 
                  WHERE dca.id_docente_curso_aula = :curso";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':curso', $id_docente_curso_aula, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregarCompetencia($id_docente_curso_aula, $nombre_competencia) {
        $query_curso = "SELECT id_curso FROM docente_curso_aula WHERE id_docente_curso_aula = :curso";
        $stmt_curso = $this->db->prepare($query_curso);
        $stmt_curso->bindValue(':curso', $id_docente_curso_aula, PDO::PARAM_INT);
        $stmt_curso->execute();
        $id_curso = (int)$stmt_curso->fetchColumn();

        if ($id_curso > 0 && !empty($nombre_competencia)) {
            $query = "INSERT INTO competencias (id_curso, nombre_competencia) VALUES (:curso, :nombre)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':curso', $id_curso, PDO::PARAM_INT);
            $stmt->bindValue(':nombre', $nombre_competencia, PDO::PARAM_STR);
            return $stmt->execute();
        }
        return false;
    }

    public function eliminarCompetencia($id_competencia) {
        $query = "DELETE FROM competencias WHERE id_competencia = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id_competencia, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function actualizarCompetencia($id_competencia, $nombre_competencia) {
        $query = "UPDATE competencias SET nombre_competencia = :nombre WHERE id_competencia = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id_competencia, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre_competencia, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function obtenerComentarioPorAlumnoYCurso($id_alumno, $id_docente_curso_aula, $bimestre = 1) {
        $query = "SELECT observacion FROM notas 
                  WHERE id_alumno = :alumno 
                    AND id_competencia = (
                        SELECT MIN(id_competencia) FROM competencias 
                        WHERE id_curso = (
                            SELECT id_curso FROM docente_curso_aula WHERE id_docente_curso_aula = :curso
                        )
                    ) 
                    AND id_docente_curso_aula = :curso 
                    AND bimestre = :bimestre";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':alumno', $id_alumno, PDO::PARAM_INT);
        $stmt->bindValue(':curso', $id_docente_curso_aula, PDO::PARAM_INT);
        $stmt->bindValue(':bimestre', $bimestre, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() ?: '';
    }

    public function guardarComentarioPorAlumnoYCurso($id_alumno, $id_docente_curso_aula, $bimestre, $comentario) {
        // Obtener la primera competencia del curso
        $query_comp = "SELECT MIN(c.id_competencia) 
                       FROM competencias c 
                       JOIN docente_curso_aula dca ON c.id_curso = dca.id_curso 
                       WHERE dca.id_docente_curso_aula = :curso";
        $stmt_comp = $this->db->prepare($query_comp);
        $stmt_comp->bindValue(':curso', $id_docente_curso_aula, PDO::PARAM_INT);
        $stmt_comp->execute();
        $id_competencia = (int)$stmt_comp->fetchColumn();

        if ($id_competencia <= 0) {
            throw new Exception("El curso no tiene competencias registradas.");
        }

        // Chequear si existe la nota
        $query_check = "SELECT id_nota FROM notas 
                        WHERE id_alumno = :alumno 
                          AND id_competencia = :competencia 
                          AND id_docente_curso_aula = :curso 
                          AND bimestre = :bimestre";
        $stmt_check = $this->db->prepare($query_check);
        $stmt_check->bindValue(':alumno', $id_alumno, PDO::PARAM_INT);
        $stmt_check->bindValue(':competencia', $id_competencia, PDO::PARAM_INT);
        $stmt_check->bindValue(':curso', $id_docente_curso_aula, PDO::PARAM_INT);
        $stmt_check->bindValue(':bimestre', $bimestre, PDO::PARAM_INT);
        $stmt_check->execute();
        $id_nota = (int)$stmt_check->fetchColumn();

        if ($id_nota > 0) {
            $query = "UPDATE notas SET observacion = :obs WHERE id_nota = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':obs', $comentario, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id_nota, PDO::PARAM_INT);
        } else {
            $nota = 0;
            $query = "INSERT INTO notas (id_alumno, id_competencia, id_docente_curso_aula, bimestre, nota, observacion) 
                      VALUES (:alumno, :competencia, :curso, :bimestre, :nota, :obs)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':alumno', $id_alumno, PDO::PARAM_INT);
            $stmt->bindValue(':competencia', $id_competencia, PDO::PARAM_INT);
            $stmt->bindValue(':curso', $id_docente_curso_aula, PDO::PARAM_INT);
            $stmt->bindValue(':bimestre', $bimestre, PDO::PARAM_INT);
            $stmt->bindValue(':nota', $nota);
            $stmt->bindValue(':obs', $comentario, PDO::PARAM_STR);
        }

        return $stmt->execute();
    }
}
?>
