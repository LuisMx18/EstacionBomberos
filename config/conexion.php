<?php
// ── Configuración de base de datos ──────────────────────────
// Railway inyecta las variables de entorno automáticamente.
// Para XAMPP local define las variables en tu entorno o déjalas con los defaults.

$host = getenv('MYSQLHOST')     ?: 'localhost';
$user = getenv('MYSQLUSER')     ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: '';
$db   = getenv('MYSQLDATABASE') ?: 'bomberos';
$port = (int)(getenv('MYSQLPORT') ?: 3306);

mysqli_report(MYSQLI_REPORT_OFF);
$con = new mysqli($host, $user, $pass, $db, $port);

if ($con->connect_errno) {
    error_log("DB connect error: " . $con->connect_error);
    die('<div style="font-family:sans-serif;padding:2rem;color:#ef4444;text-align:center;">'
      . '<strong>No se pudo conectar a la base de datos.</strong><br>'
      . '<small>Verifica la configuración en config/conexion.php</small>'
      . '</div>');
}

$con->set_charset("utf8mb4");
