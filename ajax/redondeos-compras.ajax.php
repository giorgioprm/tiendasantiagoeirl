<?php
session_start();
require_once "../vendor/autoload.php";

use Controladores\ControladorEmpresa;

class ajaxRedondeos
{
    public function ajaxRedondeosCompras()
    {
        // === CONVERTIR A NÚMEROS (FLOAT) ===
        $descuento_item = floatval(str_replace(',', '.', $_POST['descuento_item'] ?? 0));
        $valor_unitario = floatval(str_replace(',', '.', $_POST['valor_unitario'] ?? 0));
        $precio_unitario = floatval(str_replace(',', '.', $_POST['precio_unitario'] ?? 0));
        $cantidad = intval($_POST['cantidad'] ?? 1);
        $tipo_afectacion = $_POST['tipo_afectacion'] ?? '';

        // Validar que los valores sean números
        if ($cantidad <= 0) {
            $cantidad = 1;
        }

        $emisorigv = new ControladorEmpresa();
        $emisorigv->ctrEmisorIgv();

        // Inicializar variables
        $sub_total = 0;
        $igv = 0;
        $precio_unitario_calc = 0;
        $valor_unitario_calc = 0;

        // Calcular según tipo de afectación
        if ($tipo_afectacion == '10') {
            // Gravado
            $igv_precio = $valor_unitario * $emisorigv->igv_dos;
            $precio_unitario_calc = $valor_unitario + $igv_precio;
            $sub_total = ($valor_unitario * $cantidad) - $descuento_item;
            $igv = $sub_total * $emisorigv->igv_dos;
            $valor_unitario_calc = $valor_unitario;
        } elseif (
            $tipo_afectacion == '11' || $tipo_afectacion == '12' || $tipo_afectacion == '13' ||
            $tipo_afectacion == '14' || $tipo_afectacion == '15' || $tipo_afectacion == '16'
        ) {
            // Gratuito - Gravado
            $sub_total = $valor_unitario * $cantidad;
            $igv = 0.00;
            $precio_unitario_calc = $valor_unitario;
            $valor_unitario_calc = $valor_unitario;
        } elseif (
            $tipo_afectacion == '31' || $tipo_afectacion == '32' || $tipo_afectacion == '33' ||
            $tipo_afectacion == '34' || $tipo_afectacion == '35' || $tipo_afectacion == '36'
        ) {
            // Gratuito - Inafecto
            $sub_total = $valor_unitario * $cantidad;
            $igv = 0.00;
            $precio_unitario_calc = $valor_unitario;
            $valor_unitario_calc = $valor_unitario;
        } elseif ($tipo_afectacion == '20') {
            // Exonerado
            $sub_total = ($valor_unitario * $cantidad) - $descuento_item;
            $igv = 0.00;
            $precio_unitario_calc = $valor_unitario;
            $valor_unitario_calc = $valor_unitario;
        } elseif ($tipo_afectacion == '30') {
            // Inafecto
            $sub_total = ($valor_unitario * $cantidad) - $descuento_item;
            $igv = 0.00;
            $precio_unitario_calc = $valor_unitario;
            $valor_unitario_calc = $valor_unitario;
        } else {
            // Valor por defecto
            $sub_total = $valor_unitario * $cantidad;
            $igv = 0;
            $precio_unitario_calc = $valor_unitario;
            $valor_unitario_calc = $valor_unitario;
        }

        // Calcular total
        $total = $sub_total + $igv;

        // Asegurar que no haya valores negativos
        if ($total < 0) $total = 0;
        if ($sub_total < 0) $sub_total = 0;

        $datos = array(
            "precio_unitario" => round($precio_unitario_calc, 2),
            "valor_unitario" => round($valor_unitario_calc, 2),
            "subtotal" => round($sub_total, 2),
            "igv" => round($igv, 2),
            "total" => round($total, 2)
        );

        echo json_encode($datos);
    }
}

if (isset($_POST['codigoProduct'])) {
    $objDescuento = new ajaxRedondeos();
    $objDescuento->ajaxRedondeosCompras();
}