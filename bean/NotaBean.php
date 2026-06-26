<?php
// bean/NotaBean.php

class NotaBean {
    private $id_nota;
    private $id_alumno;
    private $id_competencia;
    private $id_docente_curso_aula;
    private $bimestre;
    private $nota;
    private $observacion;
    private $creado_en;

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
}
?>
