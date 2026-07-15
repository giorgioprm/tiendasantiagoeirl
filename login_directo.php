<?php
// login_directo.php
session_start();
require_once 'Conect/Conexion.php';
require_once 'Modelos/ModeloUsuarios.php';

$user = 'demo';
$pass = 'demo';

echo "<h1>Login Directo</h1>";
echo "Usuario: $user<br>";
echo "Password: $pass<br>";

$encriptar = crypt($pass, '$2a$07$usesomesillystringforsalt$');
echo "Password encriptado: $encriptar<br>";

// Usar el modelo
$respuesta = Modelos\ModeloUsuarios::mdlMostrarUsuarios('usuarios', 'usuario', $user);

if ($respuesta && $respuesta['password'] == $encriptar) {
    echo "<h2 style='color: green;'>✅ LOGIN EXITOSO</h2>";
    
    $_SESSION['iniciarSesion'] = 'ok';
    $_SESSION['id'] = $respuesta['id'];
    $_SESSION['nombre'] = $respuesta['nombre'];
    $_SESSION['usuario'] = $respuesta['usuario'];
    
    echo "Sesión iniciada correctamente<br>";
    echo "<a href='inicio'>Ir al inicio</a>";
} else {
    echo "<h2 style='color: red;'>❌ LOGIN FALLIDO</h2>";
    if (!$respuesta) {
        echo "Usuario no encontrado<br>";
    } else {
        echo "Password incorrecto<br>";
    }
}