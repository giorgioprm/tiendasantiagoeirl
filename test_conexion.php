<?php
// test_conexion.php
require_once 'Conect/Conexion.php';

error_log("=== INICIANDO TEST CONEXION ===");

try {
    echo "<h2>Test de Conexión a Base de Datos</h2>";

    $conexion = Conect\Conexion::conectar();
    echo "<p style='color: green;'>✓ Conexión exitosa a la base de datos</p>";

    // Probar consulta directa
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = 'demo'");
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        echo "<p style='color: green;'>✓ Usuario encontrado: " . $resultado['usuario'] . "</p>";
        echo "<p>ID: " . $resultado['id'] . "</p>";
        echo "<p>Nombre: " . $resultado['nombre'] . "</p>";
        echo "<p>Estado: " . $resultado['estado'] . "</p>";
        echo "<p>Password en BD: " . $resultado['password'] . "</p>";

        // Probar encriptación
        $pass_test = 'demo';
        $encriptado_test = crypt($pass_test, '$2a$07$usesomesillystringforsalt$');
        echo "<p>Password 'demo' encriptado: " . $encriptado_test . "</p>";
        echo "<p>¿Coinciden?: " . ($resultado['password'] == $encriptado_test ? "SÍ" : "NO") . "</p>";
    } else {
        echo "<p style='color: red;'>✗ Usuario 'demo' NO encontrado</p>";
    }

    // Probar listar todos los usuarios
    echo "<h3>Todos los usuarios:</h3>";
    $stmt = $conexion->prepare("SELECT id, usuario, nombre, estado FROM usuarios");
    $stmt->execute();
    $todos_usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($todos_usuarios) {
        foreach ($todos_usuarios as $usuario) {
            echo "<p>- " . $usuario['usuario'] . " (" . $usuario['nombre'] . ") - Estado: " . $usuario['estado'] . "</p>";
        }
    } else {
        echo "<p>No hay usuarios en la base de datos</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error de conexión: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p>Test completado</p>";
