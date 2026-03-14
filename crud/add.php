<?php
$pageTitle = "Agregar Bombero";
require_once "../config/conexion.php";
require_once "../includes/auth.php";

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

    $sql = "INSERT INTO bomberos (
                nombre, numero_empleado, puesto, clave,
                telefono, edad, domicilio, tipo_sangre,
                contacto_emergencia1, contacto_emergencia2,
                alergias, enfermedades,
                correo_institucional, correo_personal,
                rfid_codigo,
                estado
            ) VALUES (
                '$nombre',
                '$numero_empleado',
                '$puesto',
                '$clave',
                '$telefono',
                $edad,
                '$domicilio',
                '$tipo_sangre',
                '$contacto_emergencia1',
                '$contacto_emergencia2',
                '$alergias',
                '$enfermedades',
                '$correo_institucional',
                '$correo_personal',
                " . ($rfid_codigo !== '' ? "'$rfid_codigo'" : "NULL") . ",
                $estado
            )";

    mysqli_query($con, $sql);
    header("Location: index.php");
    exit;
}

require_once "../includes/header.php";
?>

<h2>Agregar Bombero</h2>

<form method="post" class="row g-3">
    <div class="col-md-5">
        <label class="form-label">Nombre completo</label>
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
        <label class="form-label">Número de teléfono</label>
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
        <input type="text" name="tipo_sangre" class="form-control" placeholder="O+, A-, etc.">
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
        <label class="form-label">Código RFID (tarjeta)</label>
        <input type="text" name="rfid_codigo" class="form-control" placeholder="0003608755">
        <div class="form-text">
            Coloca el cursor aquí y pasa la tarjeta si el lector escribe el código.
        </div>
    </div>

    <div class="col-md-3">
        <label class="form-label">Estado</label>
        <select name="estado" class="form-select">
            <option value="1">Activo</option>
            <option value="2">Inactivo</option>
        </select>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<?php require_once "../includes/footer.php"; ?>
