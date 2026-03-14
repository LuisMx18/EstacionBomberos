<?php
$pageTitle = "Agregar Bombero";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_modulo('bomberos');
validar_csrf();

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

    $stmt = $con->prepare("INSERT INTO bomberos (
        nombre, numero_empleado, puesto, clave,
        telefono, edad, domicilio, tipo_sangre,
        contacto_emergencia1, contacto_emergencia2,
        alergias, enfermedades,
        correo_institucional, correo_personal,
        rfid_codigo, estado
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "sssssississssssi",
        $nombre, $numero_empleado, $puesto, $clave,
        $telefono, $edad, $domicilio, $tipo_sangre,
        $contacto1, $contacto2,
        $alergias, $enfermedades,
        $correo_inst, $correo_pers,
        $rfid, $estado
    );
    $stmt->execute();
    $stmt->close();

    set_flash('success', 'Bombero agregado correctamente.');
    header("Location: index.php");
    exit;
}

require_once "../includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3 page-title">
    <h2 class="mb-0"><i class="bi bi-person-plus-fill text-danger me-2"></i>Agregar Bombero</h2>
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
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Número de empleado</label>
                <input type="text" name="numero_empleado" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Clave</label>
                <input type="text" name="clave" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Puesto</label>
                <input type="text" name="puesto" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Edad</label>
                <input type="number" name="edad" class="form-control" min="0">
            </div>
            <div class="col-md-6">
                <label class="form-label">Domicilio</label>
                <input type="text" name="domicilio" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Tipo de sangre</label>
                <input type="text" name="tipo_sangre" class="form-control" placeholder="O+, A-…">
            </div>
            <div class="col-md-6">
                <label class="form-label">Contacto de emergencia 1</label>
                <input type="text" name="contacto_emergencia1" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Contacto de emergencia 2</label>
                <input type="text" name="contacto_emergencia2" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Alergias</label>
                <textarea name="alergias" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Enfermedades</label>
                <textarea name="enfermedades" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Correo institucional</label>
                <input type="email" name="correo_institucional" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Correo personal</label>
                <input type="email" name="correo_personal" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Código RFID</label>
                <input type="text" name="rfid_codigo" class="form-control" placeholder="0003608755">
                <div class="form-text">Pasa la tarjeta si el lector escribe el código.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="1">Activo</option>
                    <option value="2">Inactivo</option>
                </select>
            </div>
            <div class="col-12 mt-2">
                <button type="submit" class="btn btn-success btn-soft">
                    <i class="bi bi-check-lg me-1"></i> Guardar
                </button>
                <a href="index.php" class="btn btn-outline-secondary btn-soft ms-2">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
