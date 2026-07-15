<?php

namespace Controladores;

use Modelos\ModeloUsuarios;
use Conect\Conexion;
use PDO;

class ControladorUsuarios
{

    // METODO PARA INGRESO DE USUARIO
    public static function ctrIngresoUsuario($user, $pass, $token, $conectar)
    {
        // DEBUG
        error_log("=== LOGIN DEBUG ===");
        error_log("Usuario: " . $user);
        error_log("Conectar: " . ($conectar ?? 'NULL'));
        error_log("Token: " . substr($token, 0, 20) . '...');

        // Limpiar cualquier salida previa
        ob_clean();

        // Si no hay token o es vacío, permitir login en modo desarrollo
        $bypass_recaptcha = true; // Cambiar a false en producción

        if (isset($user) && isset($pass)) {
            if (preg_match('/^[a-zA-Z0-9]+$/', $user) && preg_match("/^[a-zA-Z0-9]+$/", $pass)) {

                $encriptar = crypt($pass, '$2a$07$usesomesillystringforsalt$');

                try {
                    require_once __DIR__ . '/../Conect/Conexion.php';
                    $conexion = \Conect\Conexion::conectar();
                    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
                    $stmt->bindParam(":usuario", $user, PDO::PARAM_STR);
                    $stmt->execute();
                    $respuesta = $stmt->fetch(PDO::FETCH_ASSOC);

                    error_log("Usuario encontrado: " . ($respuesta ? $respuesta['usuario'] : "NO"));

                    if ($respuesta && $respuesta['usuario'] == $user && $respuesta['password'] == $encriptar) {
                        error_log("✅ LOGIN EXITOSO");

                        if ($respuesta['estado'] == 1) {
                            // Iniciar sesión si no está iniciada
                            if (session_status() === PHP_SESSION_NONE) {
                                session_start();
                            }

                            $_SESSION['tiempo'] = time();
                            $_SESSION['iniciarSesion'] = 'ok';
                            $_SESSION['id'] = $respuesta['id'];
                            $_SESSION['id_sucursal'] = $respuesta['id_empresa'] ?? 1;
                            $_SESSION['nombre'] = $respuesta['nombre'];
                            $_SESSION['usuario'] = $respuesta['usuario'];
                            $_SESSION['foto'] = $respuesta['foto'] ?? '';
                            $_SESSION['perfil'] = $respuesta['perfil'];

                            // Registrar último login
                            date_default_timezone_set("America/Lima");
                            $fechaHora = date("Y-m-d H:i:s");

                            $stmt = $conexion->prepare("UPDATE usuarios SET ultimo_login = :fecha WHERE id = :id");
                            $stmt->bindParam(":fecha", $fechaHora);
                            $stmt->bindParam(":id", $respuesta['id']);
                            $stmt->execute();

                            // Devolver JSON
                            header('Content-Type: application/json');
                            echo json_encode([
                                'status' => 'success',
                                'redirect' => 'inicio'
                            ]);
                            return;
                        } else {
                            header('Content-Type: application/json');
                            echo json_encode([
                                'status' => 'error',
                                'message' => 'Su cuenta está desactivada'
                            ]);
                            return;
                        }
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Usuario o contraseña incorrectos'
                        ]);
                        return;
                    }
                } catch (\Exception $e) {
                    error_log("Error en login: " . $e->getMessage());
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error del sistema: ' . $e->getMessage()
                    ]);
                    return;
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Usuario o contraseña con caracteres inválidos'
                ]);
                return;
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'Usuario o contraseña no proporcionados'
            ]);
            return;
        }
    }
    // REGISTRO DE USUARIO
    public static function ctrCrearUsuario($datos)
    {

        $tabla = 'usuarios';
        $respuesta = ModeloUsuarios::mdlNuevoUsuario($tabla, $datos);
        if ($respuesta == 'ok') {

            echo "<script>
                            Swal.fire({
                                title: '¡El usuario ha sido guardado correctamente!',
                                text: '...',
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Cerrar'
                              }).then((result) => {
                                if (result.isConfirmed) {
                                  window.location = 'usuarios';
                                }
                                if(window.history.replaceState){
                                    window.history.replaceState(null,null, window.location.href);
                                    }


                              })</script>";
        }
    }
    // MOSTRAR USUARIOS|
    public static function ctrMostrarUsuarios($item, $valor)
    {

        $tabla = 'usuarios';
        $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
        return $respuesta;
    }

    // EDITAR USUARIOS|
    public static function ctrEditarUsuario()
    {

        if (isset($_POST["editarUsuario"])) {

            if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarNombre"])) {

                /*=============================================
				VALIDAR IMAGEN
				=============================================*/

                $ruta = $_POST["fotoActual"];

                if (isset($_FILES["editarFoto"]["tmp_name"]) && !empty($_FILES["editarFoto"]["tmp_name"])) {

                    list($ancho, $alto) = getimagesize($_FILES["editarFoto"]["tmp_name"]);

                    $nuevoAncho = 500;
                    $nuevoAlto = 500;

                    /*=============================================
					CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA FOTO DEL USUARIO
					=============================================*/

                    $directorio = "vistas/img/usuarios/" . $_POST["editarUsuario"];

                    /*=============================================
					PRIMERO PREGUNTAMOS SI EXISTE OTRA IMAGEN EN LA BD
					=============================================*/

                    if (!empty($_POST["fotoActual"])) {

                        unlink($_POST["fotoActual"]);
                    } else {

                        mkdir($directorio, 0755);
                    }

                    /*=============================================
					DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
					=============================================*/

                    if ($_FILES["editarFoto"]["type"] == "image/jpeg") {

                        /*=============================================
						GUARDAMOS LA IMAGEN EN EL DIRECTORIO
						=============================================*/

                        $aleatorio = mt_rand(100, 999);

                        $ruta = "vistas/img/usuarios/" . $_POST["editarUsuario"] . "/" . $aleatorio . ".jpg";

                        $origen = imagecreatefromjpeg($_FILES["editarFoto"]["tmp_name"]);

                        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

                        imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

                        imagejpeg($destino, $ruta);
                    }

                    if ($_FILES["editarFoto"]["type"] == "image/png") {

                        /*=============================================
						GUARDAMOS LA IMAGEN EN EL DIRECTORIO
						=============================================*/

                        $aleatorio = mt_rand(100, 999);

                        $ruta = "vistas/img/usuarios/" . $_POST["editarUsuario"] . "/" . $aleatorio . ".png";

                        $origen = imagecreatefrompng($_FILES["editarFoto"]["tmp_name"]);

                        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

                        imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

                        imagepng($destino, $ruta);
                    }
                }
                $tabla = "usuarios";

                if ($_POST["editarPassword"] != "") {

                    if (preg_match('/^[a-zA-Z0-9]+$/', $_POST["editarPassword"])) {

                        $encriptar = crypt($_POST['editarPassword'], '$2a$07$usesomesillystringforsalt$');
                    } else {

                        echo "<script>
                    Swal.fire({
                        title: '¡La contraseña no puede ir vacío o llevar caracteres especiales!',
                        text: '...',
                        icon: 'error',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Cerrar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                        window.location = 'usuarios';
                        }
                    })</script>";
                    }
                } else {

                    $encriptar = $_POST["passwordActual"];
                }
                session_start();
                $datos = array(
                    "nombre" => $_POST["editarNombre"],
                    "usuario" => $_POST["editarUsuario"],
                    "password" => $encriptar,
                    "perfil" => $_POST["editarPerfil"],
                    'dni' => $_POST["editarDni"],
                    'email' => $_POST["editarEmail"],
                    "foto" => $ruta,
                    "id_sucursal" => $_SESSION['id_sucursal']
                );

                $respuesta = ModeloUsuarios::mdlEditarUsuario($tabla, $datos);

                if ($respuesta == "ok") {

                    echo "<script>
                            Swal.fire({
                                title: '¡El usuario ha sido actualizado correctamente!',
                                text: '...',
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Cerrar'
                              }).then((result) => {
                                if (result.isConfirmed) {
                                  window.location = 'usuarios';
                                }
                                if(window.history.replaceState){
                                    window.history.replaceState(null,null, window.location.href);
                                    }
                              })</script>";
                } else {

                    echo "<script>
                    Swal.fire({
                        title: '¡El usuario no puede ir vacío o llevar caracteres especiales!',
                        text: '...',
                        icon: 'error',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Cerrar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                        window.location = 'usuarios';
                        }
                    })</script>";
                }
            }
        }
    }

    // BORRAR USUARIO
    public static function ctrBorrarUsuario()
    {
        if (isset($_GET['idUsuario'])) {
            $tabla = 'usuarios';
            $datos = $_GET['idUsuario'];
            if (file_exists($_GET['fotoUsuario'])) {

                unlink($_GET['fotoUsuario']);
                rmdir("vistas/img/usuarios/" . $_GET['usuario']);
            }
            $respuesta = ModeloUsuarios::mdlBorrarUsuario($tabla, $datos);
            if ($respuesta == 'ok') {

                echo "<script>
                        Swal.fire({
                        title: '¡El usuario ha sido eliminado!',
                        text: '...',
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Cerrar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                        window.location = 'usuarios';
                        }
                    })
                    </script>";
            }
        }
    }
    // BUSCAR DNI USUARIO=========================
    public static function ctrBuscarDni($dni)
    {

        $dni = $dni;

        $token =  '97aec307dcc31dd529127753f38db369';

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.apifacturacion.com/dni/' . $dni,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS  => array('token' => $token),
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_CAINFO => dirname(__FILE__) . "/../api/cacert.pem" //Comentar si sube a un hosting 
            //para ejecutar los procesos de forma local en windows
            //enlace de descarga del cacert.pem https://curl.haxx.se/docs/caextract.html

        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $empresa = json_decode($response);

        if (isset($empresa->dni)) {
            $datos = array(
                'dni' => $empresa->dni,
                'nombre' => $empresa->cliente,

            );

            echo json_encode($datos);
        } else {
            echo json_encode('error');
        }
    }

    // COMPROBAR CONEXIÓN 
    public static function ctrConn()
    {
        // use 80 for http or 443 for https protocol
        $emisor = ControladorEmpresa::ctrEmisorConexion();

        if ($emisor['conexion'] == 's') {
            return 'ok';
        } else {
            return 'error';
        }
    }
}
