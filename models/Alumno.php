<?php
// model/Alumno.php

class Alumno {
    private $db;

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

    public function __construct($db = null) {
        $this->db = $db;
    }

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

    // Métodos de Persistencia

    public function getById($id) {
        $query = "SELECT * FROM alumnos WHERE id_alumno = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $this->mapRowToModel($row);
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

    public function guardar() {
        $id = $this->id_alumno;
        $codigo = $this->codigo_estudiante;
        $nombres = $this->nombres;
        $apellidos = $this->apellidos;
        $dni = $this->dni;
        $fecha = $this->fecha_nacimiento;
        $telefono = $this->telefono;
        $nombre_apoderado = $this->nombre_apoderado;
        $telefono_apoderado = $this->telefono_apoderado;
        $email_apoderado = $this->email_apoderado;
        $id_aula = $this->id_aula;

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
                $this->setIdAlumno($alumno_id);
                // Generar acceso del Padre de Familia automáticamente
                // Buscar rol PadreFamilia
                $query_rol = "SELECT id_rol FROM roles WHERE nombre_rol = 'PadreFamilia' LIMIT 1";
                $stmt_rol = $this->db->prepare($query_rol);
                $stmt_rol->execute();
                $id_rol_padre = (int)$stmt_rol->fetchColumn();

                if ($id_rol_padre > 0) {
                    // Verificar si ya existe un padre con el mismo correo electrónico
                    $query_existente = "SELECT pf.id_padre, u.dni, pf.password_generada, u.nombres 
                                        FROM padres_familia pf 
                                        JOIN usuarios u ON pf.id_usuario = u.id_usuario 
                                        WHERE u.email = :email AND u.id_rol = :id_rol LIMIT 1";
                    $stmt_existente = $this->db->prepare($query_existente);
                    $stmt_existente->bindValue(':email', $email_apoderado, PDO::PARAM_STR);
                    $stmt_existente->bindValue(':id_rol', $id_rol_padre, PDO::PARAM_INT);
                    $stmt_existente->execute();
                    $padre_existente = $stmt_existente->fetch(PDO::FETCH_ASSOC);

                    if ($padre_existente) {
                        $padre_id = (int)$padre_existente['id_padre'];
                        $dni_padre = $padre_existente['dni'];
                        $password_padre = $padre_existente['password_generada'];
                        $nombre_apoderado = $padre_existente['nombres'];

                        // Insertar relación alumno_padre
                        $query_alumno_padre = "INSERT INTO alumno_padre (id_alumno, id_padre) VALUES (:id_alumno, :id_padre)";
                        $stmt_alumno_padre = $this->db->prepare($query_alumno_padre);
                        $stmt_alumno_padre->bindValue(':id_alumno', $alumno_id, PDO::PARAM_INT);
                        $stmt_alumno_padre->bindValue(':id_padre', $padre_id, PDO::PARAM_INT);
                        $stmt_alumno_padre->execute();

                        $resultado['padre_creado'] = true;
                        $resultado['mensaje'] = "✓ Alumno vinculado a Apoderado existente automáticamente";
                        $resultado['apoderado'] = $nombre_apoderado;
                        $resultado['dni_padre'] = $dni_padre;
                        $resultado['password'] = $password_padre;
                    } else {
                        // Crear nuevo padre
                        $password_padre = $this->generateRandomPassword();
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

    private function mapRowToModel(array $row) {
        $model = new Alumno($this->db);
        $model->setIdAlumno((int)$row['id_alumno']);
        $model->setCodigoEstudiante($row['codigo_estudiante']);
        $model->setNombres($row['nombres']);
        $model->setApellidos($row['apellidos']);
        $model->setDni($row['dni']);
        $model->setFechaNacimiento($row['fecha_nacimiento']);
        $model->setTelefono($row['telefono']);
        $model->setNombreApoderado($row['nombre_apoderado']);
        $model->setTelefonoApoderado($row['telefono_apoderado']);
        $model->setEmailApoderado($row['email_apoderado']);
        $model->setIdAula($row['id_aula'] !== null ? (int)$row['id_aula'] : null);
        $model->setActivo((int)$row['activo']);
        return $model;
    }
}
?>
