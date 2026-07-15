<?php
// test_login.php
session_start();
require_once "vendor/autoload.php";

use Controladores\ControladorUsuarios;

// Probar con usuario demo
$user = 'demo';
$pass = 'demo'; // Cambia por la contraseña correcta
$token = 'test';
$conectar = 'ok';

ob_clean();
header('Content-Type: application/json');

$resultado = ControladorUsuarios::ctrIngresoUsuario($user, $pass, $token, $conectar);

// Si no hay salida, mostrar mensaje
if (ob_get_length() === 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No se obtuvo respuesta del controlador'
    ]);
}