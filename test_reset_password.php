<?php
// test_reset_password.php
require_once 'Conect/Conexion.php';

// Probar login después del reset
$pass = 'demo';
$encriptado = crypt($pass, '$2a$07$usesomesillystringforsalt$');

echo "Password a probar: $pass<br>";
echo "Password encriptado: $encriptado<br>";

$conexion = Conect\Conexion::conectar();
$stmt = $conexion->prepare("SELECT password FROM usuarios WHERE usuario = 'demo'");
$stmt->execute();
$usuario = $stmt->fetch();

echo "Password en BD: " . $usuario['password'] . "<br>";
echo "¿Coinciden?: " . ($usuario['password'] == $encriptado ? "SÍ ✅" : "NO ❌") . "<br>";

if ($usuario['password'] == $encriptado) {
    echo "<h2 style='color: green;'>¡LISTO! Ahora puedes loguearte con usuario: demo, contraseña: demo</h2>";
} else {
    echo "<h2 style='color: red;'>Aún no coinciden, necesitamos resetear la contraseña</h2>";
}