<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: vista/dashboard.php');
} else {
    header('Location: vista/login.php');
}
exit;
?>