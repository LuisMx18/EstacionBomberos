<?php
// ══════════════════════════════════════════════
//  CONFIGURACIÓN DE BASE DE DATOS
//  Cambia estos 4 valores con los de tu hosting
// ══════════════════════════════════════════════
$host = 'localhost';               // En 000webhost siempre es localhost
$user = 'TU_USUARIO_BD';           // Ej: id123456789_bomberos
$pass = 'TU_CONTRASEÑA_BD';        // La que creaste en el panel
$db   = 'TU_NOMBRE_BD';            // Ej: id123456789_bomberos
$port = 3306;

// ── Soporte para Railway (variables de entorno) ──
if (getenv('MYSQLHOST'))     $host = getenv('MYSQLHOST');
if (getenv('MYSQLUSER'))     $user = getenv('MYSQLUSER');
if (getenv('MYSQLPASSWORD')) $pass = getenv('MYSQLPASSWORD');
if (getenv('MYSQLDATABASE')) $db   = getenv('MYSQLDATABASE');
if (getenv('MYSQLPORT'))     $port = (int) getenv('MYSQLPORT');

// ─────────────────────────────────────────────
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
