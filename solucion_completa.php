<?php
// solucion_completa.php
require_once 'Conect/Conexion.php';

$conexion = Conect\Conexion::conectar();

// 1. Resetear password de demo
$pass = 'demo';
$password_correcto = crypt($pass, '$2a$07$usesomesillystringforsalt$');

$stmt = $conexion->prepare("UPDATE usuarios SET password = :pass WHERE usuario = 'demo'");
$stmt->bindParam(":pass", $password_correcto);

if ($stmt->execute()) {
    echo "✅ Password de 'demo' reseteado a 'demo'<br>";
} else {
    echo "❌ Error reset password demo<br>";
}

// 2. Verificar
$stmt = $conexion->prepare("SELECT usuario, password FROM usuarios WHERE usuario = 'demo'");
$stmt->execute();
$usuario = $stmt->fetch();

echo "Password en BD: " . $usuario['password'] . "<br>";
echo "Password generado: " . $password_correcto . "<br>";
echo "¿Coinciden?: " . ($usuario['password'] == $password_correcto ? "✅ SÍ" : "❌ NO") . "<br>";

if ($usuario['password'] == $password_correcto) {
    echo "<h2 style='color: green;'>🎉 ¡LISTO! Ahora puedes loguearte con:</h2>";
    echo "<p>Usuario: <strong>demo</strong></p>";
    echo "<p>Contraseña: <strong>demo</strong></p>";
    echo "<a href='login' style='font-size: 20px;'>➡️ Ir al Login</a>";
}
