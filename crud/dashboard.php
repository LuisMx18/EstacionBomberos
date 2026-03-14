<?php
$pageTitle = "Panel de Control";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_modulo('bomberos');

date_default_timezone_set('America/Monterrey');

$hoy       = date('Y-m-d');
$mes_ini   = date('Y-m-01');
$mes_fin   = date('Y-m-t');
$mes_label = strftime('%B %Y') ?: date('m/Y');

// ── Bomberos ──────────────────────────────────────────────
$r = $con->query("SELECT COUNT(*) AS total FROM bomberos WHERE estado = 1");
$bomberos_activos = (int)$r->fetch_assoc()['total'];

$r = $con->query("SELECT COUNT(*) AS total FROM bomberos");
$bomberos_total = (int)$r->fetch_assoc()['total'];

// ── Servicios ─────────────────────────────────────────────
$stmt = $con->prepare("SELECT COUNT(*) AS total FROM servicios WHERE fecha_servicio BETWEEN ? AND ?");
$stmt->bind_param("ss", $mes_ini, $mes_fin);
$stmt->execute();
$servicios_mes = (int)$stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$r = $con->query("SELECT COUNT(*) AS total FROM servicios");
$servicios_total = (int)$r->fetch_assoc()['total'];

// ── Asistencias hoy ───────────────────────────────────────
$stmt = $con->prepare("SELECT COUNT(*) AS total FROM asistencias WHERE fecha = ?");
$stmt->bind_param("s", $hoy);
$stmt->execute();
$asistencias_hoy = (int)$stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$stmt = $con->prepare("SELECT COUNT(*) AS total FROM asistencias WHERE fecha = ? AND hora_salida IS NULL");
$stmt->bind_param("s", $hoy);
$stmt->execute();
$en_turno = (int)$stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// ── Novedades hoy ─────────────────────────────────────────
$stmt = $con->prepare("SELECT COUNT(*) AS total FROM novedades WHERE fecha = ?");
$stmt->bind_param("s", $hoy);
$stmt->execute();
$novedades_hoy = (int)$stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// ── Despachos activos (sin hora regreso) ──────────────────
$r = $con->query("SELECT COUNT(*) AS total FROM despachos WHERE hora_regreso IS NULL");
$despachos_activos = (int)$r->fetch_assoc()['total'];

// ── Servicios por tipo este mes ───────────────────────────
$stmt = $con->prepare(
    "SELECT tipo_incidente, COUNT(*) AS cnt
     FROM servicios
     WHERE fecha_servicio BETWEEN ? AND ?
     GROUP BY tipo_incidente
     ORDER BY cnt DESC
     LIMIT 6"
);
$stmt->bind_param("ss", $mes_ini, $mes_fin);
$stmt->execute();
$tipos_result = $stmt->get_result();
$tipos_labels = [];
$tipos_data   = [];
while ($t = $tipos_result->fetch_assoc()) {
    $tipos_labels[] = $t['tipo_incidente'] ?: 'Sin tipo';
    $tipos_data[]   = (int)$t['cnt'];
}
$stmt->close();

// ── Asistencias últimos 7 días ────────────────────────────
$asist_7dias_labels = [];
$asist_7dias_data   = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $asist_7dias_labels[] = date('d/m', strtotime($d));
    $stmt = $con->prepare("SELECT COUNT(*) AS cnt FROM asistencias WHERE fecha = ?");
    $stmt->bind_param("s", $d);
    $stmt->execute();
    $asist_7dias_data[] = (int)$stmt->get_result()->fetch_assoc()['cnt'];
    $stmt->close();
}

// ── Últimos 5 servicios ───────────────────────────────────
$ultimos_servicios = $con->query(
    "SELECT s.id, s.fecha_servicio, s.hora_reporte, s.tipo_incidente, s.descripcion, u.nombre AS unidad
     FROM servicios s INNER JOIN unidades u ON u.id = s.unidad_id
     ORDER BY s.fecha_servicio DESC, s.hora_reporte DESC LIMIT 5"
);

// ── Últimas 5 novedades ───────────────────────────────────
$ultimas_novedades = $con->query(
    "SELECT n.id, n.hora, n.descripcion, u.nombre AS unidad
     FROM novedades n LEFT JOIN unidades u ON u.id = n.unidad_id
     ORDER BY n.id DESC LIMIT 5"
);

// ── Bomberos en turno hoy ─────────────────────────────────
$bomberos_turno = $con->prepare(
    "SELECT b.nombre, b.puesto, a.hora_entrada
     FROM asistencias a INNER JOIN bomberos b ON b.id = a.bombero_id
     WHERE a.fecha = ? AND a.hora_salida IS NULL
     ORDER BY a.hora_entrada ASC"
);
$bomberos_turno->bind_param("s", $hoy);
$bomberos_turno->execute();
$turno_result = $bomberos_turno->get_result();
$bomberos_turno->close();

require_once "../includes/header.php";
?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="d-flex align-items-center mb-4 page-title">
    <i class="bi bi-speedometer2 text-danger me-2" style="font-size:1.6rem;"></i>
    <div>
        <h2 class="mb-0">Panel de Control</h2>
        <small class="text-muted"><?php echo date('l d \d\e F \d\e Y'); ?></small>
    </div>
</div>

<!-- ── STAT CARDS ─────────────────────────────────────── -->
<div class="row g-3 mb-4">

    <div class="col-6 col-md-3">
        <div class="card card-soft h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background:rgba(239,68,68,.12);">
                    <i class="bi bi-people-fill text-danger" style="font-size:1.6rem;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-3 lh-1"><?php echo $bomberos_activos; ?></div>
                    <div class="text-muted small">Bomberos activos</div>
                    <div class="text-muted" style="font-size:.72rem;"><?php echo $bomberos_total; ?> en total</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-soft h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background:rgba(249,115,22,.12);">
                    <i class="bi bi-truck-front-fill text-warning" style="font-size:1.6rem;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-3 lh-1"><?php echo $servicios_mes; ?></div>
                    <div class="text-muted small">Servicios este mes</div>
                    <div class="text-muted" style="font-size:.72rem;"><?php echo $servicios_total; ?> en total</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-soft h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background:rgba(34,197,94,.12);">
                    <i class="bi bi-clock-history text-success" style="font-size:1.6rem;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-3 lh-1"><?php echo $asistencias_hoy; ?></div>
                    <div class="text-muted small">Asistencias hoy</div>
                    <div class="text-muted" style="font-size:.72rem;">
                        <?php echo $en_turno; ?> en turno activo
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-soft h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background:rgba(99,102,241,.12);">
                    <i class="bi bi-broadcast text-primary" style="font-size:1.6rem;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-3 lh-1"><?php echo $despachos_activos; ?></div>
                    <div class="text-muted small">Despachos activos</div>
                    <div class="text-muted" style="font-size:.72rem;">sin hora de regreso</div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ── GRÁFICAS ───────────────────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="card card-soft h-100">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">
                    <i class="bi bi-bar-chart me-1 text-danger"></i>
                    Asistencia — últimos 7 días
                </h6>
                <canvas id="chartAsistencia" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card card-soft h-100">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">
                    <i class="bi bi-pie-chart me-1 text-warning"></i>
                    Servicios por tipo — <?php echo date('M Y'); ?>
                </h6>
                <?php if (empty($tipos_labels)): ?>
                    <p class="text-muted small text-center mt-4">Sin servicios este mes.</p>
                <?php else: ?>
                    <canvas id="chartTipos" height="150"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ── TABLAS + TURNO ─────────────────────────────────── -->
<div class="row g-3">

    <!-- Últimos servicios -->
    <div class="col-md-5">
        <div class="card card-soft">
            <div class="card-header bg-transparent fw-semibold">
                <i class="bi bi-truck-front-fill text-danger me-1"></i>
                Últimos servicios
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Unidad</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($ultimos_servicios->num_rows === 0): ?>
                        <tr><td colspan="3" class="text-center text-muted py-3">Sin registros.</td></tr>
                    <?php else: ?>
                    <?php while ($s = $ultimos_servicios->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('d/m', strtotime($s['fecha_servicio'])); ?></td>
                            <td><?php echo htmlspecialchars($s['tipo_incidente'] ?? '—'); ?></td>
                            <td><?php echo htmlspecialchars($s['unidad']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-transparent text-end">
                <a href="servicios.php" class="btn btn-sm btn-outline-danger btn-soft">Ver todos</a>
            </div>
        </div>
    </div>

    <!-- Últimas novedades -->
    <div class="col-md-4">
        <div class="card card-soft">
            <div class="card-header bg-transparent fw-semibold">
                <i class="bi bi-journal-text text-primary me-1"></i>
                Últimas novedades
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr><th>Hora</th><th>Descripción</th></tr>
                    </thead>
                    <tbody>
                    <?php if ($ultimas_novedades->num_rows === 0): ?>
                        <tr><td colspan="2" class="text-center text-muted py-3">Sin novedades.</td></tr>
                    <?php else: ?>
                    <?php while ($n = $ultimas_novedades->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo substr($n['hora'] ?? '--:--', 0, 5); ?></td>
                            <td><?php echo htmlspecialchars(substr($n['descripcion'], 0, 45)); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-transparent text-end">
                <a href="novedades.php" class="btn btn-sm btn-outline-primary btn-soft">Ver todas</a>
            </div>
        </div>
    </div>

    <!-- Bomberos en turno -->
    <div class="col-md-3">
        <div class="card card-soft">
            <div class="card-header bg-transparent fw-semibold">
                <i class="bi bi-person-check-fill text-success me-1"></i>
                En turno hoy
                <?php if ($en_turno): ?>
                    <span class="badge bg-success ms-1"><?php echo $en_turno; ?></span>
                <?php endif; ?>
            </div>
            <ul class="list-group list-group-flush">
            <?php if ($turno_result->num_rows === 0): ?>
                <li class="list-group-item text-muted small text-center py-3">Nadie en turno activo.</li>
            <?php else: ?>
            <?php while ($b = $turno_result->fetch_assoc()): ?>
                <li class="list-group-item py-2">
                    <div class="fw-semibold small"><?php echo htmlspecialchars($b['nombre']); ?></div>
                    <div class="text-muted" style="font-size:.72rem;">
                        <?php echo htmlspecialchars($b['puesto']); ?> · desde <?php echo substr($b['hora_entrada'], 0, 5); ?>
                    </div>
                </li>
            <?php endwhile; ?>
            <?php endif; ?>
            </ul>
            <div class="card-footer bg-transparent text-end">
                <a href="asistencia.php" class="btn btn-sm btn-outline-success btn-soft">Ver asistencia</a>
            </div>
        </div>
    </div>

</div>

<!-- ── ACCESOS RÁPIDOS ────────────────────────────────── -->
<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="card card-soft">
            <div class="card-body">
                <h6 class="fw-semibold mb-3"><i class="bi bi-lightning-fill text-warning me-1"></i> Acciones rápidas</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="servicios_add.php" class="btn btn-outline-danger btn-soft btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Nuevo servicio
                    </a>
                    <a href="novedades_add.php" class="btn btn-outline-primary btn-soft btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Nueva novedad
                    </a>
                    <a href="despachos_add.php" class="btn btn-outline-secondary btn-soft btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Nuevo despacho
                    </a>
                    <a href="add.php" class="btn btn-outline-dark btn-soft btn-sm">
                        <i class="bi bi-person-plus me-1"></i> Agregar bombero
                    </a>
                    <a href="reportes.php" class="btn btn-outline-success btn-soft btn-sm">
                        <i class="bi bi-bar-chart me-1"></i> Ver reportes
                    </a>
                    <a href="asistencia_rfid.php" class="btn btn-outline-warning btn-soft btn-sm">
                        <i class="bi bi-wifi me-1"></i> RFID
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const DANGER = '#ef4444';
const WARN   = '#f97316';
const COLORS = ['#ef4444','#f97316','#f59e0b','#10b981','#3b82f6','#8b5cf6'];

// Asistencia 7 días
new Chart(document.getElementById('chartAsistencia'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($asist_7dias_labels); ?>,
        datasets: [{
            label: 'Asistencias',
            data: <?php echo json_encode($asist_7dias_data); ?>,
            backgroundColor: DANGER + '99',
            borderColor: DANGER,
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

<?php if (!empty($tipos_labels)): ?>
// Servicios por tipo
new Chart(document.getElementById('chartTipos'), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($tipos_labels); ?>,
        datasets: [{
            data: <?php echo json_encode($tipos_data); ?>,
            backgroundColor: COLORS,
            borderWidth: 2,
        }]
    },
    options: {
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 } } }
        }
    }
});
<?php endif; ?>
</script>

<?php require_once "../includes/footer.php"; ?>
