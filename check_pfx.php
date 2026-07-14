<?php
// check_pfx.php
$pfxPath = 'api/certificado/certificado_prueba.pfx';
$pass = 'ceti'; // Cambia por tu contraseña

echo "=== VERIFICANDO ARCHIVO PFX ===\n\n";

if (!file_exists($pfxPath)) {
    echo "❌ El archivo PFX no existe en: " . $pfxPath . "\n";
    exit;
}

echo "✅ El archivo PFX existe en: " . realpath($pfxPath) . "\n";
echo "📄 Tamaño: " . filesize($pfxPath) . " bytes\n";

$pfxContent = file_get_contents($pfxPath);
$key = array();

if (openssl_pkcs12_read($pfxContent, $key, $pass)) {
    echo "✅ PFX leído correctamente\n";
    echo "   - Certificado: " . (isset($key['cert']) ? '✅' : '❌') . "\n";
    echo "   - Clave privada: " . (isset($key['pkey']) ? '✅' : '❌') . "\n";

    if (isset($key['cert'])) {
        $cert = openssl_x509_read($key['cert']);
        if ($cert) {
            $certData = openssl_x509_parse($cert);
            echo "   - Sujeto: " . ($certData['subject']['CN'] ?? 'N/A') . "\n";
            echo "   - Emisor: " . ($certData['issuer']['CN'] ?? 'N/A') . "\n";
            echo "   - Válido hasta: " . date('Y-m-d', $certData['validTo_time_t']) . "\n";
        }
    }
} else {
    echo "❌ Error al leer PFX: " . openssl_error_string() . "\n";
}