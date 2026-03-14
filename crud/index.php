<?php
$pageTitle = "Lista de Bomberos";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_once "../includes/header.php";

$result = mysqli_query($con, "SELECT id, clave, nombre, numero_empleado, puesto, estado FROM bomberos ORDER BY nombre ASC");
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Bomberos</h2>
    <a href="add.php" class="btn btn-primary">Agregar Bombero</a>
</div>

<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>Clave</th>
            <th>Nombre</th>
            <th>Número de empleado</th>
            <th>Puesto</th>
            <th>Estado</th>
            <th>Ficha</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['clave']); ?></td>
            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
            <td><?php echo htmlspecialchars($row['numero_empleado']); ?></td>
            <td><?php echo htmlspecialchars($row['puesto']); ?></td>
            <td><?php echo $row['estado'] == 1 ? 'Activo' : 'Inactivo'; ?></td>
            <td>
                <a href="show.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                    Ver ficha
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php require_once "../includes/footer.php"; ?>
