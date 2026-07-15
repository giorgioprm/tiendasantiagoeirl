<?php
// debug_namespace.php
use Modelos\ModeloUsuarios;

echo "<h1>Debug Namespace</h1>";

// Probar diferentes formas de cargar la clase
$formas = [
    'Directo' => function () {
        require_once 'Modelos/ModeloUsuarios.php';
        require_once 'Conect/Conexion.php';
        return \Modelos\ModeloUsuarios::mdlMostrarUsuarios('usuarios', 'usuario', 'demo');
    },
    'Con namespace completo' => function () {
        require_once 'Modelos/ModeloUsuarios.php';
        require_once 'Conect/Conexion.php';
        return \Modelos\ModeloUsuarios::mdlMostrarUsuarios('usuarios', 'usuario', 'demo');
    },
    'Con use' => function () {
        require_once 'Modelos/ModeloUsuarios.php';
        require_once 'Conect/Conexion.php';
        return ModeloUsuarios::mdlMostrarUsuarios('usuarios', 'usuario', 'demo');
    }
];

foreach ($formas as $nombre => $funcion) {
    echo "<h3>Probando: $nombre</h3>";
    try {
        $resultado = $funcion();
        if ($resultado) {
            echo "✅ FUNCIONA - Usuario: " . $resultado['usuario'] . "<br>";
        } else {
            echo "❌ Devuelve NULL<br>";
        }
    } catch (Error $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    } catch (Exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "<br>";
    }
}
