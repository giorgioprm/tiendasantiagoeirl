<?php
// login_test.php
session_start();
require_once "vendor/autoload.php";

use Controladores\ControladorUsuarios;

$user = 'demo'; // Cambia por tu usuario
$pass = 'demo'; // Cambia por tu contraseña

ob_clean();
header('Content-Type: application/json');

// Ejecutar login
ControladorUsuarios::ctrIngresoUsuario($user, $pass, 'test', 'ok');