<?php
// bean/CursoBean.php

class CursoBean {
    private $id_curso;
    private $nombre_curso;
    private $codigo_curso;
    private $horas_semanales;
    private $id_nivel;

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
}
?>
