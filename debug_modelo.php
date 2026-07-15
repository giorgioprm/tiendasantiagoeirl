<?php
// debug_modelo.php
error_log("=== DEBUG MODELO INICIADO ===");

// Verificar si los archivos existen
$archivos = [
    'Modelos/ModeloUsuarios.php',
    'Conect/Conexion.php',
    'Controladores/ControladorUsuarios.php'
];

foreach ($archivos as $archivo) {
    echo "Archivo $archivo: " . (file_exists($archivo) ? "✅ EXISTE" : "❌ NO EXISTE") . "<br>";
}

// Verificar namespaces
require_once 'Conect/Conexion.php';
require_once 'Modelos/ModeloUsuarios.php';

// Probar el método directamente
try {
    error_log("Llamando a mdlMostrarUsuarios directamente...");
    $resultado = Modelos\ModeloUsuarios::mdlMostrarUsuarios('usuarios', 'usuario', 'demo');
    error_log("Resultado directo: " . ($resultado ? "OK" : "NULL"));
    
    if ($resultado) {
        echo "✅ mdlMostrarUsuarios funciona - Usuario: " . $resultado['usuario'] . "<br>";
    } else {
        echo "❌ mdlMostrarUsuarios devuelve NULL<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}