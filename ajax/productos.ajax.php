<?php
require_once "../vendor/autoload.php";

use Controladores\ControladorProductos;

class AjaxProductos
{
    // CREAR PRODUCTO
    public function ajaxCrearProducto()
    {
        $productos = $_POST;
        $file = $_FILES;
        $respuesta = ControladorProductos::ctrCrearProducto($productos, $file);
        // var_dump($file);
        echo $respuesta;
    }

    // GENERAR CÓDIGO DESDE LA idCategoria
    public function ajaxCrearCodigoProducto()
    {

        $item = "id_categoria";
        $valor = $this->idCategoria;
        $respuesta = ControladorProductos::ctrMostrarProductos($item, $valor);

        if (is_array($respuesta) && count($respuesta) > 0) {
            // Si hay productos, obtener el último código
            $ultimoProducto = end($respuesta);
            echo json_encode($ultimoProducto);
        } else {
            // Si no hay productos, devolver false
            echo json_encode(false);
        }
    }

    // EDITAR PRODUCTO Y SELECCIONAR PRODUCTO
    public $idProducto;
    public function ajaxAgregarProducto()
    {
        $item = "id";
        $valor = $this->idProducto;
        $respuesta = ControladorProductos::ctrMostrarProductos($item, $valor);
        echo json_encode($respuesta);
    }

    //  ELIMINAR PRODUCTO BETA
    public $idEliminarProducto;
    public $codigo;
    public $imagen;
    public function ajaxEliminarProducto()
    {
        $datosEliminar = array(
            'idProducto' => $this->idEliminarProducto,
            'codigo' => $this->codigo,
            'imagen' => $this->imagen
        );

        $resultado = ControladorProductos::ctrEliminarProducto($datosEliminar);
    }

    // ACTIVAR O DESACTIVAR UNIDAD DE MEDIDA
    public function ajaxActivaDesactivaUnidadMedida()
    {
        $datos = array(
            "id" => $_POST['idUnidad'],
            "modo" => $_POST['modo']
        );
        $resultado = ControladorProductos::ctrActivaDesactivaUnidadMedida($datos);
    }
}

// CREAR PRODUCTO
if (isset($_POST['nuevaDescripcion'])) {
    $actDesa = new AjaxProductos();
    $actDesa->ajaxCrearProducto();
}
// GENERAR CÓDIGO DESDE LA idCategoria
if (isset($_POST['idCategoria'])) {
    $codigoProducto = new AjaxProductos();
    $codigoProducto->idCategoria = $_POST['idCategoria'];
    $codigoProducto->ajaxCrearCodigoProducto();
}
// EDITAR PRODUCTO
if (isset($_POST['idProducto'])) {
    $editarProducto = new AjaxProductos();
    $editarProducto->idProducto = $_POST['idProducto'];
    $editarProducto->ajaxAgregarProducto();
}

// ELIMINAR PRODUCTO
if (isset($_POST['idEliminarProducto'])) {

    $eliminar = new AjaxProductos();
    $eliminar->idEliminarProducto = $_POST['idEliminarProducto'];
    $eliminar->codigo = $_POST['codigo'];
    $eliminar->imagen = $_POST['imagen'];
    $eliminar->ajaxEliminarProducto();
}

// ACTIVAR O DESACTIVAR UNIDAD DE MEDIDA
if (isset($_POST['idUnidad'])) {
    $actDesa = new AjaxProductos();
    $actDesa->ajaxActivaDesactivaUnidadMedida();
}
