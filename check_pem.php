<?php
$pemPath = './api/certificado/cacert.pem';

echo "=== VERIFICANDO CERTIFICADO PEM ===\n\n";

if (file_exists($pemPath)) {
    echo "✅ El archivo existe en: " . realpath($pemPath) . "\n";
    echo "📄 Tamaño: " . filesize($pemPath) . " bytes\n";

    $content = file_get_contents($pemPath);

    // Verificar si tiene certificado
    if (preg_match('/-----BEGIN CERTIFICATE-----/', $content)) {
        echo "✅ Contiene certificado\n";
    } else {
        echo "❌ No contiene certificado\n";
    }

    // Verificar si tiene clave privada
    if (
        preg_match('/-----BEGIN PRIVATE KEY-----/', $content) ||
        preg_match('/-----BEGIN RSA PRIVATE KEY-----/', $content)
    ) {
        echo "✅ Contiene clave privada\n";
    } else {
        echo "❌ No contiene clave privada\n";
    }
} else {
    echo "❌ El archivo NO existe en: " . $pemPath . "\n";
    echo "Verifica que el archivo esté en la carpeta api/\n";
}