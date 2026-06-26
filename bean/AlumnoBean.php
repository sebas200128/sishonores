<?php
// bean/AlumnoBean.php

class AlumnoBean {
    private $id_alumno;
    private $codigo_estudiante;
    private $nombres;
    private $apellidos;
    private $dni;
    private $fecha_nacimiento;
    private $telefono;
    private $nombre_apoderado;
    private $telefono_apoderado;
    private $email_apoderado;
    private $id_aula;
    private $activo;

    // Getters y Setters
    public function getIdAlumno() { return $this->id_alumno; }
    public function setIdAlumno($id_alumno) { $this->id_alumno = $id_alumno; }

    public function getCodigoEstudiante() { return $this->codigo_estudiante; }
    public function setCodigoEstudiante($codigo_estudiante) { $this->codigo_estudiante = $codigo_estudiante; }

    public function getNombres() { return $this->nombres; }
    public function setNombres($nombres) { $this->nombres = $nombres; }

    public function getApellidos() { return $this->apellidos; }
    public function setApellidos($apellidos) { $this->apellidos = $apellidos; }

    public function getDni() { return $this->dni; }
    public function setDni($dni) { $this->dni = $dni; }

    public function getFechaNacimiento() { return $this->fecha_nacimiento; }
    public function setFechaNacimiento($fecha_nacimiento) { $this->fecha_nacimiento = $fecha_nacimiento; }

    public function getTelefono() { return $this->telefono; }
    public function setTelefono($telefono) { $this->telefono = $telefono; }

    public function getNombreApoderado() { return $this->nombre_apoderado; }
    public function setNombreApoderado($nombre_apoderado) { $this->nombre_apoderado = $nombre_apoderado; }

    public function getTelefonoApoderado() { return $this->telefono_apoderado; }
    public function setTelefonoApoderado($telefono_apoderado) { $this->telefono_apoderado = $telefono_apoderado; }

    public function getEmailApoderado() { return $this->email_apoderado; }
    public function setEmailApoderado($email_apoderado) { $this->email_apoderado = $email_apoderado; }

    public function getIdAula() { return $this->id_aula; }
    public function setIdAula($id_aula) { $this->id_aula = $id_aula; }

    public function getActivo() { return $this->activo; }
    public function setActivo($activo) { $this->activo = $activo; }
}
?>
