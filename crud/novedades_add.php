<?php
$pageTitle = "Registrar Novedad";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_modulo('novedades');
validar_csrf();

$servicios = $con->query("SELECT s.id, s.fecha_servicio, s.descripcion, u.nombre AS unidad
    FROM servicios s INNER JOIN unidades u ON u.id = s.unidad_id
    ORDER BY s.fecha_servicio DESC, s.hora_reporte DESC LIMIT 50");

$unidades = $con->query("SELECT id, nombre FROM unidades ORDER BY nombre ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servicio_id = !empty($_POST['servicio_id']) ? (int)$_POST['servicio_id'] : null;
    $hora        = trim($_POST['hora'] ?? '') ?: date('H:i:s');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $unidad_id   = !empty($_POST['unidad_id'])  ? (int)$_POST['unidad_id']  : null;
    $mando       = trim($_POST['mando'] ?? '');
    $bomberos_n  = (isset($_POST['bomberos']) && $_POST['bomberos'] !== '') ? (int)$_POST['bomberos'] : null;
    $calle       = trim($_POST['calle'] ?? '');
    $cruce       = trim($_POST['cruce'] ?? '');
    $colonia     = trim($_POST['colonia'] ?? '');
    $municipio   = trim($_POST['municipio'] ?? '');

    $stmt = $con->prepare("INSERT INTO novedades (
        servicio_id, hora, descripcion, unidad_id, mando, bomberos, calle, cruce, colonia, municipio
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isisisisss",
        $servicio_id, $hora, $descripcion, $unidad_id, $mando, $bomberos_n,
        $calle, $cruce, $colonia, $municipio);
    $stmt->execute();
    $stmt->close();

    set_flash('success', 'Novedad registrada correctamente.');
    header("Location: novedades.php");
    exit;
}

require_once "../includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Registrar Novedad</h2>
    <a href="novedades.php" class="btn btn-secondary">Volver a lista</a>
</div>

<form method="post" class="row g-3">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

    <!-- Asociar a un servicio (opcional) -->
    <div class="col-md-6">
        <label class="form-label">Servicio (opcional)</label>
        <select name="servicio_id" class="form-select">
            <option value="">Sin servicio asociado</option>
            <?php while ($s = mysqli_fetch_assoc($servicios)): ?>
                <option value="<?php echo $s['id']; ?>">
                    <?php echo $s['fecha_servicio'] . " - " . $s['unidad'] . " - " . substr($s['descripcion'], 0, 30); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- Datos de la novedad -->
    <div class="col-md-3">
        <label class="form-label">Hora</label>
        <input type="time" name="hora" class="form-control"
               value="<?php echo date('H:i'); ?>">
    </div>

    <div class="col-12">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control" rows="3" required></textarea>
    </div>

    <!-- Unidad y personal -->
    <div class="col-md-4">
        <label class="form-label">Unidad</label>
        <select name="unidad_id" class="form-select">
            <option value="">(Opcional, si no viene del servicio)</option>
            <?php mysqli_data_seek($unidades, 0); while ($u = mysqli_fetch_assoc($unidades)): ?>
                <option value="<?php echo $u['id']; ?>">
                    <?php echo htmlspecialchars($u['nombre']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Mando</label>
        <input type="text" name="mando" class="form-control">
    </div>
    <div class="col-md-2">
        <label class="form-label">Bomberos (cantidad)</label>
        <input type="number" name="bomberos" class="form-control" min="0">
    </div>

    <!-- Ubicación -->
    <div class="col-md-4">
        <label class="form-label">Calle</label>
        <input type="text" name="calle" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Cruce con</label>
        <input type="text" name="cruce" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Colonia</label>
        <input type="text" name="colonia" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Municipio</label>
        <input type="text" name="municipio" class="form-control" value="Linares, N.L.">
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-success">Guardar novedad</button>
        <a href="novedades.php" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<?php require_once "../includes/footer.php"; ?>
