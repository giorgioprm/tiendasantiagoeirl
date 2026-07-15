<?php

namespace Modelos;

use Conect\Conexion;
use PDO;

class ModeloProveedores
{

    // MOSTRAR PROVEEDORES
    public static function mdlMostrarProveedores($tabla, $item, $valor)
    {

        if ($item != null) {

            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla  WHERE $item = :$item");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);

            $stmt->execute();
            return $stmt->fetch();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");
            //$stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);    
            $stmt->execute();
            return $stmt->fetchall();
        }


        $stmt->close();
        $stmt = null;
    }

    public static function mdlGuardarProveedor($tabla, $datos)
    {

        if (strlen($datos['docIdentidad']) > 8) {
            $ruc = $datos['docIdentidad'];
            $razon_social = $datos['razon_social'];
            $nombre = '';
        } else {
            $ruc = '';
        }
        if (strlen($datos['docIdentidad']) == 8) {
            $dni = $datos['docIdentidad'];
            $razon_social = '';
            $nombre = $datos['razon_social'];
        } else {
            $dni = '';
        }

        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre, documento, ruc, razon_social, email, telefono,  direccion, ubigeo) VALUES (:nombre, :documento, :ruc, :razon_social, :email, :telefono, :direccion, :ubigeo)");

        $stmt->bindParam(":nombre", $nombre, PDO::PARAM_STR);
        $stmt->bindParam(":documento", $dni, PDO::PARAM_STR);
        $stmt->bindParam(":ruc", $ruc, PDO::PARAM_STR);
        $stmt->bindParam(":razon_social", $razon_social, PDO::PARAM_STR);
        $stmt->bindParam(":email", $datos['email'], PDO::PARAM_STR);
        $stmt->bindParam(":telefono", $datos['telefono'], PDO::PARAM_STR);
        $stmt->bindParam(":direccion", $datos['direccion'], PDO::PARAM_STR);
        $stmt->bindParam(":ubigeo", $datos['ubigeo'], PDO::PARAM_STR);

        if ($stmt->execute()) {

            return "ok";
        } else {

            return "error";
        }
        $stmt->close();
        $stmt = null;
    }

    // BUSCAR PROVEEDOR EN LA EMISIÓN DE COMPROBANTES
    public static function mdlBuscarProveedor($tabla, $valor)
    {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla  WHERE nombre LIKE :valor OR documento LIKE :valor OR ruc LIKE :valor");
        $parametros = array(':valor' => '%' . $valor . '%');

        $stmt->execute($parametros);
        return $stmt->fetchall();
    }

    // BUSCAR RUC Y DNI SUNAT - RENIEC PARA PROVEEDORES
    public static function mdlBuscarRuc($numDoc, $tipoDoc)
    {
        error_log("🔍 [ModeloProveedores] Buscando: " . $numDoc . " - Tipo: " . $tipoDoc);

        // REEMPLAZA CON TU TOKEN REAL DE APIPERU.DEV
        $token = "60f198fc53c48b7f8da7762d8ef4493b1029ddcf23abf38d35df0e3bc942a9b7";

        $numDoc = trim($numDoc);

        if ($tipoDoc == 6 && strlen($numDoc) == 11) {
            error_log("🌐 Consultando RUC en API...");
            return self::consultarRucApiPeru($numDoc, $token);
        } else if ($tipoDoc == 1 && strlen($numDoc) == 8) {
            error_log("🌐 Consultando DNI en API...");
            return self::consultarDniApiPeru($numDoc, $token);
        }

        error_log("❌ Documento no válido: " . $numDoc);
        return 'error';
    }

    // CONSULTAR RUC CON APIPERU.DEV
    private static function consultarRucApiPeru($ruc, $token)
    {
        $url = "https://apiperu.dev/api/ruc/" . $ruc;
        error_log("🔗 URL RUC: " . $url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $token,
            "Content-Type: application/json"
        ));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        error_log("📡 Respuesta HTTP RUC: " . $httpCode);

        if ($curlError) {
            error_log("❌ Error cURL RUC: " . $curlError);
            return 'error';
        }

        if ($httpCode == 200 && $response != false) {
            $data = json_decode($response, true);

            if (isset($data['success']) && $data['success'] === true && isset($data['data'])) {
                $datos = $data['data'];
                error_log("✅ RUC encontrado: " . $datos['nombre_o_razon_social']);

                return array(
                    'ruc' => $ruc,
                    'razon_social' => $datos['nombre_o_razon_social'],
                    'direccion' => $datos['direccion_completa'],
                    'estado' => $datos['estado'],
                    'ubigeo' => isset($datos['ubigeo']) ? $datos['ubigeo'] : '',
                    'telefono' => isset($datos['telefono']) ? $datos['telefono'] : ''
                );
            } else {
                error_log("❌ RUC no encontrado en API");
            }
        }

        return 'error';
    }

    // CONSULTAR DNI CON APIPERU.DEV
    private static function consultarDniApiPeru($dni, $token)
    {
        $url = "https://apiperu.dev/api/dni/" . $dni;
        error_log("🔗 URL DNI: " . $url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $token,
            "Content-Type: application/json"
        ));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        error_log("📡 Respuesta HTTP DNI: " . $httpCode);

        if ($curlError) {
            error_log("❌ Error cURL DNI: " . $curlError);
            return 'error';
        }

        if ($httpCode == 200 && $response != false) {
            $data = json_decode($response, true);

            if (isset($data['success']) && $data['success'] === true && isset($data['data'])) {
                $datos = $data['data'];
                error_log("✅ DNI encontrado: " . $datos['nombre_completo']);

                return array(
                    'documento' => $dni,
                    'nombre' => $datos['nombre_completo'],
                    'nombres' => $datos['nombres'],
                    'apellido_paterno' => $datos['apellido_paterno'],
                    'apellido_materno' => $datos['apellido_materno'],
                    'direccion' => '',
                    'estado' => 'ACTIVO'
                );
            } else {
                error_log("❌ DNI no encontrado en API");
            }
        }

        return 'error';
    }
}
