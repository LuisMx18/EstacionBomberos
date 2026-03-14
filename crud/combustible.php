<?php
$pageTitle = "Control de Combustible";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_modulo('combustible');
require_once "../includes/header.php";

$sql = "
    SELECT c.id,
           s.id AS servicio_id,
           s.fecha_servicio,
           s.hora_salida AS hora_salida_servicio,
           u.nombre AS unidad,
           c.hora_salida,
           c.hora_regreso,
           c.kilometraje,
           c.nivel_combustible,
           c.descripcion
    FROM combustible c
    INNER JOIN servicios s ON s.id = c.servicio_id
    INNER JOIN unidades u ON u.id = s.unidad_id
    ORDER BY s.fecha_servicio DESC, c.hora_salida DESC
    LIMIT 100
";
$registros = mysqli_query($con, $sql);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Control de Combustible</h2>
    <a href="servicios.php" class="btn btn-secondary">Ir a servicios</a>
</div>

<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>Fecha</th>
            <th>Unidad</th>
            <th>Hora salida</th>
            <th>Hora regreso</th>
            <th>Km</th>
            <th>Nivel comb.</th>
            <th>Editar</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($c = mysqli_fetch_assoc($registros)): ?>
        <tr>
            <td><?php echo $c['fecha_servicio']; ?></td>
            <td><?php echo htmlspecialchars($c['unidad']); ?></td>
            <td><?php echo substr($c['hora_salida'], 0, 5); ?></td>
            <td><?php echo $c['hora_regreso'] ? substr($c['hora_regreso'], 0, 5) : '-'; ?></td>
            <td><?php echo $c['kilometraje']; ?></td>
            <td><?php echo htmlspecialchars($c['nivel_combustible']); ?></td>
            <td>
                <a href="combustible_edit.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-warning">
                    Editar
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php require_once "../includes/footer.php"; ?>
