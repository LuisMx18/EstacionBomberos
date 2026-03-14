<?php
$pageTitle = "Reportes";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_once "../includes/header.php";

date_default_timezone_set('America/Monterrey');

// parámetros de filtros
$fecha_inicio = $_GET['inicio'] ?? date('Y-m-01');
$fecha_fin    = $_GET['fin']    ?? date('Y-m-d');
$bombero_id   = !empty($_GET['bombero_id']) ? (int) $_GET['bombero_id'] : null;

// listas para selects
$bomberos = mysqli_query($con, "SELECT id, nombre FROM bomberos ORDER BY nombre ASC");
$unidades = mysqli_query($con, "SELECT id, nombre FROM unidades ORDER BY nombre ASC");

// --- Consulta de asistencia ---
$whereAsis = "a.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
if ($bombero_id) {
    $whereAsis .= " AND a.bombero_id = $bombero_id";
}

$sqlAsis = "
    SELECT a.*, b.nombre, b.puesto
    FROM asistencias a
    INNER JOIN bomberos b ON b.id = a.bombero_id
    WHERE $whereAsis
    ORDER BY a.fecha ASC, a.hora_entrada ASC
";
$asis = mysqli_query($con, $sqlAsis);

// --- Consulta de servicios ---
$whereServ = "s.fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'";
$unidad_id = !empty($_GET['unidad_id']) ? (int) $_GET['unidad_id'] : null;
if ($unidad_id) {
    $whereServ .= " AND s.unidad_id = $unidad_id";
}

$sqlServ = "
    SELECT s.*, u.nombre AS unidad
    FROM servicios s
    INNER JOIN unidades u ON u.id = s.unidad_id
    WHERE $whereServ
    ORDER BY s.fecha_servicio DESC, s.hora_reporte DESC
";
$servicios = mysqli_query($con, $sqlServ);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Reportes</h2>
    <a href="index.php" class="btn btn-secondary">Volver a Bomberos</a>
</div>

<form method="get" class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label">Desde</label>
        <input type="date" name="inicio" class="form-control"
               value="<?php echo htmlspecialchars($fecha_inicio); ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Hasta</label>
        <input type="date" name="fin" class="form-control"
               value="<?php echo htmlspecialchars($fecha_fin); ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Bombero (para asistencia)</label>
        <select name="bombero_id" class="form-select">
            <option value="">Todos</option>
            <?php mysqli_data_seek($bomberos, 0); ?>
            <?php while ($b = mysqli_fetch_assoc($bomberos)): ?>
                <option value="<?php echo $b['id']; ?>"
                    <?php echo ($bombero_id == $b['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($b['nombre']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Unidad (para servicios)</label>
        <select name="unidad_id" class="form-select">
            <option value="">Todas</option>
            <?php mysqli_data_seek($unidades, 0); ?>
            <?php while ($u = mysqli_fetch_assoc($unidades)): ?>
                <option value="<?php echo $u['id']; ?>"
                    <?php echo ($unidad_id == $u['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($u['nombre']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-12 d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-primary">Ver</button>

        <!-- PDF asistencia -->
        <a class="btn btn-danger"
           href="reporte_asistencia_pdf.php?tipo=personalizado
                &inicio=<?php echo htmlspecialchars($fecha_inicio); ?>
                &fin=<?php echo htmlspecialchars($fecha_fin); ?>
                &bombero_id=<?php echo $bombero_id ?: ''; ?>">
            PDF Asistencia
        </a>

        <!-- PDF servicios -->
        <a class="btn btn-danger"
           href="reporte_servicios_pdf.php
                ?inicio=<?php echo htmlspecialchars($fecha_inicio); ?>
                &fin=<?php echo htmlspecialchars($fecha_fin); ?>
                &unidad_id=<?php echo $unidad_id ?: ''; ?>">
            PDF Servicios
        </a>
    </div>
</form>

<ul class="nav nav-tabs mb-3" id="tabReportes" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-asistencia" data-bs-toggle="tab"
                data-bs-target="#asistencia" type="button" role="tab">
            Asistencia
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-servicios" data-bs-toggle="tab"
                data-bs-target="#servicios" type="button" role="tab">
            Servicios
        </button>
    </li>
</ul>

<div class="tab-content" id="tabReportesContent">
    <!-- TAB ASISTENCIA -->
    <div class="tab-pane fade show active" id="asistencia" role="tabpanel">
        <h4>Asistencia</h4>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Bombero</th>
                    <th>Puesto</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th>Horas</th>
                    <th>Tarde</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($asis) == 0): ?>
                <tr><td colspan="7" class="text-center">Sin registros en el periodo.</td></tr>
            <?php else: ?>
                <?php while ($a = mysqli_fetch_assoc($asis)): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($a['fecha'])); ?></td>
                        <td><?php echo htmlspecialchars($a['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($a['puesto']); ?></td>
                        <td><?php echo $a['hora_entrada'] ? substr($a['hora_entrada'], 0, 5) : '-'; ?></td>
                        <td><?php echo $a['hora_salida']  ? substr($a['hora_salida'], 0, 5)  : '-'; ?></td>
                        <td>
                            <?php
                            if (!is_null($a['horas_turno'])) {
                                echo $a['horas_turno'] . " h";
                                if ($a['horas_turno'] < 8) {
                                    echo " <span class=\"badge bg-warning text-dark\">Inc.</span>";
                                }
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>
                        <td><?php echo $a['llego_tarde'] ? 'Sí' : 'No'; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- TAB SERVICIOS -->
    <div class="tab-pane fade" id="servicios" role="tabpanel">
        <h4>Servicios</h4>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Hora reporte</th>
                    <th>Unidad</th>
                    <th>Descripción</th>
                    <th>Turno</th>
                    <th>Mando</th>
                    <th>Bomberos</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($servicios) == 0): ?>
                <tr><td colspan="7" class="text-center">Sin servicios en el periodo.</td></tr>
            <?php else: ?>
                <?php while ($s = mysqli_fetch_assoc($servicios)): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($s['fecha_servicio'])); ?></td>
                        <td><?php echo substr($s['hora_reporte'], 0, 5); ?></td>
                        <td><?php echo htmlspecialchars($s['unidad']); ?></td>
                        <td><?php echo htmlspecialchars(substr($s['descripcion'], 0, 60)); ?></td>
                        <td><?php echo $s['turno_numero']; ?></td>
                        <td><?php echo htmlspecialchars($s['mando']); ?></td>
                        <td><?php echo (int)$s['bomberos']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>