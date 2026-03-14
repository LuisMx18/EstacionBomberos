<?php
$pageTitle = "Lista de Bomberos";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_modulo('bomberos');
require_once "../includes/header.php";

$result = $con->query("SELECT id, clave, nombre, numero_empleado, puesto, estado FROM bomberos ORDER BY nombre ASC");
?>

<div class="d-flex justify-content-between align-items-center mb-3 page-title">
    <h2 class="mb-0">
        <i class="bi bi-people-fill text-danger me-2"></i>
        Bomberos
    </h2>
    <a href="add.php" class="btn btn-danger btn-soft">
        <i class="bi bi-plus-lg me-1"></i> Agregar Bombero
    </a>
</div>

<div class="card card-soft animate__animated animate__fadeIn">
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Clave</th>
                    <th>Nombre</th>
                    <th>N° Empleado</th>
                    <th>Puesto</th>
                    <th>Estado</th>
                    <th>Ficha</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['clave']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['numero_empleado']); ?></td>
                    <td><?php echo htmlspecialchars($row['puesto']); ?></td>
                    <td>
                        <?php if ($row['estado'] == 1): ?>
                            <span class="badge bg-success">Activo</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="show.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary btn-soft">
                            <i class="bi bi-eye me-1"></i> Ver ficha
                        </a>
                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary btn-soft">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
