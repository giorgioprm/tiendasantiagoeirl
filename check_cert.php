<?php
// check_cert.php
$certPath = 'api/certificado.pfx'; // Cambia por la ruta correcta
$pass = 'ceti';

echo "=== DIAGNÓSTICO DEL CERTIFICADO ===\n\n";

// 1. Verificar si el archivo existe
if (!file_exists($certPath)) {
    die("❌ El certificado no existe en: " . realpath($certPath) . "\n");
}
echo "✅ El certificado existe en: " . realpath($certPath) . "\n";

// 2. Leer el contenido
$content = file_get_contents($certPath);
echo "📄 Tamaño del archivo: " . strlen($content) . " bytes\n";

// 3. Determinar el formato
if (strpos($content, 'PK') === 0) {
    echo "📦 Formato: PFX/P12 (binario)\n";
} elseif (strpos($content, '-----BEGIN') !== false) {
    echo "📄 Formato: PEM (texto)\n";
} else {
    echo "❓ Formato desconocido\n";
}

// 4. Intentar leer como PFX
$key = array();
if (openssl_pkcs12_read($content, $key, $pass)) {
    echo "✅ PFX leído correctamente\n";
    echo "   - Certificado: " . (isset($key['cert']) ? '✅' : '❌') . "\n";
    echo "   - Clave privada: " . (isset($key['pkey']) ? '✅' : '❌') . "\n";
} else {
    echo "❌ Error al leer PFX: " . openssl_error_string() . "\n";
}

// 5. Intentar leer como PEM
$cert = openssl_x509_read($content);
if ($cert !== false) {
    echo "✅ Se pudo leer como certificado PEM\n";
    $certData = openssl_x509_parse($cert);
    echo "   - Emisor: " . ($certData['issuer']['CN'] ?? 'N/A') . "\n";
    echo "   - Sujeto: " . ($certData['subject']['CN'] ?? 'N/A') . "\n";
    echo "   - Válido hasta: " . date('Y-m-d', $certData['validTo_time_t']) . "\n";
} else {
    echo "❌ Error al leer como PEM: " . openssl_error_string() . "\n";
}

// 6. Intentar extraer clave privada
$privateKey = openssl_pkey_get_private($content, $pass);
if ($privateKey !== false) {
    echo "✅ Clave privada cargada correctamente\n";
    $keyDetails = openssl_pkey_get_details($privateKey);
    echo "   - Tipo: " . ($keyDetails['type'] ?? 'N/A') . "\n";
    echo "   - Bits: " . ($keyDetails['bits'] ?? 'N/A') . "\n";
} else {
    echo "❌ Error al cargar clave privada: " . openssl_error_string() . "\n";
}

// 7. Recomendación
echo "\n=== RECOMENDACIÓN ===\n";
if (strpos(openssl_error_string(), 'unsupported') !== false) {
    echo "⚠️ Tu certificado usa un algoritmo no soportado por OpenSSL 3.x\n";
    echo "Para solucionarlo, crea el archivo openssl.cnf en la raíz del proyecto\n";
    echo "o convierte tu certificado a formato PEM con el siguiente comando:\n\n";
    echo "openssl pkcs12 -in certificado.pfx -out certificado.pem -nodes -passin pass:ceti\n";
}