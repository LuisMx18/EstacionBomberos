<?php
// DIAGNÓSTICO - Elimina este archivo después de usarlo
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>PHP Info</h2>";
echo "Versión PHP: " . phpversion() . "<br>";
echo "random_bytes: " . (function_exists('random_bytes') ? '✅' : '❌') . "<br>";
echo "openssl_random_pseudo_bytes: " . (function_exists('openssl_random_pseudo_bytes') ? '✅' : '❌') . "<br>";
echo "session_start: " . (function_exists('session_start') ? '✅' : '❌') . "<br>";
echo "password_verify: " . (function_exists('password_verify') ? '✅' : '❌') . "<br>";

echo "<h2>Conexión BD</h2>";
$host = "localhost"; // <-- cambia esto
$user = "root";      // <-- cambia esto
$pass = "";          // <-- cambia esto
$db   = "bomberos";  // <-- cambia esto

$con = @new mysqli($host, $user, $pass, $db);
if ($con->connect_errno) {
    echo "❌ Error BD: " . $con->connect_error . "<br>";
} else {
    echo "✅ Conexión exitosa<br>";
    $con->close();
}

echo "<h2>Rutas</h2>";
echo "DIR actual: " . __DIR__ . "<br>";
echo "config/conexion.php existe: " . (file_exists(__DIR__ . '/config/conexion.php') ? '✅' : '❌') . "<br>";
echo "includes/auth.php existe: " . (file_exists(__DIR__ . '/includes/auth.php') ? '✅' : '❌') . "<br>";
