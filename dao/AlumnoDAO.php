<?php
// dao/AlumnoDAO.php

require_once __DIR__ . '/../bean/AlumnoBean.php';

class AlumnoDAO {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getById($id) {
        $query = "SELECT * FROM alumnos WHERE id_alumno = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $this->mapRowToBean($row);
        }
        return null;
    }

    public function listarTodos() {
        $query = "SELECT a.*, g.nombre_grado, s.nombre_seccion, n.nombre_nivel,
                         COALESCE(pf.password_generada, 'Sin generar') as contraseña_padre
                  FROM alumnos a
                  LEFT JOIN alumno_padre ap ON a.id_alumno = ap.id_alumno
                  LEFT JOIN padres_familia pf ON ap.id_padre = pf.id_padre
                  LEFT JOIN aulas_asignadas aa ON a.id_aula = aa.id_aula
                  LEFT JOIN grados g ON aa.id_grado = g.id_grado
                  LEFT JOIN secciones s ON aa.id_seccion = s.id_seccion
                  LEFT JOIN niveles n ON g.id_nivel = n.id_nivel
                  WHERE a.activo = 1
                  ORDER BY a.id_alumno DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkDniDuplicado($dni, $id) {
        $query = "SELECT COUNT(*) FROM alumnos WHERE dni = :dni AND id_alumno != :id AND activo = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':dni', $dni, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    public function checkCodigoDuplicado($codigo, $id) {
        $query = "SELECT COUNT(*) FROM alumnos WHERE codigo_estudiante = :codigo AND id_alumno != :id AND activo = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Guarda o actualiza un alumno.
     * Si es una inserción (id <= 0), crea de manera transaccional al padre de familia.
     * 
     * @param AlumnoBean $alumno
     * @return array Resultado con información sobre el éxito y los datos del padre creado si aplica.
     */
    public function guardar(AlumnoBean $alumno) {
        $id = $alumno->getIdAlumno();
        $codigo = $alumno->getCodigoEstudiante();
        $nombres = $alumno->getNombres();
        $apellidos = $alumno->getApellidos();
        $dni = $alumno->getDni();
        $fecha = $alumno->getFechaNacimiento();
        $telefono = $alumno->getTelefono();
        $nombre_apoderado = $alumno->getNombreApoderado();
        $telefono_apoderado = $alumno->getTelefonoApoderado();
        $email_apoderado = $alumno->getEmailApoderado();
        $id_aula = $alumno->getIdAula();

        try {
            $this->db->beginTransaction();

            if ($id > 0) {
                // Actualizar
                $query = "UPDATE alumnos 
                          SET codigo_estudiante=:codigo, nombres=:nombres, apellidos=:apellidos, dni=:dni, 
                              fecha_nacimiento=:fecha, telefono=:telefono, nombre_apoderado=:apoderado, 
                              telefono_apoderado=:tel_apo, email_apoderado=:email_apo, id_aula=:aula 
                          WHERE id_alumno=:id";
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            } else {
                // Insertar
                $query = "INSERT INTO alumnos (codigo_estudiante, nombres, apellidos, dni, fecha_nacimiento, 
                                              telefono, nombre_apoderado, telefono_apoderado, email_apoderado, id_aula, activo) 
                          VALUES (:codigo, :nombres, :apellidos, :dni, :fecha, :telefono, :apoderado, :tel_apo, :email_apo, :aula, 1)";
                $stmt = $this->db->prepare($query);
            }

            $stmt->bindValue(':codigo', $codigo, PDO::PARAM_STR);
            $stmt->bindValue(':nombres', $nombres, PDO::PARAM_STR);
            $stmt->bindValue(':apellidos', $apellidos, PDO::PARAM_STR);
            $stmt->bindValue(':dni', $dni, PDO::PARAM_STR);
            $stmt->bindValue(':fecha', $fecha, PDO::PARAM_STR);
            $stmt->bindValue(':telefono', $telefono, PDO::PARAM_STR);
            $stmt->bindValue(':apoderado', $nombre_apoderado, PDO::PARAM_STR);
            $stmt->bindValue(':tel_apo', $telefono_apoderado, PDO::PARAM_STR);
            $stmt->bindValue(':email_apo', $email_apoderado, PDO::PARAM_STR);
            $stmt->bindValue(':aula', $id_aula !== '' ? (int)$id_aula : null, $id_aula !== '' ? PDO::PARAM_INT : PDO::PARAM_NULL);
            
            $stmt->execute();

            $resultado = ['success' => true];

            if ($id <= 0) {
                $alumno_id = (int)$this->db->lastInsertId();
                $alumno->setIdAlumno($alumno_id);

                // Generar acceso del Padre de Familia automáticamente
                $password_padre = $this->generateRandomPassword();

                // Buscar rol PadreFamilia
                $query_rol = "SELECT id_rol FROM roles WHERE nombre_rol = 'PadreFamilia' LIMIT 1";
                $stmt_rol = $this->db->prepare($query_rol);
                $stmt_rol->execute();
                $id_rol_padre = (int)$stmt_rol->fetchColumn();

                if ($id_rol_padre > 0) {
                    $dni_padre = $dni; // El DNI del estudiante es el usuario del padre
                    $hash = password_hash($password_padre, PASSWORD_DEFAULT);
                    $apellido_apoderado = 'Apoderado';

                    // Insertar usuario general
                    $query_usuario = "INSERT INTO usuarios (nombres, apellidos, email, dni, telefono, id_rol, password_hash, activo) 
                                      VALUES (:nombres, :apellidos, :email, :dni, :telefono, :id_rol, :password, 1)";
                    $stmt_usuario = $this->db->prepare($query_usuario);
                    $stmt_usuario->bindValue(':nombres', $nombre_apoderado, PDO::PARAM_STR);
                    $stmt_usuario->bindValue(':apellidos', $apellido_apoderado, PDO::PARAM_STR);
                    $stmt_usuario->bindValue(':email', $email_apoderado, PDO::PARAM_STR);
                    $stmt_usuario->bindValue(':dni', $dni_padre, PDO::PARAM_STR);
                    $stmt_usuario->bindValue(':telefono', $telefono_apoderado, PDO::PARAM_STR);
                    $stmt_usuario->bindValue(':id_rol', $id_rol_padre, PDO::PARAM_INT);
                    $stmt_usuario->bindValue(':password', $hash, PDO::PARAM_STR);
                    $stmt_usuario->execute();

                    $usuario_id = (int)$this->db->lastInsertId();

                    // Insertar padre de familia
                    $query_padre = "INSERT INTO padres_familia (id_usuario, password_generada) VALUES (:id_usuario, :password)";
                    $stmt_padre = $this->db->prepare($query_padre);
                    $stmt_padre->bindValue(':id_usuario', $usuario_id, PDO::PARAM_INT);
                    $stmt_padre->bindValue(':password', $password_padre, PDO::PARAM_STR);
                    $stmt_padre->execute();

                    $padre_id = (int)$this->db->lastInsertId();

                    // Insertar relación alumno_padre
                    $query_alumno_padre = "INSERT INTO alumno_padre (id_alumno, id_padre) VALUES (:id_alumno, :id_padre)";
                    $stmt_alumno_padre = $this->db->prepare($query_alumno_padre);
                    $stmt_alumno_padre->bindValue(':id_alumno', $alumno_id, PDO::PARAM_INT);
                    $stmt_alumno_padre->bindValue(':id_padre', $padre_id, PDO::PARAM_INT);
                    $stmt_alumno_padre->execute();

                    $resultado['padre_creado'] = true;
                    $resultado['mensaje'] = "✓ Acceso Padre de Familia generado automáticamente";
                    $resultado['apoderado'] = $nombre_apoderado;
                    $resultado['dni_padre'] = $dni_padre;
                    $resultado['password'] = $password_padre;
                }
            }

            $this->db->commit();
            return $resultado;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function eliminar($id) {
        $query = "UPDATE alumnos SET activo = 0 WHERE id_alumno = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function generateRandomPassword($length = 8) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $password;
    }

    private function mapRowToBean(array $row) {
        $bean = new AlumnoBean();
        $bean->setIdAlumno((int)$row['id_alumno']);
        $bean->setCodigoEstudiante($row['codigo_estudiante']);
        $bean->setNombres($row['nombres']);
        $bean->setApellidos($row['apellidos']);
        $bean->setDni($row['dni']);
        $bean->setFechaNacimiento($row['fecha_nacimiento']);
        $bean->setTelefono($row['telefono']);
        $bean->setNombreApoderado($row['nombre_apoderado']);
        $bean->setTelefonoApoderado($row['telefono_apoderado']);
        $bean->setEmailApoderado($row['email_apoderado']);
        $bean->setIdAula($row['id_aula'] !== null ? (int)$row['id_aula'] : null);
        $bean->setActivo((int)$row['activo']);
        return $bean;
    }
}
?>
