<?php
// controller/LogoutController.php

require_once __DIR__ . '/../util/Database.php';

session_unset();
session_destroy();

header('Location: ../vista/login.php');
exit;
?>
