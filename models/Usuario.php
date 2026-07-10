<?php
// model/Usuario.php

class Usuario {
    private $db;

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

    public function __construct($db = null) {
        $this->db = $db;
    }

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

    // Métodos de Persistencia

    public function getById($id) {
        $query = "SELECT * FROM usuarios WHERE id_usuario = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $this->mapRowToModel($row);
        }
        return null;
    }

    public function getByDni($dni) {
        $query = "SELECT u.*, r.nombre_rol 
                  FROM usuarios u 
                  JOIN roles r ON u.id_rol = r.id_rol 
                  WHERE u.dni = :dni AND u.activo = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':dni', $dni, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarTodos() {
        $query = "SELECT u.*, r.nombre_rol 
                  FROM usuarios u 
                  JOIN roles r ON u.id_rol = r.id_rol 
                  ORDER BY u.id_usuario DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkDniDuplicado($dni, $id) {
        $query = "SELECT COUNT(*) FROM usuarios WHERE dni = :dni AND id_usuario != :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':dni', $dni, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    public function checkEmailDuplicado($email, $id) {
        $query = "SELECT COUNT(*) FROM usuarios WHERE email = :email AND id_usuario != :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    public function getRolIdByNombre($nombre_rol) {
        $query = "SELECT id_rol FROM roles WHERE nombre_rol = :nombre LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nombre', $nombre_rol, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function guardar() {
        $id = $this->id_usuario;
        $nombres = $this->nombres;
        $apellidos = $this->apellidos;
        $email = $this->email;
        $dni = $this->dni;
        $telefono = $this->telefono;
        $id_rol = $this->id_rol;
        $pass_hash = $this->password_hash;

        if ($id > 0) {
            if ($pass_hash !== null && $pass_hash !== '') {
                $query = "UPDATE usuarios 
                          SET nombres=:nombres, apellidos=:apellidos, email=:email, dni=:dni, 
                              telefono=:telefono, id_rol=:id_rol, password_hash=:password 
                          WHERE id_usuario=:id";
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':password', $pass_hash, PDO::PARAM_STR);
            } else {
                $query = "UPDATE usuarios 
                          SET nombres=:nombres, apellidos=:apellidos, email=:email, dni=:dni, 
                              telefono=:telefono, id_rol=:id_rol 
                          WHERE id_usuario=:id";
                $stmt = $this->db->prepare($query);
            }
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        } else {
            $query = "INSERT INTO usuarios (nombres, apellidos, email, password_hash, dni, telefono, id_rol, activo) 
                      VALUES (:nombres, :apellidos, :email, :password, :dni, :telefono, :id_rol, 1)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':password', $pass_hash, PDO::PARAM_STR);
        }

        $stmt->bindValue(':nombres', $nombres, PDO::PARAM_STR);
        $stmt->bindValue(':apellidos', $apellidos, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':dni', $dni, PDO::PARAM_STR);
        $stmt->bindValue(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindValue(':id_rol', $id_rol, PDO::PARAM_INT);
        
        $success = $stmt->execute();
        if ($success && $id <= 0) {
            $this->setIdUsuario((int)$this->db->lastInsertId());
        }
        return $success;
    }

    public function eliminar($id) {
        $query = "DELETE FROM usuarios WHERE id_usuario = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function listarDocentes() {
        $query = "SELECT u.*, GROUP_CONCAT(n.nombre_nivel ORDER BY n.id_nivel SEPARATOR ', ') AS nombres_niveles
                  FROM usuarios u
                  JOIN roles r ON u.id_rol = r.id_rol
                  LEFT JOIN docente_nivel dn ON u.id_usuario = dn.id_usuario
                  LEFT JOIN niveles n ON dn.id_nivel = n.id_nivel
                  WHERE r.nombre_rol = 'Docente'
                  GROUP BY u.id_usuario
                  ORDER BY u.id_usuario DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDocenteNiveles($id_usuario) {
        $query = "SELECT id_nivel FROM docente_nivel WHERE id_usuario = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function guardarDocenteConNiveles(array $niveles) {
        try {
            $this->db->beginTransaction();

            $this->guardar();
            $usuario_id = $this->getIdUsuario();

            // Limpiar niveles anteriores si era una edición
            $query_del = "DELETE FROM docente_nivel WHERE id_usuario = :id";
            $stmt_del = $this->db->prepare($query_del);
            $stmt_del->bindValue(':id', $usuario_id, PDO::PARAM_INT);
            $stmt_del->execute();

            // Insertar nuevos niveles
            $query_ins = "INSERT INTO docente_nivel (id_usuario, id_nivel) VALUES (:id_user, :id_nivel)";
            $stmt_ins = $this->db->prepare($query_ins);
            $stmt_ins->bindValue(':id_user', $usuario_id, PDO::PARAM_INT);

            foreach ($niveles as $nivel_id) {
                $stmt_ins->bindValue(':id_nivel', (int)$nivel_id, PDO::PARAM_INT);
                $stmt_ins->execute();
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

    public function eliminarDocenteConAsociaciones($id_usuario) {
        try {
            $this->db->beginTransaction();

            // Eliminar de docente_nivel
            $stmt = $this->db->prepare("DELETE FROM docente_nivel WHERE id_usuario = :id");
            $stmt->bindValue(':id', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();

            // Eliminar asignaciones de docente_curso_aula
            $stmt = $this->db->prepare("DELETE FROM docente_curso_aula WHERE id_usuario = :id");
            $stmt->bindValue(':id', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();

            // Eliminar de usuarios
            $this->eliminar($id_usuario);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    private function mapRowToModel(array $row) {
        $model = new Usuario($this->db);
        $model->setIdUsuario((int)$row['id_usuario']);
        $model->setNombres($row['nombres']);
        $model->setApellidos($row['apellidos']);
        $model->setEmail($row['email']);
        $model->setPasswordHash($row['password_hash']);
        $model->setDni($row['dni']);
        $model->setTelefono($row['telefono']);
        $model->setIdRol((int)$row['id_rol']);
        $model->setActivo((int)$row['activo']);
        $model->setCreadoEn($row['creado_en']);
        return $model;
    }
}
?>
