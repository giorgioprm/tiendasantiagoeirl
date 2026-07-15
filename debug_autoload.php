<?php
// debug_autoload.php
require_once 'vendor/autoload.php';

echo "<h1>Debug Autoload</h1>";

// Verificar si las clases se cargan
$clases = [
    'Modelos\\ModeloUsuarios',
    'Conect\\Conexion', 
    'Controladores\\ControladorUsuarios'
];

foreach ($clases as $clase) {
    if (class_exists($clase)) {
        echo "✅ Clase <strong>$clase</strong> cargada correctamente<br>";
    } else {
        echo "❌ Clase <strong>$clase</strong> NO encontrada<br>";
    }
}

// Probar el modelo
echo "<h2>Probando ModeloUsuarios</h2>";
try {
    $resultado = Modelos\ModeloUsuarios::mdlMostrarUsuarios('usuarios', 'usuario', 'demo');
    if ($resultado) {
        echo "✅ Modelo funciona - Usuario: " . $resultado['usuario'] . "<br>";
    } else {
        echo "❌ Modelo devuelve NULL<br>";
    }
} catch (Error $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}