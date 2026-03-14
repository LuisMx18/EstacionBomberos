<?php
$pageTitle = "Servicios";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_modulo('servicios');
require_once "../includes/header.php";

$servicios = mysqli_query(
    $con,
    "SELECT s.*, u.nombre AS unidad
     FROM servicios s
     INNER JOIN unidades u ON u.id = s.unidad_id
     ORDER BY s.fecha_servicio DESC, s.hora_reporte DESC
     LIMIT 100"
);
?>

<div class="d-flex justify-content-between align-items-center mb-3 page-title">
    <h2 class="mb-0">
        <i class="bi bi-truck-front text-danger me-2"></i>
        Servicios
    </h2>
    <a href="servicios_add.php" class="btn btn-primary btn-soft">
        <i class="bi bi-plus-lg me-1"></i>
        Registrar servicio
    </a>
</div>

<div class="card card-soft p-3 animate__animated animate__fadeIn">
    <table class="table table-striped table-hover mb-0">
        <thead class="table-dark">
            <tr>
                <th>Fecha</th>
                <th>Hora reporte</th>
                <th>Unidad</th>
                <th>Descripción</th>
                <th>Turno</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($servicios) === 0): ?>
            <tr>
                <td colspan="6" class="text-center">Sin servicios registrados.</td>
            </tr>
        <?php else: ?>
            <?php while ($s = mysqli_fetch_assoc($servicios)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($s['fecha_servicio']); ?></td>
                    <td><?php echo substr($s['hora_reporte'], 0, 5); ?></td>
                    <td><?php echo htmlspecialchars($s['unidad']); ?></td>
                    <td><?php echo htmlspecialchars(substr($s['descripcion'], 0, 60)); ?></td>
                    <td>
                        <span class="badge bg-secondary">
                            Turno <?php echo (int)$s['turno_numero']; ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="servicios_edit.php?id=<?php echo $s['id']; ?>"
                           class="btn btn-sm btn-warning btn-soft">
                            <i class="bi bi-pencil-square me-1"></i>
                            Editar
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once "../includes/footer.php"; ?>
