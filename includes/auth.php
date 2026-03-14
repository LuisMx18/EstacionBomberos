<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Timeout de sesión: 30 minutos ---
define('SESSION_TIMEOUT', 1800);

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    header("Location: ../login.php?timeout=1");
    exit;
}
$_SESSION['last_activity'] = time();

// --- Verificar autenticación ---
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}

// --- Token CSRF ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = function_exists('random_bytes')
        ? bin2hex(random_bytes(32))
        : bin2hex(openssl_random_pseudo_bytes(32));
}

// --- Roles y permisos ---
define('ROL_ADMIN', 'Administrador');
define('ROL_RADIO', 'Radio Operador');

$_PERMISOS_MODULOS = [
    ROL_ADMIN => ['bomberos', 'reportes', 'asistencia', 'asistencia_rfid', 'servicios', 'combustible', 'novedades', 'despachos'],
    ROL_RADIO => ['asistencia', 'asistencia_rfid', 'servicios', 'combustible', 'novedades', 'despachos'],
];

function get_rol(): string {
    return $_SESSION['usuario_rol'] ?? '';
}

function puede_acceder(string $modulo): bool {
    global $_PERMISOS_MODULOS;
    $rol = get_rol();
    return isset($_PERMISOS_MODULOS[$rol]) && in_array($modulo, $_PERMISOS_MODULOS[$rol], true);
}

function require_modulo(string $modulo): void {
    if (!puede_acceder($modulo)) {
        http_response_code(403);
        global $pageTitle;
        $pageTitle = "Acceso denegado";
        require_once __DIR__ . '/header.php';
        echo '<div class="alert alert-danger animate__animated animate__fadeIn">'
           . '<i class="bi bi-shield-x me-2"></i>'
           . '<strong>Acceso denegado.</strong> No tienes permiso para acceder a este módulo.'
           . '</div>';
        require_once __DIR__ . '/footer.php';
        exit;
    }
}

function validar_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die('<div style="font-family:sans-serif;padding:2rem;color:#ef4444;"><strong>Token de seguridad inválido.</strong> <a href="javascript:history.back()">Volver</a></div>');
        }
    }
}

function set_flash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
