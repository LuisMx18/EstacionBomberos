<?php
$pageTitle = "Editar Bombero";
require_once "../config/conexion.php";
require_once "../includes/auth.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];
$res = mysqli_query($con, "SELECT * FROM bomberos WHERE id = $id");
$b = mysqli_fetch_assoc($res);

if (!$b) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = mysqli_real_escape_string($con, $_POST['nombre']);
    $numero_empleado = mysqli_real_escape_string($con, $_POST['numero_empleado']);
    $puesto   = mysqli_real_escape_string($con, $_POST['puesto']);
    $clave    = mysqli_real_escape_string($con, $_POST['clave']);
    $telefono = mysqli_real_escape_string($con, $_POST['telefono']);
    $edad     = (int) $_POST['edad'];
    $domicilio = mysqli_real_escape_string($con, $_POST['domicilio']);
    $tipo_sangre = mysqli_real_escape_string($con, $_POST['tipo_sangre']);
    $contacto_emergencia1 = mysqli_real_escape_string($con, $_POST['contacto_emergencia1']);
    $contacto_emergencia2 = mysqli_real_escape_string($con, $_POST['contacto_emergencia2']);
    $alergias = mysqli_real_escape_string($con, $_POST['alergias']);
    $enfermedades = mysqli_real_escape_string($con, $_POST['enfermedades']);
    $correo_institucional = mysqli_real_escape_string($con, $_POST['correo_institucional']);
    $correo_personal = mysqli_real_escape_string($con, $_POST['correo_personal']);
    $rfid_codigo = mysqli_real_escape_string($con, trim($_POST['rfid_codigo']));
    $estado   = (int) $_POST['estado'];

    // nuevo: horas mínimas de turno (8, 12, 24)
    $horas_turno_minimo = (int) ($_POST['horas_turno_minimo'] ?? 8);
    if (!in_array($horas_turno_minimo, [8,12,24], true)) {
        $horas_turno_minimo = 8;
    }

    $sql = "UPDATE bomberos SET
                nombre = '$nombre',
                numero_empleado = '$numero_empleado',
                puesto = '$puesto',
                clave = '$clave',
                telefono = '$telefono',
                edad = $edad,
                domicilio = '$domicilio',
                tipo_sangre = '$tipo_sangre',
                contacto_emergencia1 = '$contacto_emergencia1',
                contacto_emergencia2 = '$contacto_emergencia2',
                alergias = '$alergias',
                enfermedades = '$enfermedades',
                correo_institucional = '$correo_institucional',
                correo_personal = '$correo_personal',
                rfid_codigo = " . ($rfid_codigo !== '' ? "'$rfid_codigo'" : "NULL") . ",
                estado = $estado,
                horas_turno_minimo = $horas_turno_minimo
            WHERE id = $id";

    mysqli_query($con, $sql);
    header("Location: index.php");
    exit;
}

require_once "../includes/header.php";
?>

<h2>Editar Bombero</h2>

<form method="post" class="row g-3">
    <div class="col-md-5">
        <label class="form-label">Nombre completo</label>
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
        <label class="form-label">Número de teléfono</label>
        <input type="text" name="telefono" class="form-control"
               value="<?php echo htmlspecialchars($b['telefono']); ?>">
    </div>
    <div class="col-md-2">
        <label class="form-label">Edad</label>
        <input type="number" name="edad" class="form-control" min="0"
               value="<?php echo htmlspecialchars($b['edad']); ?>">
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
        <textarea name="alergias" class="form-control" rows="2"><?php
            echo htmlspecialchars($b['alergias']);
        ?></textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">Enfermedades</label>
        <textarea name="enfermedades" class="form-control" rows="2"><?php
            echo htmlspecialchars($b['enfermedades']);
        ?></textarea>
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
        <label class="form-label">Código RFID (tarjeta)</label>
        <input type="text" name="rfid_codigo" class="form-control"
               value="<?php echo htmlspecialchars($b['rfid_codigo']); ?>"
               placeholder="0003608755">
        <div class="form-text">
            Puedes escribirlo o pasar la tarjeta si el lector lo envía como teclado.
        </div>
    </div>

    <div class="col-md-3">
        <label class="form-label">Estado</label>
        <select name="estado" class="form-select">
            <option value="1" <?php echo $b['estado'] == 1 ? 'selected' : ''; ?>>Activo</option>
            <option value="2" <?php echo $b['estado'] == 2 ? 'selected' : ''; ?>>Inactivo</option>
        </select>
    </div>

    <!-- NUEVO: horas mínimas de turno -->
    <div class="col-md-3">
        <label class="form-label">Horas mínimas de turno</label>
        <?php $ht = (int)($b['horas_turno_minimo'] ?? 8); ?>
        <select name="horas_turno_minimo" class="form-select" required>
            <option value="8"  <?php echo $ht==8?'selected':''; ?>>8 horas</option>
            <option value="12" <?php echo $ht==12?'selected':''; ?>>12 horas</option>
            <option value="24" <?php echo $ht==24?'selected':''; ?>>24 horas</option>
        </select>
        <div class="form-text">
            Este valor se usará para validar la asistencia y horas extra.
        </div>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<?php require_once "../includes/footer.php"; ?>
