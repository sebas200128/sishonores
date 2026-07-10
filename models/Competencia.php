<?php
// model/Competencia.php

class Competencia {
    private $id_competencia;
    private $id_curso;
    private $nombre_competencia;

    // Getters y Setters
    public function getIdCompetencia() { return $this->id_competencia; }
    public function setIdCompetencia($id_competencia) { $this->id_competencia = $id_competencia; }

    public function getIdCurso() { return $this->id_curso; }
    public function setIdCurso($id_curso) { $this->id_curso = $id_curso; }

    public function getNombreCompetencia() { return $this->nombre_competencia; }
    public function setNombreCompetencia($nombre_competencia) { $this->nombre_competencia = $nombre_competencia; }
}
?>
