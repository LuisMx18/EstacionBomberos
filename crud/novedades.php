<?php
$pageTitle = "Reporte de Novedades";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_modulo('novedades');
require_once "../includes/header.php";

$sql = "
    SELECT n.*, 
           s.fecha_servicio,
           u.nombre AS unidad
    FROM novedades n
    LEFT JOIN servicios s ON s.id = n.servicio_id
    LEFT JOIN unidades u ON u.id = n.unidad_id
    ORDER BY n.id DESC
    LIMIT 100
";
$novedades = mysqli_query($con, $sql);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Novedades</h2>
    <a href="novedades_add.php" class="btn btn-primary">Registrar novedad</a>
</div>

<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>Fecha serv.</th>
            <th>Hora novedad</th>
            <th>Unidad</th>
            <th>Mando</th>
            <th>Descripción</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($n = mysqli_fetch_assoc($novedades)): ?>
        <tr>
            <td><?php echo $n['fecha_servicio'] ?: '-'; ?></td>
            <td><?php echo substr($n['hora'], 0, 5); ?></td>
            <td><?php echo htmlspecialchars($n['unidad'] ?: ''); ?></td>
            <td><?php echo htmlspecialchars($n['mando'] ?: ''); ?></td>
            <td><?php echo htmlspecialchars(substr($n['descripcion'], 0, 40)); ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php require_once "../includes/footer.php"; ?>
