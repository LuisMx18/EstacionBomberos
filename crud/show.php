<?php
$pageTitle = "Ficha de Bombero";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_modulo('bomberos');

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];
$stmt = $con->prepare("SELECT * FROM bomberos WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$b = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$b) {
    header("Location: index.php");
    exit;
}

require_once "../includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Ficha de Bombero</h2>
    <div>
        <a href="edit.php?id=<?php echo $b['id']; ?>" class="btn btn-warning">Editar</a>
        <a href="index.php" class="btn btn-secondary">Volver a lista</a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        Datos generales
    </div>
    <div class="card-body row">
        <div class="col-md-4">
            <p><strong>Nombre completo:</strong><br>
                <?php echo htmlspecialchars($b['nombre']); ?></p>
            <p><strong>Número de empleado:</strong><br>
                <?php echo htmlspecialchars($b['numero_empleado']); ?></p>
            <p><strong>Clave:</strong><br>
                <?php echo htmlspecialchars($b['clave']); ?></p>
        </div>
        <div class="col-md-4">
            <p><strong>Puesto:</strong><br>
                <?php echo htmlspecialchars($b['puesto']); ?></p>
            <p><strong>Teléfono:</strong><br>
                <?php echo htmlspecialchars($b['telefono']); ?></p>
            <p><strong>Edad:</strong><br>
                <?php echo htmlspecialchars($b['edad']); ?></p>
        </div>
        <div class="col-md-4">
            <p><strong>Domicilio:</strong><br>
                <?php echo htmlspecialchars($b['domicilio']); ?></p>
            <p><strong>Tipo de sangre:</strong><br>
                <?php echo htmlspecialchars($b['tipo_sangre']); ?></p>
            <p><strong>Código RFID:</strong><br>
                <?php echo htmlspecialchars($b['rfid_codigo']); ?></p>
            <p><strong>Estado:</strong><br>
                <?php echo $b['estado'] == 1 ? 'Activo' : 'Inactivo'; ?></p>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        Contactos de emergencia
    </div>
    <div class="card-body row">
        <div class="col-md-6">
            <p><strong>Contacto de emergencia 1:</strong><br>
                <?php echo nl2br(htmlspecialchars($b['contacto_emergencia1'])); ?></p>
        </div>
        <div class="col-md-6">
            <p><strong>Contacto de emergencia 2:</strong><br>
                <?php echo nl2br(htmlspecialchars($b['contacto_emergencia2'])); ?></p>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        Información médica
    </div>
    <div class="card-body row">
        <div class="col-md-6">
            <p><strong>Alergias:</strong><br>
                <?php echo nl2br(htmlspecialchars($b['alergias'])); ?></p>
        </div>
        <div class="col-md-6">
            <p><strong>Enfermedades:</strong><br>
                <?php echo nl2br(htmlspecialchars($b['enfermedades'])); ?></p>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        Correos
    </div>
    <div class="card-body row">
        <div class="col-md-6">
            <p><strong>Correo institucional:</strong><br>
                <?php echo htmlspecialchars($b['correo_institucional']); ?></p>
        </div>
        <div class="col-md-6">
            <p><strong>Correo personal:</strong><br>
                <?php echo htmlspecialchars($b['correo_personal']); ?></p>
        </div>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
