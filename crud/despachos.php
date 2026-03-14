<?php
$pageTitle = "Despacho de Servicios";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_modulo('despachos');
require_once "../includes/header.php";

$despachos = mysqli_query(
    $con,
    "SELECT d.*, s.tipo_servicio, s.fecha AS fecha_servicio, u.nombre AS unidad
     FROM despachos d
     INNER JOIN servicios s ON s.id = d.servicio_id
     INNER JOIN unidades u ON u.id = d.unidad_id
     ORDER BY d.fecha DESC, d.hora_salida DESC
     LIMIT 100"
);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Despachos</h2>
    <a href="despachos_add.php" class="btn btn-primary">Registrar despacho</a>
</div>

<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>Fecha</th>
            <th>Servicio</th>
            <th>Unidad</th>
            <th>Hora salida</th>
            <th>Hora regreso</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($d = mysqli_fetch_assoc($despachos)): ?>
        <tr>
            <td><?php echo $d['fecha']; ?></td>
            <td><?php echo htmlspecialchars($d['tipo_servicio']); ?></td>
            <td><?php echo htmlspecialchars($d['unidad']); ?></td>
            <td><?php echo substr($d['hora_salida'], 0, 5); ?></td>
            <td><?php echo $d['hora_regreso'] ? substr($d['hora_regreso'], 0, 5) : '-'; ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php require_once "../includes/footer.php"; ?>
