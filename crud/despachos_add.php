<?php
$pageTitle = "Registrar Despacho";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_modulo('despachos');
validar_csrf();

$servicios = $con->query(
    "SELECT s.id, s.fecha_servicio, s.tipo_incidente, s.descripcion, u.nombre AS unidad
     FROM servicios s INNER JOIN unidades u ON u.id = s.unidad_id
     ORDER BY s.fecha_servicio DESC, s.hora_reporte DESC LIMIT 50"
);

$unidades = $con->query("SELECT id, nombre FROM unidades ORDER BY nombre ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servicio_id  = (int)($_POST['servicio_id'] ?? 0);
    $unidad_id    = (int)($_POST['unidad_id'] ?? 0);
    $fecha        = trim($_POST['fecha'] ?? date('Y-m-d'));
    $hora_salida  = trim($_POST['hora_salida'] ?? date('H:i'));
    $hora_regreso = trim($_POST['hora_regreso'] ?? '') ?: null;
    $conductor    = trim($_POST['conductor'] ?? '');
    $personal     = ($_POST['personal'] !== '') ? (int)$_POST['personal'] : null;
    $observaciones = trim($_POST['observaciones'] ?? '');

    $stmt = $con->prepare(
        "INSERT INTO despachos (servicio_id, unidad_id, fecha, hora_salida, hora_regreso, conductor, personal, observaciones)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("iissssis",
        $servicio_id, $unidad_id, $fecha, $hora_salida,
        $hora_regreso, $conductor, $personal, $observaciones
    );
    $stmt->execute();
    $stmt->close();

    set_flash('success', 'Despacho registrado correctamente.');
    header("Location: despachos.php");
    exit;
}

require_once "../includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3 page-title">
    <h2 class="mb-0">
        <i class="bi bi-broadcast text-danger me-2"></i>
        Registrar Despacho
    </h2>
    <a href="despachos.php" class="btn btn-outline-secondary btn-soft">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card card-soft animate__animated animate__fadeIn">
    <div class="card-body">
        <form method="post" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="col-md-6">
                <label class="form-label">Servicio asociado <span class="text-danger">*</span></label>
                <select name="servicio_id" class="form-select" required>
                    <option value="">— Selecciona un servicio —</option>
                    <?php while ($s = $servicios->fetch_assoc()): ?>
                        <option value="<?php echo $s['id']; ?>">
                            <?php echo $s['fecha_servicio'] . ' | ' . htmlspecialchars($s['unidad']) . ' | ' . htmlspecialchars($s['tipo_incidente'] ?? '') . ' — ' . htmlspecialchars(substr($s['descripcion'] ?? '', 0, 35)); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Unidad despachada <span class="text-danger">*</span></label>
                <select name="unidad_id" class="form-select" required>
                    <option value="">— Selecciona unidad —</option>
                    <?php while ($u = $unidades->fetch_assoc()): ?>
                        <option value="<?php echo $u['id']; ?>">
                            <?php echo htmlspecialchars($u['nombre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control"
                       value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Hora de salida</label>
                <input type="time" name="hora_salida" class="form-control"
                       value="<?php echo date('H:i'); ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Hora de regreso</label>
                <input type="time" name="hora_regreso" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Conductor</label>
                <input type="text" name="conductor" class="form-control" placeholder="Nombre del conductor">
            </div>
            <div class="col-md-2">
                <label class="form-label">Personal</label>
                <input type="number" name="personal" class="form-control" min="1" placeholder="#">
            </div>

            <div class="col-12">
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="2"></textarea>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-danger btn-soft">
                    <i class="bi bi-check-lg me-1"></i> Guardar despacho
                </button>
                <a href="despachos.php" class="btn btn-outline-secondary btn-soft ms-2">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
