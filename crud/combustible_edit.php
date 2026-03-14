<?php
$pageTitle = "Editar Combustible";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_modulo('combustible');
validar_csrf();

if (!isset($_GET['id'])) {
    header("Location: combustible.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $con->prepare("SELECT c.*, s.fecha_servicio, u.nombre AS unidad
    FROM combustible c
    INNER JOIN servicios s ON s.id = c.servicio_id
    INNER JOIN unidades u ON u.id = s.unidad_id
    WHERE c.id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$comb = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$comb) {
    header("Location: combustible.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hora_salida  = $_POST['hora_salida']  ?: $comb['hora_salida'];
    $hora_regreso = $_POST['hora_regreso'] ?: null;
    $kilometraje  = (int)($_POST['kilometraje'] ?? 0);
    $nivel        = trim($_POST['nivel_combustible'] ?? '');
    $descripcion  = trim($_POST['descripcion'] ?? '');

    $stmt = $con->prepare("UPDATE combustible SET hora_salida = ?, hora_regreso = ?, kilometraje = ?, nivel_combustible = ?, descripcion = ? WHERE id = ?");
    $stmt->bind_param("ssissi", $hora_salida, $hora_regreso, $kilometraje, $nivel, $descripcion, $id);
    $stmt->execute();
    $stmt->close();

    set_flash('success', 'Registro de combustible actualizado.');
    header("Location: combustible.php");
    exit;
}

require_once "../includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3 page-title">
    <h2 class="mb-0"><i class="bi bi-fuel-pump-fill text-danger me-2"></i>Editar Combustible</h2>
    <a href="combustible.php" class="btn btn-outline-secondary btn-soft">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card card-soft animate__animated animate__fadeIn">
    <div class="card-body">
        <form method="post" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="col-md-4">
                <label class="form-label">Fecha del servicio</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($comb['fecha_servicio']); ?>" disabled>
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
                       value="<?php echo (int)$comb['kilometraje']; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Nivel de combustible</label>
                <input type="text" name="nivel_combustible" class="form-control"
                       value="<?php echo htmlspecialchars($comb['nivel_combustible']); ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($comb['descripcion']); ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success btn-soft">
                    <i class="bi bi-check-lg me-1"></i> Guardar
                </button>
                <a href="combustible.php" class="btn btn-outline-secondary btn-soft ms-2">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
