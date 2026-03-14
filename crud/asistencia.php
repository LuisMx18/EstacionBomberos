<?php
$pageTitle = "Lista de Asistencia";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_modulo('asistencia');
validar_csrf();

date_default_timezone_set('America/Monterrey');

$fecha_seleccionada = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

// Registrar asistencia (entrada)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bombero_id'])) {
    $bombero_id   = (int)$_POST['bombero_id'];
    $fecha        = $_POST['fecha'] ?: date('Y-m-d');
    $turno_inicio = $_POST['turno_inicio'];
    $turno_fin    = $_POST['turno_fin'];
    $hora_actual  = date('H:i:s');

    // Obtener horas mínimas del bombero
    $stmtB = $con->prepare("SELECT horas_turno_minimo FROM bomberos WHERE id = ? LIMIT 1");
    $stmtB->bind_param("i", $bombero_id);
    $stmtB->execute();
    $rowBom    = $stmtB->get_result()->fetch_assoc();
    $stmtB->close();
    $horas_min = (int)($rowBom['horas_turno_minimo'] ?? 8);

    $llego_tarde = (strtotime($hora_actual) > strtotime($turno_inicio)) ? 1 : 0;

    // Evitar duplicado
    $stmtC = $con->prepare("SELECT id FROM asistencias WHERE bombero_id = ? AND fecha = ? LIMIT 1");
    $stmtC->bind_param("is", $bombero_id, $fecha);
    $stmtC->execute();
    $stmtC->store_result();
    $existe = $stmtC->num_rows;
    $stmtC->close();

    if ($existe == 0) {
        $stmtIns = $con->prepare("INSERT INTO asistencias (
            bombero_id, fecha, hora_entrada, hora_salida,
            horas_turno, llego_tarde, hora_registro,
            turno_inicio, turno_fin, horas_turno_minimo
        ) VALUES (?, ?, ?, NULL, NULL, ?, ?, ?, ?, ?)");
        $stmtIns->bind_param("issiissi",
            $bombero_id, $fecha, $hora_actual,
            $llego_tarde, $hora_actual,
            $turno_inicio, $turno_fin, $horas_min);
        $stmtIns->execute();
        $stmtIns->close();
    }

    header("Location: asistencia.php?fecha=" . urlencode($fecha));
    exit;
}

// Queries para display (DESPUÉS del POST processing)
$bomberos    = $con->query("SELECT id, nombre, puesto FROM bomberos ORDER BY nombre ASC");
$stmtAs      = $con->prepare("SELECT a.*, b.nombre, b.puesto FROM asistencias a INNER JOIN bomberos b ON b.id = a.bombero_id WHERE a.fecha = ? ORDER BY a.hora_entrada ASC");
$stmtAs->bind_param("s", $fecha_seleccionada);
$stmtAs->execute();
$asistencias = $stmtAs->get_result();
$stmtAs->close();

require_once "../includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3 page-title">
    <h2 class="mb-0">
        <i class="bi bi-people-fill text-danger me-2"></i>
        Asistencia
    </h2>
    <a href="index.php" class="btn btn-outline-secondary btn-soft">
        <i class="bi bi-arrow-left-short me-1"></i>
        Volver a Bomberos
    </a>
</div>

<div class="card card-soft p-3 mb-3 animate__animated animate__fadeIn">
    <!-- Filtro por fecha -->
    <form method="get" class="row g-3 mb-2">
        <div class="col-md-4">
            <label class="form-label">Seleccionar día</label>
            <input type="date" name="fecha" class="form-control"
                   value="<?php echo htmlspecialchars($fecha_seleccionada); ?>">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100 btn-soft">
                <i class="bi bi-search me-1"></i>
                Ver
            </button>
        </div>
    </form>

    <?php mysqli_data_seek($bomberos, 0); ?>

    <!-- Formulario de PDF rápido -->
    <form method="get" action="reporte_asistencia_pdf.php" class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Tipo de reporte</label>
            <select name="tipo" class="form-select" required>
                <option value="dia">Diario</option>
                <option value="semana">Semana</option>
                <option value="mes">Mensual</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Fecha base</label>
            <input type="date" name="fecha" class="form-control"
                   value="<?php echo htmlspecialchars($fecha_seleccionada); ?>" required>
            <small class="text-muted">Para semana/mes se toma esta fecha como referencia.</small>
        </div>

        <div class="col-md-3">
            <label class="form-label">Bombero (opcional)</label>
            <select name="bombero_id" class="form-select">
                <option value="">Todos</option>
                <?php while ($b = mysqli_fetch_assoc($bomberos)): ?>
                    <option value="<?php echo $b['id']; ?>">
                        <?php echo htmlspecialchars($b['nombre'] . " (" . $b['puesto'] . ")"); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-danger w-100 btn-soft">
                <i class="bi bi-file-earmark-pdf me-1"></i>
                Descargar PDF
            </button>
        </div>
    </form>
</div>

<?php
// volver a cargar la lista de bomberos para el formulario de registro
$bomberos = mysqli_query($con, "SELECT id, nombre, puesto FROM bomberos ORDER BY nombre ASC");
?>

<!-- Registro de asistencia (entrada) -->
<div class="card card-soft mb-4 animate__animated animate__fadeIn">
    <div class="card-header">
        Registrar asistencia del día: <?php echo htmlspecialchars($fecha_seleccionada); ?>
        <small class="text-muted ms-2">
            (hora actual: <?php echo date('H:i:s'); ?>)
        </small>
    </div>
    <div class="card-body">
        <form method="post" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha_seleccionada); ?>">

            <div class="col-md-4">
                <label class="form-label">Bombero</label>
                <select name="bombero_id" class="form-select" required>
                    <option value="">Selecciona un bombero</option>
                    <?php while ($b = mysqli_fetch_assoc($bomberos)): ?>
                        <option value="<?php echo $b['id']; ?>">
                            <?php echo htmlspecialchars($b['nombre'] . " (" . $b['puesto'] . ")"); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Inicio turno</label>
                <input type="time" name="turno_inicio" class="form-control" required value="08:00">
            </div>

            <div class="col-md-2">
                <label class="form-label">Fin turno</label>
                <input type="time" name="turno_fin" class="form-control" required value="20:00">
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100 btn-soft">
                    <i class="bi bi-clock-history me-1"></i>
                    Registrar entrada (hora actual)
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card card-soft p-3 animate__animated animate__fadeIn">
    <h3 class="mb-3">
        <i class="bi bi-list-check me-2"></i>
        Registros del día <?php echo htmlspecialchars($fecha_seleccionada); ?>
    </h3>

    <table class="table table-striped table-hover mb-0">
        <thead class="table-dark">
            <tr>
                <th>Bombero</th>
                <th>Puesto</th>
                <th>Turno</th>
                <th>Hora entrada</th>
                <th>Hora salida</th>
                <th>Horas trabajadas</th>
                <th>Meta</th>
                <th>Llegó tarde</th>
                <th>Hora registro</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($asistencias) == 0): ?>
            <tr>
                <td colspan="9" class="text-center">Sin registros para esta fecha.</td>
            </tr>
        <?php endif; ?>

        <?php while ($a = mysqli_fetch_assoc($asistencias)): ?>
            <?php
            $meta = isset($a['horas_turno_minimo']) ? (int)$a['horas_turno_minimo'] : 8;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($a['nombre']); ?></td>
                <td><?php echo htmlspecialchars($a['puesto']); ?></td>
                <td>
                    <?php echo $a['turno_inicio'] ? substr($a['turno_inicio'], 0, 5) : '-'; ?> -
                    <?php echo $a['turno_fin']    ? substr($a['turno_fin'], 0, 5)    : '-'; ?>
                </td>
                <td><?php echo $a['hora_entrada'] ? substr($a['hora_entrada'], 0, 5) : '-'; ?></td>
                <td><?php echo $a['hora_salida']  ? substr($a['hora_salida'], 0, 5)  : '-'; ?></td>
                <td>
                    <?php
                    if (!is_null($a['horas_turno'])) {
                        echo $a['horas_turno'] . " h";
                        if ($a['horas_turno'] < $meta) {
                            echo ' <span class="badge bg-warning text-dark">Incompleto</span>';
                        } elseif ($a['horas_turno'] == $meta) {
                            echo ' <span class="badge bg-success">OK</span>';
                        } else {
                            $extras = round($a['horas_turno'] - $meta, 2);
                            echo ' <span class="badge bg-info text-dark">+' . $extras . ' h extra</span>';
                        }
                    } else {
                        echo "-";
                    }
                    ?>
                </td>
                <td><?php echo $meta; ?> h</td>
                <td>
                    <?php if ($a['hora_entrada']): ?>
                        <?php if ($a['llego_tarde']): ?>
                            <span class="badge bg-danger">Tarde</span>
                        <?php else: ?>
                            <span class="badge bg-success">A tiempo</span>
                        <?php endif; ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td><?php echo $a['hora_registro'] ? substr($a['hora_registro'], 0, 8) : '-'; ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once "../includes/footer.php"; ?>
