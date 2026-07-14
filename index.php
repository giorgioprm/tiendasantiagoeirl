<?php
putenv('OPENSSL_CONF=' . __DIR__ . '/openssl.cnf');
putenv('PEMPATH=' . __DIR__ . '/check_pem.php');
putenv('PFXPATH=' . __DIR__ . '/check_pfx.php');

require_once("vendor/autoload.php");

use Controladores\ControladorPlantilla;

date_default_timezone_set('America/Lima');

$plantilla = new ControladorPlantilla();
$plantilla->ctrPlantilla();

// Al inicio de tu index.php o en un archivo de configuración

// Cargar variables de entorno
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile);
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line) && strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// Definir constantes
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_DEBUG', getenv('APP_DEBUG') === 'true');

// Función de logging
function logDev($message)
{
    if (APP_DEBUG) {
        $logFile = __DIR__ . '/logs/dev.log';
        $dir = dirname($logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($logFile, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
    }
}
