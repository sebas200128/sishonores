<?php
// dao/UsuarioDAO.php

require_once __DIR__ . '/../bean/UsuarioBean.php';

class UsuarioDAO {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getById($id) {
        $query = "SELECT * FROM usuarios WHERE id_usuario = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $this->mapRowToBean($row);
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
        return $stmt->fetch(PDO::FETCH_ASSOC); // Devolver array asociativo por conveniencia en login
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

    public function guardar(UsuarioBean $usuario) {
        $id = $usuario->getIdUsuario();
        $nombres = $usuario->getNombres();
        $apellidos = $usuario->getApellidos();
        $email = $usuario->getEmail();
        $dni = $usuario->getDni();
        $telefono = $usuario->getTelefono();
        $id_rol = $usuario->getIdRol();
        $pass_hash = $usuario->getPasswordHash();

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
            $usuario->setIdUsuario((int)$this->db->lastInsertId());
        }
        return $success;
    }

    public function eliminar($id) {
        $query = "DELETE FROM usuarios WHERE id_usuario = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Métodos específicos de Docente (con niveles)

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

    public function guardarDocenteConNiveles(UsuarioBean $usuario, array $niveles) {
        try {
            $this->db->beginTransaction();

            $this->guardar($usuario);
            $usuario_id = $usuario->getIdUsuario();

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

    private function mapRowToBean(array $row) {
        $bean = new UsuarioBean();
        $bean->setIdUsuario((int)$row['id_usuario']);
        $bean->setNombres($row['nombres']);
        $bean->setApellidos($row['apellidos']);
        $bean->setEmail($row['email']);
        $bean->setPasswordHash($row['password_hash']);
        $bean->setDni($row['dni']);
        $bean->setTelefono($row['telefono']);
        $bean->setIdRol((int)$row['id_rol']);
        $bean->setActivo((int)$row['activo']);
        $bean->setCreadoEn($row['creado_en']);
        return $bean;
    }
}
?>
