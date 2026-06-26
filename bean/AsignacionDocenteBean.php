<?php
// bean/AsignacionDocenteBean.php

class AsignacionDocenteBean {
    private $id_docente_curso_aula;
    private $id_usuario;
    private $id_curso;
    private $id_aula;
    private $anio;

    // Getters y Setters
    public function getIdDocenteCursoAula() { return $this->id_docente_curso_aula; }
    public function setIdDocenteCursoAula($id_docente_curso_aula) { $this->id_docente_curso_aula = $id_docente_curso_aula; }

    public function getIdUsuario() { return $this->id_usuario; }
    public function setIdUsuario($id_usuario) { $this->id_usuario = $id_usuario; }

    public function getIdCurso() { return $this->id_curso; }
    public function setIdCurso($id_curso) { $this->id_curso = $id_curso; }

    public function getIdAula() { return $this->id_aula; }
    public function setIdAula($id_aula) { $this->id_aula = $id_aula; }

    public function getAnio() { return $this->anio; }
    public function setAnio($anio) { $this->anio = $anio; }
}
?>
