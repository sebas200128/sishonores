<?php
// config/database.php
class Database {
    private $host = "localhost";
    private $db_name = "honoresbd";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                                  $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

function app_base_path() {
    static $basePath = null;

    if ($basePath !== null) {
        return $basePath;
    }

    $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $projectRoot = realpath(__DIR__ . '/..');

    if ($documentRoot && $projectRoot && stripos($projectRoot, $documentRoot) === 0) {
        $relativePath = substr($projectRoot, strlen($documentRoot));
        $relativePath = str_replace('\\', '/', $relativePath);
        $basePath = '/' . trim($relativePath, '/');
        $basePath = $basePath === '/' ? '' : $basePath;
        return $basePath;
    }

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath = strpos($scriptName, '/sishonores/') === 0 ? '/sishonores' : '';
    return $basePath;
}

function app_url($path = '') {
    return app_base_path() . '/' . ltrim($path, '/');
}

// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
