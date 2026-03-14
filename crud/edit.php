<?php
$pageTitle = "Editar Bombero";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_modulo('bomberos');
validar_csrf();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $con->prepare("SELECT * FROM bomberos WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$b = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$b) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre               = trim($_POST['nombre'] ?? '');
    $numero_empleado      = trim($_POST['numero_empleado'] ?? '');
    $puesto               = trim($_POST['puesto'] ?? '');
    $clave                = trim($_POST['clave'] ?? '');
    $telefono             = trim($_POST['telefono'] ?? '');
    $edad                 = (int)($_POST['edad'] ?? 0);
    $domicilio            = trim($_POST['domicilio'] ?? '');
    $tipo_sangre          = trim($_POST['tipo_sangre'] ?? '');
    $contacto1            = trim($_POST['contacto_emergencia1'] ?? '');
    $contacto2            = trim($_POST['contacto_emergencia2'] ?? '');
    $alergias             = trim($_POST['alergias'] ?? '');
    $enfermedades         = trim($_POST['enfermedades'] ?? '');
    $correo_inst          = trim($_POST['correo_institucional'] ?? '');
    $correo_pers          = trim($_POST['correo_personal'] ?? '');
    $rfid                 = trim($_POST['rfid_codigo'] ?? '') ?: null;
    $estado               = (int)($_POST['estado'] ?? 1);
    $horas_turno_minimo   = (int)($_POST['horas_turno_minimo'] ?? 8);

    if (!in_array($horas_turno_minimo, [8, 12, 24], true)) {
        $horas_turno_minimo = 8;
    }

    $stmt = $con->prepare("UPDATE bomberos SET
        nombre = ?, numero_empleado = ?, puesto = ?, clave = ?,
        telefono = ?, edad = ?, domicilio = ?, tipo_sangre = ?,
        contacto_emergencia1 = ?, contacto_emergencia2 = ?,
        alergias = ?, enfermedades = ?,
        correo_institucional = ?, correo_personal = ?,
        rfid_codigo = ?, estado = ?, horas_turno_minimo = ?
        WHERE id = ?");

    $stmt->bind_param(
        "sssssississsssssiiii",
        $nombre, $numero_empleado, $puesto, $clave,
        $telefono, $edad, $domicilio, $tipo_sangre,
        $contacto1, $contacto2,
        $alergias, $enfermedades,
        $correo_inst, $correo_pers,
        $rfid, $estado, $horas_turno_minimo,
        $id
    );
    $stmt->execute();
    $stmt->close();

    set_flash('success', 'Bombero actualizado correctamente.');
    header("Location: index.php");
    exit;
}

require_once "../includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3 page-title">
    <h2 class="mb-0"><i class="bi bi-pencil-square text-danger me-2"></i>Editar Bombero</h2>
    <a href="index.php" class="btn btn-outline-secondary btn-soft">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card card-soft animate__animated animate__fadeIn">
    <div class="card-body">
        <form method="post" class="row g-3" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="col-md-5">
                <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                <input type="text" name="nombre" class="form-control"
                       value="<?php echo htmlspecialchars($b['nombre']); ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Número de empleado</label>
                <input type="text" name="numero_empleado" class="form-control"
                       value="<?php echo htmlspecialchars($b['numero_empleado']); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Clave</label>
                <input type="text" name="clave" class="form-control"
                       value="<?php echo htmlspecialchars($b['clave']); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Puesto</label>
                <input type="text" name="puesto" class="form-control"
                       value="<?php echo htmlspecialchars($b['puesto']); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control"
                       value="<?php echo htmlspecialchars($b['telefono']); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Edad</label>
                <input type="number" name="edad" class="form-control" min="0"
                       value="<?php echo (int)$b['edad']; ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Domicilio</label>
                <input type="text" name="domicilio" class="form-control"
                       value="<?php echo htmlspecialchars($b['domicilio']); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Tipo de sangre</label>
                <input type="text" name="tipo_sangre" class="form-control"
                       value="<?php echo htmlspecialchars($b['tipo_sangre']); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Contacto de emergencia 1</label>
                <input type="text" name="contacto_emergencia1" class="form-control"
                       value="<?php echo htmlspecialchars($b['contacto_emergencia1']); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Contacto de emergencia 2</label>
                <input type="text" name="contacto_emergencia2" class="form-control"
                       value="<?php echo htmlspecialchars($b['contacto_emergencia2']); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Alergias</label>
                <textarea name="alergias" class="form-control" rows="2"><?php echo htmlspecialchars($b['alergias']); ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Enfermedades</label>
                <textarea name="enfermedades" class="form-control" rows="2"><?php echo htmlspecialchars($b['enfermedades']); ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Correo institucional</label>
                <input type="email" name="correo_institucional" class="form-control"
                       value="<?php echo htmlspecialchars($b['correo_institucional']); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Correo personal</label>
                <input type="email" name="correo_personal" class="form-control"
                       value="<?php echo htmlspecialchars($b['correo_personal']); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Código RFID</label>
                <input type="text" name="rfid_codigo" class="form-control"
                       value="<?php echo htmlspecialchars($b['rfid_codigo'] ?? ''); ?>"
                       placeholder="0003608755">
                <div class="form-text">Pasa la tarjeta si el lector escribe el código.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="1" <?php echo $b['estado'] == 1 ? 'selected' : ''; ?>>Activo</option>
                    <option value="2" <?php echo $b['estado'] == 2 ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Horas mínimas de turno</label>
                <?php $ht = (int)($b['horas_turno_minimo'] ?? 8); ?>
                <select name="horas_turno_minimo" class="form-select">
                    <option value="8"  <?php echo $ht == 8  ? 'selected' : ''; ?>>8 horas</option>
                    <option value="12" <?php echo $ht == 12 ? 'selected' : ''; ?>>12 horas</option>
                    <option value="24" <?php echo $ht == 24 ? 'selected' : ''; ?>>24 horas</option>
                </select>
                <div class="form-text">Usado para validar asistencia y horas extra.</div>
            </div>
            <div class="col-12 mt-2">
                <button type="submit" class="btn btn-primary btn-soft">
                    <i class="bi bi-check-lg me-1"></i> Actualizar
                </button>
                <a href="index.php" class="btn btn-outline-secondary btn-soft ms-2">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
