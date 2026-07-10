// controllers/LogoutController.php

require_once __DIR__ . '/../core/Database.php';

session_unset();
session_destroy();

header('Location: index.php?controller=login');
exit;
?>
