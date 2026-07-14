<?php

namespace api;

class Signature
{
    public function signature_xml($flg_firma, $ruta, $ruta_firma, $pass_firma)
    {
        // Verificar modo
        $env = getenv('APP_ENV') ?: 'development';
        $isDevelopment = ($env == 'development');

        if ($isDevelopment) {
            // En desarrollo, solo guardar una copia y simular firma
            $doc = new \DOMDocument();
            $doc->load($ruta);

            // Agregar un marcador de desarrollo
            $root = $doc->documentElement;
            $devNode = $doc->createElement('Desarrollo', 'Simulado - Sin firma SSL');
            $root->appendChild($devNode);

            $doc->save($ruta);

            // Registrar en log
            error_log("Firma simulada en modo desarrollo para: " . basename($ruta));

            return [
                'respuesta' => 'ok',
                'hash_cpe' => 'DEV-' . md5($ruta),
                'firma_cpe' => 'DEV-FIRMA'
            ];
        }
    }
}
