<?php
// header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($pageTitle)) {
    $pageTitle = "Estación de Bomberos";
}

$_active_page = basename($_SERVER['SCRIPT_FILENAME']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($pageTitle); ?> — Estación de Bomberos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        :root {
            --sidebar-w: 245px;
            --sidebar-bg: #111827;
            --accent:  #ef4444;
            --accent2: #f97316;
        }

        body { background: #f3f4f6; min-height: 100vh; }

        /* ===== SIDEBAR ===== */
        #sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
            transition: transform .3s ease;
        }

        .sidebar-brand {
            padding: 1.2rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
            flex-shrink: 0;
        }

        .sidebar-section-label {
            color: rgba(255,255,255,.35);
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            padding: .75rem 1.25rem .2rem;
        }

        #sidebar .nav-link {
            color: rgba(255,255,255,.72);
            padding: .58rem 1.05rem;
            border-radius: .45rem;
            margin: .1rem .55rem;
            transition: background .18s, color .18s, transform .15s;
            display: flex;
            align-items: center;
            gap: .55rem;
            font-size: .875rem;
            text-decoration: none;
        }

        #sidebar .nav-link .bi {
            font-size: 1rem;
            width: 1.15rem;
            text-align: center;
            flex-shrink: 0;
        }

        #sidebar .nav-link:hover {
            background: rgba(255,255,255,.08);
            color: #fff;
            transform: translateX(3px);
        }

        #sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: #fff !important;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(239,68,68,.3);
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding-bottom: .5rem;
        }

        .sidebar-footer {
            flex-shrink: 0;
            padding: .9rem 1rem;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        /* ===== MAIN CONTENT ===== */
        #main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            padding: 1.75rem 2rem;
        }

        /* ===== COMPONENTS ===== */
        .card-soft {
            border-radius: .75rem;
            box-shadow: 0 8px 20px rgba(15,23,42,.08);
            transition: transform .15s ease, box-shadow .15s ease;
            border: none;
        }
        .card-soft:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(15,23,42,.12);
        }

        .btn-soft {
            border-radius: 999px;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .btn-soft:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0,0,0,.15);
        }

        .table-hover tbody tr { transition: background-color .15s ease; }
        .table-hover tbody tr:hover { background-color: #f9fafb; }

        .page-title { animation: fadeInDown .4s ease-out; }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .nav-tabs .nav-link {
            border-radius: 999px 999px 0 0;
            transition: background-color .15s ease, color .15s ease;
        }
        .nav-tabs .nav-link:hover { background-color: #f3f4f6; }
        .nav-tabs .nav-link.active {
            background-color: var(--accent);
            color: #fff !important;
        }

        /* ===== MOBILE ===== */
        .sidebar-toggle {
            display: none;
            position: fixed;
            top: .85rem; left: .85rem;
            z-index: 200;
            background: var(--sidebar-bg);
            border: none; color: white;
            padding: .45rem .65rem;
            border-radius: .4rem;
            font-size: 1.2rem;
            line-height: 1;
            cursor: pointer;
        }
        .sidebar-overlay {
            display: none;
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,.45);
            z-index: 99;
        }
        .sidebar-overlay.show { display: block; }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #main-content { margin-left: 0; padding: 1rem; padding-top: 3.5rem; }
            .sidebar-toggle { display: block; }
        }
    </style>
</head>
<body>

<!-- Mobile toggle -->
<button class="sidebar-toggle" id="sidebarToggle" aria-label="Abrir menú">
    <i class="bi bi-list"></i>
</button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ===== SIDEBAR ===== -->
<nav id="sidebar">

    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-fire text-danger" style="font-size:1.6rem;"></i>
            <div>
                <div class="text-white fw-bold" style="font-size:.9rem;line-height:1.1;">Estación</div>
                <div class="text-white-50" style="font-size:.72rem;">de Bomberos</div>
            </div>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="sidebar-section-label">Operaciones</div>

        <?php if (function_exists('puede_acceder') && puede_acceder('asistencia')): ?>
        <a href="../crud/asistencia.php"
           class="nav-link <?php echo $_active_page === 'asistencia.php' ? 'active' : ''; ?>">
            <i class="bi bi-clock-history"></i> Asistencia
        </a>
        <?php endif; ?>

        <?php if (function_exists('puede_acceder') && puede_acceder('asistencia_rfid')): ?>
        <a href="../crud/asistencia_rfid.php"
           class="nav-link <?php echo $_active_page === 'asistencia_rfid.php' ? 'active' : ''; ?>">
            <i class="bi bi-wifi"></i> Asistencia RFID
        </a>
        <?php endif; ?>

        <?php if (function_exists('puede_acceder') && puede_acceder('servicios')): ?>
        <a href="../crud/servicios.php"
           class="nav-link <?php echo in_array($_active_page, ['servicios.php','servicios_add.php','servicios_edit.php']) ? 'active' : ''; ?>">
            <i class="bi bi-truck-front-fill"></i> Servicios
        </a>
        <?php endif; ?>

        <?php if (function_exists('puede_acceder') && puede_acceder('combustible')): ?>
        <a href="../crud/combustible.php"
           class="nav-link <?php echo in_array($_active_page, ['combustible.php','combustible_edit.php']) ? 'active' : ''; ?>">
            <i class="bi bi-fuel-pump-fill"></i> Combustible
        </a>
        <?php endif; ?>

        <?php if (function_exists('puede_acceder') && puede_acceder('novedades')): ?>
        <a href="../crud/novedades.php"
           class="nav-link <?php echo in_array($_active_page, ['novedades.php','novedades_add.php']) ? 'active' : ''; ?>">
            <i class="bi bi-journal-text"></i> Novedades
        </a>
        <?php endif; ?>

        <?php if (function_exists('puede_acceder') && puede_acceder('despachos')): ?>
        <a href="../crud/despachos.php"
           class="nav-link <?php echo $_active_page === 'despachos.php' ? 'active' : ''; ?>">
            <i class="bi bi-broadcast"></i> Despachos
        </a>
        <?php endif; ?>

        <?php if (function_exists('puede_acceder') && (puede_acceder('bomberos') || puede_acceder('reportes'))): ?>
        <div class="sidebar-section-label" style="margin-top:.5rem;">Administración</div>
        <?php endif; ?>

        <?php if (function_exists('puede_acceder') && puede_acceder('bomberos')): ?>
        <a href="../crud/dashboard.php"
           class="nav-link <?php echo $_active_page === 'dashboard.php' ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="../crud/index.php"
           class="nav-link <?php echo in_array($_active_page, ['index.php','add.php','edit.php','show.php']) ? 'active' : ''; ?>">
            <i class="bi bi-people-fill"></i> Bomberos
        </a>
        <?php endif; ?>

        <?php if (function_exists('puede_acceder') && puede_acceder('reportes')): ?>
        <a href="../crud/reportes.php"
           class="nav-link <?php echo $_active_page === 'reportes.php' ? 'active' : ''; ?>">
            <i class="bi bi-bar-chart-fill"></i> Reportes
        </a>
        <?php endif; ?>
    </div>

    <div class="sidebar-footer">
        <?php if (isset($_SESSION['usuario_id'])): ?>
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-person-circle text-white-50" style="font-size:1.5rem;"></i>
            <div style="overflow:hidden;">
                <div class="text-white fw-semibold text-truncate" style="font-size:.82rem;line-height:1.2;">
                    <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?: ($_SESSION['usuario'] ?? '')); ?>
                </div>
                <div class="text-white-50" style="font-size:.7rem;">
                    <?php echo htmlspecialchars(get_rol()); ?>
                </div>
            </div>
        </div>
        <a href="../logout.php" class="btn btn-sm btn-outline-light btn-soft w-100">
            <i class="bi bi-box-arrow-right me-1"></i> Salir
        </a>
        <?php endif; ?>
    </div>
</nav>

<!-- ===== MAIN CONTENT ===== -->
<div id="main-content">
<?php
// Flash message
$_flash = get_flash();
if ($_flash):
?>
<div class="alert alert-<?php echo htmlspecialchars($_flash['type']); ?> alert-dismissible fade show animate__animated animate__fadeInDown mb-3" role="alert">
    <i class="bi <?php echo $_flash['type'] === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle'; ?> me-2"></i>
    <?php echo htmlspecialchars($_flash['msg']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
