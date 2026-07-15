<?php

namespace Modelos;
// require_once "conexion.php";
use Conect\Conexion;
use PDO;

class ModeloClientes
{

    // MOSTRAR CLIENTES
    public static function mdlMostrarClientes($tabla, $item, $valor)
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
    // OBJETO MODELO CREAR CLIENTE
    public static function mdlCrearCliente($tabla, $datos)
    {

        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre, documento, ruc, razon_social, email, telefono,  direccion, ubigeo, fecha_nacimiento) VALUES (:nombre, :documento, :ruc, :razon_social, :email, :telefono, :direccion, :ubigeo, :fecha_nacimiento)");

        $stmt->bindParam(":nombre", $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(":documento", $datos['documento'], PDO::PARAM_STR);
        $stmt->bindParam(":ruc", $datos['ruc'], PDO::PARAM_STR);
        $stmt->bindParam(":razon_social", $datos['razon_social'], PDO::PARAM_STR);
        $stmt->bindParam(":email", $datos['email'], PDO::PARAM_STR);
        $stmt->bindParam(":telefono", $datos['telefono'], PDO::PARAM_STR);
        $stmt->bindParam(":direccion", $datos['direccion'], PDO::PARAM_STR);
        $stmt->bindParam(":ubigeo", $datos['ubigeo'], PDO::PARAM_STR);
        $stmt->bindParam(":fecha_nacimiento", $datos['fecha_nacimiento'], PDO::PARAM_STR);

        if ($stmt->execute()) {

            return "ok";
        } else {

            return "error";
        }
        $stmt->close();
        $stmt = null;
    }
    // EDITAR CLIENTE
    public static function mdlEditarCliente($tabla, $datos)
    {

        $stmt = Conexion::conectar();
        $stmt = $stmt->prepare("UPDATE $tabla SET nombre = :nombre, documento = :documento, ruc = :ruc, razon_social = :razon_social, email = :email, telefono = :telefono, direccion = :direccion, fecha_nacimiento = :fecha_nacimiento WHERE id = :id");

        $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
        $stmt->bindParam(":nombre", $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(":documento", $datos['documento'], PDO::PARAM_STR);
        $stmt->bindParam(":ruc", $datos['ruc'], PDO::PARAM_STR);
        $stmt->bindParam(":razon_social", $datos['razon_social'], PDO::PARAM_STR);
        $stmt->bindParam(":email", $datos['email'], PDO::PARAM_STR);
        $stmt->bindParam(":telefono", $datos['telefono'], PDO::PARAM_STR);
        $stmt->bindParam(":direccion", $datos['direccion'], PDO::PARAM_STR);
        $stmt->bindParam(":fecha_nacimiento", $datos['fecha_nacimiento'], PDO::PARAM_STR);

        if ($stmt->execute()) {

            return "ok";
        } else {

            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

    // ELIMINAR CLIENTE
    public static function mdlEliminarCliente($tabla, $datos)
    {

        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla  WHERE id=:id");
        $stmt->bindParam(":id", $datos, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return 'ok';
        } else {
            return 'error';
        }
        $stmt->close();
        $stmt = null;
    }
    // LISTAR CLIENTES OTRO MÉTODO
    public static function mdlListarClientes()
    {

        $content =  "<tbody class='body-clientes'></tbody>";
        return $content;
    }

    // BUSCAR RUC Y DNI SUNAT - RENIEC PARA CLIENTES
    public static function mdlBuscarRuc($numDoc, $tipoDoc)
    {
        // Tu token de apiperu.dev - REEMPLAZA CON TU TOKEN REAL
        // $token = "TU_TOKEN_DE_APIPERU_DEV";
        $token = "60f198fc53c48b7f8da7762d8ef4493b1029ddcf23abf38d35df0e3bc942a9b7";

        // Limpiar el número de documento
        $numDoc = trim($numDoc);

        if ($tipoDoc == 6 && strlen($numDoc) == 11) { // RUC (11 dígitos)
            return self::consultarRucApiPeru($numDoc, $token);
        } else if ($tipoDoc == 1 && strlen($numDoc) == 8) { // DNI (8 dígitos)
            return self::consultarDniApiPeru($numDoc, $token);
        }

        return 'error';
    }

    // CONSULTAR RUC CON APIPERU.DEV
    private static function consultarRucApiPeru($ruc, $token)
    {
        $url = "https://apiperu.dev/api/ruc/" . $ruc;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $token
        ));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200 && $response != false) {
            $data = json_decode($response, true);

            if (isset($data['success']) && $data['success'] === true && isset($data['data'])) {
                $datos = $data['data'];

                return array(
                    'ruc' => $ruc,
                    'razon_social' => $datos['nombre_o_razon_social'],
                    'direccion' => $datos['direccion_completa'],
                    'estado' => $datos['estado'],
                    'ubigeo' => isset($datos['ubigeo']) ? $datos['ubigeo'] : '',
                    'telefono' => isset($datos['telefono']) ? $datos['telefono'] : ''
                );
            }
        }

        return 'error';
        return ModeloProveedores::mdlBuscarRuc($numDoc, $tipoDoc);
    }

    // CONSULTAR DNI CON APIPERU.DEV
    private static function consultarDniApiPeru($dni, $token)
    {
        $url = "https://apiperu.dev/api/dni/" . $dni;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $token
        ));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200 && $response != false) {
            $data = json_decode($response, true);

            if (isset($data['success']) && $data['success'] === true && isset($data['data'])) {
                $datos = $data['data'];

                return array(
                    'documento' => $dni,
                    'nombre' => $datos['nombre_completo'],
                    'nombres' => $datos['nombres'],
                    'apellido_paterno' => $datos['apellido_paterno'],
                    'apellido_materno' => $datos['apellido_materno'],
                    'direccion' => '', // RENIEC no proporciona dirección
                    'estado' => 'ACTIVO'
                );
            }
        }

        return 'error';
    }
}
