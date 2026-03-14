<?php
// ── Configuración de base de datos ──────────────────────────
// Para XAMPP local:
//   $host = "localhost"; $user = "root"; $pass = ""; $db = "bomberos";
// Para InfinityFree: cambia estos valores con los del panel de control
    "sql311.infinityfree.com",   // host que te dieron
    "if0_41390710",              // usuario de la BD
    "9Zjr9WFEiKS3nCS",             // contraseña
    "if0_41390710_bomberos"      // nombre de la BD

mysqli_report(MYSQLI_REPORT_OFF); // Evita warnings en pantalla
$con = new mysqli($host, $user, $pass, $db);

if ($con->connect_errno) {
    // En producción muestra mensaje genérico, no expongas credenciales
    error_log("DB connect error: " . $con->connect_error);
    die('<div style="font-family:sans-serif;padding:2rem;color:#ef4444;text-align:center;">'
      . '<strong>No se pudo conectar a la base de datos.</strong><br>'
      . '<small>Verifica la configuración en config/conexion.php</small>'
      . '</div>');
}

$con->set_charset("utf8mb4");
