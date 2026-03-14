<?php
session_start();
require_once "config/conexion.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];

    if ($usuario === "" || $password === "") {
        $error = "Ingresa usuario y contraseña.";
    } else {
        $u = mysqli_real_escape_string($con, $usuario);
        $sql = "SELECT id, usuario, password_hash, nombre, rol FROM usuarios WHERE usuario = '$u' LIMIT 1";
        $res = mysqli_query($con, $sql);

        if ($row = mysqli_fetch_assoc($res)) {
            if (password_verify($password, $row['password_hash'])) {
                // Login OK
                $_SESSION['usuario_id']   = $row['id'];
                $_SESSION['usuario']      = $row['usuario'];
                $_SESSION['usuario_nombre'] = $row['nombre'];
                $_SESSION['usuario_rol']  = $row['rol'];

                header("Location: crud/index.php");
                exit;
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "Usuario no encontrado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Ingresar - Estación de Bomberos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow" style="max-width: 400px; width: 100%;">
        <div class="card-header text-center">
            <h4>Acceso al sistema</h4>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="post" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <input type="text" name="usuario" class="form-control" autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
