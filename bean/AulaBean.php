<?php
// bean/AulaBean.php

class AulaBean {
    private $id_aula;
    private $id_grado;
    private $id_seccion;
    private $anio;
    private $vacantes;

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
}
?>
