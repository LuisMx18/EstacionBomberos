<?php
$pageTitle = "Editar Combustible";
require_once "../config/conexion.php";
require_once "../includes/auth.php";

if (!isset($_GET['id'])) {
    header("Location: combustible.php");
    exit;
}

$id = (int) $_GET['id'];

// Cargar registro
$sql = "
    SELECT c.*, s.fecha_servicio, u.nombre AS unidad
    FROM combustible c
    INNER JOIN servicios s ON s.id = c.servicio_id
    INNER JOIN unidades u ON u.id = s.unidad_id
    WHERE c.id = $id
";
$res = mysqli_query($con, $sql);
$comb = mysqli_fetch_assoc($res);

if (!$comb) {
    header("Location: combustible.php");
    exit;
}

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hora_salida   = $_POST['hora_salida'] ?: $comb['hora_salida'];
    $hora_regreso  = $_POST['hora_regreso'] ?: null;
    $kilometraje   = (int) $_POST['kilometraje'];
    $nivel         = mysqli_real_escape_string($con, $_POST['nivel_combustible']);
    $descripcion   = mysqli_real_escape_string($con, $_POST['descripcion']);

    $sqlUpdate = "
        UPDATE combustible
        SET hora_salida = '$hora_salida',
            hora_regreso = " . ($hora_regreso ? "'$hora_regreso'" : "NULL") . ",
            kilometraje = $kilometraje,
            nivel_combustible = '$nivel',
            descripcion = '$descripcion'
        WHERE id = $id
    ";

    mysqli_query($con, $sqlUpdate);
    header("Location: combustible.php");
    exit;
}

require_once "../includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Editar Combustible</h2>
    <a href="combustible.php" class="btn btn-secondary">Volver</a>
</div>

<form method="post" class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Fecha del servicio</label>
        <input type="text" class="form-control" value="<?php echo $comb['fecha_servicio']; ?>" disabled>
    </div>
    <div class="col-md-4">
        <label class="form-label">Unidad</label>
        <input type="text" class="form-control" value="<?php echo htmlspecialchars($comb['unidad']); ?>" disabled>
    </div>

    <div class="col-md-3">
        <label class="form-label">Hora salida</label>
        <input type="time" name="hora_salida" class="form-control"
               value="<?php echo substr($comb['hora_salida'], 0, 5); ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Hora regreso</label>
        <input type="time" name="hora_regreso" class="form-control"
               value="<?php echo $comb['hora_regreso'] ? substr($comb['hora_regreso'], 0, 5) : ''; ?>">
    </div>

    <div class="col-md-3">
        <label class="form-label">Kilometraje</label>
        <input type="number" name="kilometraje" class="form-control"
               value="<?php echo $comb['kilometraje']; ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Nivel de combustible</label>
        <input type="text" name="nivel_combustible" class="form-control"
               value="<?php echo htmlspecialchars($comb['nivel_combustible']); ?>">
    </div>

    <div class="col-12">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control" rows="3"><?php
            echo htmlspecialchars($comb['descripcion']);
        ?></textarea>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="combustible.php" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<?php require_once "../includes/footer.php"; ?>
