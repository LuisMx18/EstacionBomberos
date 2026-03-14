<?php
session_start();
require_once "config/conexion.php";

$error = "";

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = function_exists('random_bytes')
                        ? bin2hex(random_bytes(32))
                        : bin2hex(openssl_random_pseudo_bytes(32));
}

// Si ya está autenticado, redirigir según rol
if (isset($_SESSION['usuario_id'])) {
    $rol = $_SESSION['usuario_rol'] ?? '';
    if ($rol === 'Administrador') {
        header("Location: crud/dashboard.php");
    } else {
        header("Location: crud/asistencia.php");
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validar CSRF
    $csrf = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
        $error = "Token de seguridad inválido. Recarga la página.";
    } else {
        // Límite de intentos: máximo 10 en la sesión
        $_SESSION['login_intentos'] = ($_SESSION['login_intentos'] ?? 0);
        if ($_SESSION['login_intentos'] >= 10) {
            $error = "Demasiados intentos fallidos. Cierra y abre el navegador.";
        } else {
            $usuario  = trim($_POST['usuario'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($usuario === '' || $password === '') {
                $error = "Ingresa usuario y contraseña.";
            } else {
                $stmt = $con->prepare("SELECT id, usuario, password_hash, nombre, rol FROM usuarios WHERE usuario = ? LIMIT 1");
                $stmt->bind_param("s", $usuario);
                $stmt->execute();
                $res = $stmt->get_result();
                $row = $res->fetch_assoc();
                $stmt->close();

                if ($row && password_verify($password, $row['password_hash'])) {
                    // Login correcto: regenerar sesión para evitar session fixation
                    session_regenerate_id(true);
                    $_SESSION['login_intentos']   = 0;
                    $_SESSION['usuario_id']        = $row['id'];
                    $_SESSION['usuario']           = $row['usuario'];
                    $_SESSION['usuario_nombre']    = $row['nombre'];
                    $_SESSION['usuario_rol']       = $row['rol'];
                    $_SESSION['last_activity']     = time();
                    $_SESSION['csrf_token']        = function_exists('random_bytes')
                        ? bin2hex(random_bytes(32))
                        : bin2hex(openssl_random_pseudo_bytes(32));

                    // Redirigir según rol
                    if ($row['rol'] === 'Administrador') {
                        header("Location: crud/dashboard.php");
                    } else {
                        header("Location: crud/asistencia.php");
                    }
                    exit;
                } else {
                    $_SESSION['login_intentos']++;
                    $error = "Usuario o contraseña incorrectos.";
                }
            }
        }
    }
}

$timeout_msg = isset($_GET['timeout']) ? "Tu sesión expiró por inactividad." : "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Acceso — Estación de Bomberos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #111827 0%, #1f2937 60%, #374151 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 25px 60px rgba(0,0,0,.35);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            background: linear-gradient(135deg, #ef4444, #f97316);
            padding: 2rem 1.5rem 1.5rem;
            text-align: center;
            color: #fff;
        }
        .login-header .bi-fire {
            font-size: 2.5rem;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,.25));
        }
        .login-body { padding: 1.75rem; }
        .btn-login {
            background: linear-gradient(135deg, #ef4444, #f97316);
            border: none;
            border-radius: 999px;
            color: #fff;
            font-weight: 600;
            letter-spacing: .03em;
            transition: opacity .15s ease, transform .15s ease;
        }
        .btn-login:hover {
            opacity: .9;
            transform: translateY(-1px);
            color: #fff;
        }
        .form-control:focus { border-color: #ef4444; box-shadow: 0 0 0 .25rem rgba(239,68,68,.2); }
    </style>
</head>
<body>
<div class="login-card animate__animated animate__fadeInUp">
    <div class="login-header">
        <i class="bi bi-fire d-block mb-1"></i>
        <h5 class="mb-0 fw-bold">Estación de Bomberos</h5>
        <div style="font-size:.82rem;opacity:.85;">Sistema de Gestión</div>
    </div>
    <div class="login-body">
        <?php if ($timeout_msg): ?>
            <div class="alert alert-warning py-2 small">
                <i class="bi bi-clock me-1"></i><?php echo htmlspecialchars($timeout_msg); ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small">
                <i class="bi bi-exclamation-triangle me-1"></i><?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post" autocomplete="off" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold small">Usuario</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="usuario" class="form-control" autofocus
                           value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>"
                           autocomplete="username">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control" autocomplete="current-password">
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar
            </button>
        </form>
    </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</body>
</html>
