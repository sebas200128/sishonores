<?php
// bean/UsuarioBean.php

class UsuarioBean {
    private $id_usuario;
    private $nombres;
    private $apellidos;
    private $email;
    private $password_hash;
    private $dni;
    private $telefono;
    private $id_rol;
    private $activo;
    private $creado_en;

    // Getters y Setters
    public function getIdUsuario() { return $this->id_usuario; }
    public function setIdUsuario($id_usuario) { $this->id_usuario = $id_usuario; }

    public function getNombres() { return $this->nombres; }
    public function setNombres($nombres) { $this->nombres = $nombres; }

    public function getApellidos() { return $this->apellidos; }
    public function setApellidos($apellidos) { $this->apellidos = $apellidos; }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    public function getPasswordHash() { return $this->password_hash; }
    public function setPasswordHash($password_hash) { $this->password_hash = $password_hash; }

    public function getDni() { return $this->dni; }
    public function setDni($dni) { $this->dni = $dni; }

    public function getTelefono() { return $this->telefono; }
    public function setTelefono($telefono) { $this->telefono = $telefono; }

    public function getIdRol() { return $this->id_rol; }
    public function setIdRol($id_rol) { $this->id_rol = $id_rol; }

    public function getActivo() { return $this->activo; }
    public function setActivo($activo) { $this->activo = $activo; }

    public function getCreadoEn() { return $this->creado_en; }
    public function setCreadoEn($creado_en) { $this->creado_en = $creado_en; }
}
?>
