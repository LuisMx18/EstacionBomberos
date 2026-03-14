<?php
// header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($pageTitle)) {
    $pageTitle = "Estación de Bomberos";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Animate.css (animaciones listas) -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        /* Fondo general y tarjetas */
        body {
            background: #f3f4f6;
        }

        .card-soft {
            border-radius: 0.75rem;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .card-soft:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
        }

        /* Botones suaves */
        .btn-soft {
            border-radius: 999px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .btn-soft:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        /* Filas de tabla con efecto hover */
        .table-hover tbody tr {
            transition: background-color 0.15s ease, transform 0.1s ease;
        }

        .table-hover tbody tr:hover {
            background-color: #f9fafb;
            transform: scale(1.005);
        }

        /* Encabezados de sección animados al cargar */
        .page-title {
            animation: fadeInDown 0.4s ease-out;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Nav activa más marcada */
        .navbar .nav-link.active {
            font-weight: 600;
            position: relative;
        }

        .navbar .nav-link.active::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, #ef4444, #f97316);
            border-radius: 999px;
        }

        /* Tabs de reportes más modernas */
        .nav-tabs .nav-link {
            border-radius: 999px 999px 0 0;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .nav-tabs .nav-link:hover {
            background-color: #f3f4f6;
        }

        .nav-tabs .nav-link.active {
            background-color: #ef4444;
            color: #fff !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="../crud/index.php">
            <i class="bi bi-fire me-1 text-danger"></i>
            Estación de Bomberos
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarBomberos" aria-controls="navbarBomberos"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarBomberos">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="../crud/index.php">Bomberos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../crud/reportes.php">Reportes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../crud/asistencia.php">Asistencia</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../crud/asistencia_rfid.php">Asistencia RFID</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../crud/servicios.php">Servicios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../crud/combustible.php">Combustible</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../crud/novedades.php">Novedades</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if (isset($_SESSION['usuario'])): ?>
                    <li class="nav-item">
                        <span class="navbar-text me-2">
                            <i class="bi bi-person-circle me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?: $_SESSION['usuario']); ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a href="../logout.php" class="btn btn-sm btn-outline-light btn-soft">
                            <i class="bi bi-box-arrow-right me-1"></i>
                            Salir
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="../login.php" class="btn btn-sm btn-outline-light btn-soft">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            Entrar
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mb-4">
