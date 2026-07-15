<?php
session_start();
require_once "../vendor/autoload.php";

use Controladores\ControladorUsuarios;
use Modelos\ModeloUsuarios;

class AjaxUsuarios
{
    //LOGIN USUARIOS
    public $ingUsuario;
    public $ingPassword;
    public function ajaxLogin()
    {
        $user = $this->ingUsuario;
        $pass = $this->ingPassword;
        @$token = $_POST["token"] ?? '';
        $conectar = $_POST["conectar"] ?? '';

        // El controlador ya devuelve JSON directamente
        ControladorUsuarios::ctrIngresoUsuario($user, $pass, $token, $conectar);
        exit;
    }

    //AGREGAR USUARIOS
    public function ajaxAgregarUsuario()
    {
        if (isset($_POST["nuevoNombre"])) {
            if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoNombre"])) {
                $ruta = "";

                if (isset($_FILES["nuevaFoto"]["tmp_name"])) {
                    if (($_FILES["nuevaFoto"]["type"] == "image/jpeg") ||
                        ($_FILES["nuevaFoto"]["type"] == "image/jpg") ||
                        ($_FILES["nuevaFoto"]["type"] == "image/png")
                    ) {
                        $directorio = "../vistas/img/usuarios/" . $_POST['nuevoUsuario'];
                        if (!file_exists($directorio)) {
                            mkdir($directorio, 0755, true);
                        }
                        $nombre_img = $_FILES['nuevaFoto']['name'];
                        $ruta = "vistas/img/usuarios/" . $_POST['nuevoUsuario'] . "/" . $nombre_img;
                        move_uploaded_file($_FILES['nuevaFoto']['tmp_name'], $directorio . "/" . $nombre_img);
                    }
                }

                $encriptar = crypt($_POST['nuevoPassword'], '$2a$07$usesomesillystringforsalt$');
                $datos = array(
                    'nombre' => $_POST["nuevoNombre"],
                    'usuario' => $_POST["nuevoUsuario"],
                    'password' => $encriptar,
                    'perfil' => $_POST["nuevoPerfil"],
                    'dni' => $_POST["nuevoDni"],
                    'email' => $_POST["nuevoEmail"],
                    "foto" => $ruta,
                    "id_sucursal" => $_SESSION['id_sucursal'] ?? 1
                );

                $respuesta = ControladorUsuarios::ctrCrearUsuario($datos);
                echo $respuesta;
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => '¡Los campos no pueden ir vacíos o llevar caracteres especiales!'
                ]);
            }
        }
        exit;
    }

    // EDITAR USUARIO
    public $idUsuario;
    public function ajaxEditarUsuario()
    {
        $item = 'id';
        $valor = $this->idUsuario;
        $respuesta = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
        echo json_encode($respuesta);
        exit;
    }

    // ACTIVAR USUARIO
    public $activarUsuario;
    public $activarId;
    public function ajaxActivarUsuario()
    {
        $tabla = 'usuarios';
        $item1 = 'estado';
        $valor1 = $this->activarUsuario;
        $item2 = 'id';
        $valor2 = $this->activarId;

        $respuesta = ModeloUsuarios::mdlActualizarUsuario($tabla, $item1, $valor1, $item2, $valor2);
        echo $respuesta;
        exit;
    }

    // VALIDAR NO REPETIR USUARIO
    public $validarUsuario;
    public function ajaxValidarUsuario()
    {
        $item = 'usuario';
        $valor = $this->validarUsuario;
        $respuesta = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
        echo json_encode($respuesta);
        exit;
    }

    public $dni;
    public function ajaxBuscarDni()
    {
        $dni = $this->dni;
        $respuesta = ControladorUsuarios::ctrBuscarDni($dni);
        exit;
    }

    public function ajaxCerrarSesion()
    {
        $item = 'id';
        $valor = $_SESSION['id'] ?? 0;
        $respuesta = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

        if ($respuesta && $respuesta['estado'] != 1) {
            echo 'ok';
        } else {
            echo 'error';
        }
        exit;
    }
}

// ============ OBJETO LOGIN USUARIOS ============
if (isset($_POST['ingUsuario'])) {
    function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    $login = new AjaxUsuarios();
    $login->ingUsuario = test_input($_POST['ingUsuario']);
    $login->ingPassword = test_input($_POST['ingPassword']);
    $login->ajaxLogin();
}

// ============ OBJETO AGREGAR USUARIO ============
if (isset($_POST['nuevoUsuario'])) {
    $nuevo = new AjaxUsuarios();
    $nuevo->ajaxAgregarUsuario();
}

// ============ OBJETO EDITAR USUARIO ============
if (isset($_POST['idUsuario'])) {
    $editar = new AjaxUsuarios();
    $editar->idUsuario = $_POST['idUsuario'];
    $editar->ajaxEditarUsuario();
}

// ============ OBJETO ACTIVAR USUARIO ============
if (isset($_POST['activarUsuario'])) {
    $activarUsuario = new AjaxUsuarios();
    $activarUsuario->activarId = $_POST['activarId'];
    $activarUsuario->activarUsuario = $_POST['activarUsuario'];
    $activarUsuario->ajaxActivarUsuario();
}

// ============ OBJETO VALIDAR NO REPETIR USUARIO ============
if (isset($_POST['validarUsuario'])) {
    $validarUsuario = new AjaxUsuarios();
    $validarUsuario->validarUsuario = $_POST['validarUsuario'];
    $validarUsuario->ajaxValidarUsuario();
}

// ============ OBJETO BUSCAR DNI ============
if (isset($_POST['dni'])) {
    $objDni = new AjaxUsuarios();
    $objDni->dni = $_POST['dni'];
    $objDni->ajaxBuscarDni();
}

// ============ OBJETO CERRAR SESION ============
if (isset($_POST['cerrarS'])) {
    $objCerras = new AjaxUsuarios();
    $objCerras->ajaxCerrarSesion();
}
