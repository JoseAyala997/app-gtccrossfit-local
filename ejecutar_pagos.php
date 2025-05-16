<?php // Configuración para suprimir advertencias 
error_reporting(E_ALL & ~E_USER_DEPRECATED);

ini_set('display_errors', 0);  // URL de la ruta que genera los pagos 
$url = 'http://localhost/payment-confirmation/generate-payments';  // Timestamp para registro 
$timestamp = date('Y-m-d H:i:s');
echo "[$timestamp] Iniciando generación de pagos mediante solicitud HTTP...\n";
// Función para hacer la solicitud HTTP con autenticación 
function makeHttpRequest($url)
{
    // Primero, inicia sesión    
    $loginUrl = 'http://localhost/users/login';
    $username = 'admin'; // Reemplaza con tu nombre de usuario  

    $password = 'admin'; // Reemplaza con tu contraseña     
    // Inicializar cURL y configurar para guardar cookies  
    $cookieJar = tempnam(sys_get_temp_dir(), 'cookie');
    // Paso 1: Obtener el formulario de login para capturar tokens CSRF si los hay    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $html = curl_exec($ch);
    curl_close($ch);
    // Paso 2: Enviar el formulario de login   
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ['username' => $username,         'password' => $password]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $loginResponse = curl_exec($ch);
    $loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "[$timestamp] Intento de inicio de sesión: Código $loginHttpCode\n";
    // Paso 3: Acceder a la URL que genera los pagos    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    // Limpiar archivo de cookies   
    if (file_exists($cookieJar)) {
        unlink($cookieJar);
    }
    return ['code' => $httpCode,         'response' => $response,         'error' => $error];
}
// Realizar la solicitud
$result = makeHttpRequest($url);
// Mostrar resultado 
if ($result['code'] === 200) {
    echo "[$timestamp] Solicitud completada con éxito (código: {$result['code']})\n";
    // Verificar si la respuesta contiene HTML o JSON  
    if (strpos($result['response'], '<!DOCTYPE html>') !== false) {
        echo "[$timestamp] Advertencia: La respuesta parece ser HTML, no JSON. Posible problema de autenticación.\n";
        // Mostrar solo las primeras líneas para no saturar la consola       
        echo "[$timestamp] Primeras líneas de la respuesta: " . substr($result['response'], 0, 200) . "...\n";
    } else {
        echo "[$timestamp] Respuesta: {$result['response']}\n";
    }
} else {
    echo "[$timestamp] Error en la solicitud (código: {$result['code']})\n";
    echo "[$timestamp] Error: {$result['error']}\n";
    if (!empty($result['response'])) {
        echo "[$timestamp] Respuesta: {$result['response']}\n";
    }
}
echo "[$timestamp] Script finalizado.\n";
