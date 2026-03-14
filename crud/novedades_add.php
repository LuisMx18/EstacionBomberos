<?php
$pageTitle = "Registrar Novedad";
require_once "../config/conexion.php";
require_once "../includes/auth.php";

// Servicios recientes para elegir (opcional)
$servicios = mysqli_query(
    $con,
    "SELECT s.id,
            s.fecha_servicio,
            s.descripcion,
            u.nombre AS unidad
     FROM servicios s
     INNER JOIN unidades u ON u.id = s.unidad_id
     ORDER BY s.fecha_servicio DESC, s.hora_reporte DESC
     LIMIT 50"
);

// Unidades para novedad suelta
$unidades = mysqli_query($con, "SELECT id, nombre FROM unidades ORDER BY nombre ASC");

// Guardar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servicio_id = !empty($_POST['servicio_id']) ? (int) $_POST['servicio_id'] : "NULL";
    $hora        = $_POST['hora'] ?: date('H:i:s');
    $descripcion = mysqli_real_escape_string($con, $_POST['descripcion']);

    $unidad_id   = !empty($_POST['unidad_id']) ? (int) $_POST['unidad_id'] : "NULL";
    $mando       = mysqli_real_escape_string($con, $_POST['mando']);
    $bomberos    = $_POST['bomberos'] !== '' ? (int) $_POST['bomberos'] : "NULL";
    $calle       = mysqli_real_escape_string($con, $_POST['calle']);
    $cruce       = mysqli_real_escape_string($con, $_POST['cruce']);
    $colonia     = mysqli_real_escape_string($con, $_POST['colonia']);
    $municipio   = mysqli_real_escape_string($con, $_POST['municipio']);

    $sql = "
        INSERT INTO novedades (
            servicio_id, hora, descripcion,
            unidad_id, mando, bomberos,
            calle, cruce, colonia, municipio
        ) VALUES (
            $servicio_id,
            '$hora',
            '$descripcion',
            $unidad_id,
            '$mando',
            $bomberos,
            '$calle',
            '$cruce',
            '$colonia',
            '$municipio'
        )
    ";
    mysqli_query($con, $sql);

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
