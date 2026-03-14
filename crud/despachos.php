<?php
$pageTitle = "Despachos";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_modulo('despachos');
require_once "../includes/header.php";

$stmt = $con->prepare(
    "SELECT d.*, s.tipo_incidente, s.fecha_servicio, s.descripcion AS desc_servicio, u.nombre AS unidad
     FROM despachos d
     INNER JOIN servicios s ON s.id = d.servicio_id
     INNER JOIN unidades u ON u.id = d.unidad_id
     ORDER BY d.fecha DESC, d.hora_salida DESC
     LIMIT 100"
);
$stmt->execute();
$despachos = $stmt->get_result();
$stmt->close();
?>

<div class="d-flex justify-content-between align-items-center mb-3 page-title">
    <h2 class="mb-0">
        <i class="bi bi-broadcast text-danger me-2"></i>
        Despachos
    </h2>
    <a href="despachos_add.php" class="btn btn-danger btn-soft">
        <i class="bi bi-plus-lg me-1"></i> Registrar despacho
    </a>
</div>

<div class="card card-soft animate__animated animate__fadeIn">
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Servicio</th>
                    <th>Unidad</th>
                    <th>Conductor</th>
                    <th>Personal</th>
                    <th>Hora salida</th>
                    <th>Hora regreso</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($despachos->num_rows === 0): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Sin despachos registrados.</td></tr>
            <?php else: ?>
            <?php while ($d = $despachos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($d['fecha'])); ?></td>
                    <td>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($d['tipo_incidente'] ?? '—'); ?></span><br>
                        <small class="text-muted"><?php echo htmlspecialchars(substr($d['desc_servicio'] ?? '', 0, 40)); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($d['unidad']); ?></td>
                    <td><?php echo htmlspecialchars($d['conductor'] ?? '—'); ?></td>
                    <td><?php echo $d['personal'] !== null ? (int)$d['personal'] : '—'; ?></td>
                    <td><?php echo substr($d['hora_salida'], 0, 5); ?></td>
                    <td>
                        <?php if ($d['hora_regreso']): ?>
                            <?php echo substr($d['hora_regreso'], 0, 5); ?>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">En servicio</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
