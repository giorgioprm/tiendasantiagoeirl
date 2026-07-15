<?php
// test_login_final.php
require_once 'vendor/autoload.php';

echo "<h1>Test Login Final</h1>";

// Simular llamada al controlador
$user = 'demo';
$pass = 'demo';

try {
    // Llamar directamente al método del controlador
    Controladores\ControladorUsuarios::ctrIngresoUsuario($user, $pass, 'fake_token', 'ok');
    
    echo "<p>✅ Método ejecutado sin errores</p>";
    
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . " Línea: " . $e->getLine() . "</p>";
}