<?php
$pageTitle = "Editar Servicio";
require_once "../config/conexion.php";
require_once "../includes/auth.php";

// validar id
if (empty($_GET['id'])) {
    header("Location: servicios.php");
    exit;
}
$id = (int) $_GET['id'];

// obtener unidades
$unidades = mysqli_query($con, "SELECT id, nombre FROM unidades ORDER BY nombre ASC");

// obtener servicio
$sqlServ = "
    SELECT *
    FROM servicios
    WHERE id = $id
    LIMIT 1
";
$resServ = mysqli_query($con, $sqlServ);
$servicio = mysqli_fetch_assoc($resServ);

if (!$servicio) {
    header("Location: servicios.php");
    exit;
}

// procesar envío
// procesar envío
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha_servicio   = $_POST['fecha_servicio'];
    $hora_reporte     = $_POST['hora_reporte'] ?: $servicio['hora_reporte'];
    $hora_salida      = $_POST['hora_salida']  ?: $servicio['hora_salida'];
    $hora_llegada     = $_POST['hora_llegada'] ?: null;
    $hora_regreso     = $_POST['hora_regreso'] ?: null;

    $nombre_reporta   = mysqli_real_escape_string($con, $_POST['nombre_reporta']);
    $telefono_reporta = mysqli_real_escape_string($con, $_POST['telefono_reporta']);

    $unidad_id        = (int) $_POST['unidad_id'];
    $mando            = mysqli_real_escape_string($con, $_POST['mando']);
    $bomberos         = (int) $_POST['bomberos'];

    $descripcion      = mysqli_real_escape_string($con, $_POST['descripcion']);
    $turno_numero     = (int) $_POST['turno_numero'];

    $calle            = mysqli_real_escape_string($con, $_POST['calle']);
    $cruce            = mysqli_real_escape_string($con, $_POST['cruce']);
    $colonia          = mysqli_real_escape_string($con, $_POST['colonia']);
    $municipio        = mysqli_real_escape_string($con, $_POST['municipio']);

    $despachador      = mysqli_real_escape_string($con, $_POST['despachador']);
    $tiempo_motobomba = (int) $_POST['tiempo_motobomba'];
    $litros_agua      = (int) $_POST['litros_agua'];

    $kilometraje      = (int) $_POST['kilometraje'];
    $nivel_combustible = mysqli_real_escape_string($con, $_POST['nivel_combustible']);

    // Nuevos campos
    $tipo_incidente   = mysqli_real_escape_string($con, $_POST['tipo_incidente'] ?? '');
    $prioridad        = mysqli_real_escape_string($con, $_POST['prioridad'] ?? '');
    $condiciones      = mysqli_real_escape_string($con, $_POST['condiciones'] ?? '');

    $lesionados       = (int) ($_POST['lesionados'] ?? 0);
    $fallecidos       = (int) ($_POST['fallecidos'] ?? 0);
    $danios           = mysqli_real_escape_string($con, $_POST['danios'] ?? '');

    $acciones         = mysqli_real_escape_string($con, $_POST['acciones'] ?? '');
    $responsable_rep  = mysqli_real_escape_string($con, $_POST['responsable_reporte'] ?? '');
    $observaciones    = mysqli_real_escape_string($con, $_POST['observaciones'] ?? '');

    $sqlUpdate = "
        UPDATE servicios SET
            fecha_servicio    = '$fecha_servicio',
            hora_reporte      = '$hora_reporte',
            hora_salida       = '$hora_salida',
            hora_llegada      = " . ($hora_llegada ? "'$hora_llegada'" : "NULL") . ",
            hora_regreso      = " . ($hora_regreso ? "'$hora_regreso'" : "NULL") . ",
            nombre_reporta    = '$nombre_reporta',
            telefono_reporta  = '$telefono_reporta',
            unidad_id         = $unidad_id,
            mando             = '$mando',
            bomberos          = $bomberos,
            lesionados        = $lesionados,
            fallecidos        = $fallecidos,
            danios            = '$danios',
            descripcion       = '$descripcion',
            tipo_incidente    = '$tipo_incidente',
            prioridad         = '$prioridad',
            condiciones       = '$condiciones',
            turno_numero      = $turno_numero,
            calle             = '$calle',
            cruce             = '$cruce',
            colonia           = '$colonia',
            municipio         = '$municipio',
            despachador       = '$despachador',
            responsable_reporte = '$responsable_rep',
            observaciones     = '$observaciones',
            tiempo_motobomba  = $tiempo_motobomba,
            litros_agua       = $litros_agua,
            kilometraje       = $kilometraje,
            nivel_combustible = '$nivel_combustible',
            acciones          = '$acciones'
        WHERE id = $id
        LIMIT 1
    ";

    mysqli_query($con, $sqlUpdate);

    header("Location: servicios.php");
    exit;
}


require_once "../includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-3 page-title">
    <h2 class="mb-0">
        <i class="bi bi-pencil-square text-warning me-2"></i>
        Editar Servicio
    </h2>
    <a href="servicios.php" class="btn btn-outline-secondary btn-soft">
        <i class="bi bi-arrow-left-short me-1"></i>
        Volver a lista
    </a>
</div>

<div class="card card-soft p-3 animate__animated animate__fadeIn">
    <form method="post" class="row g-3">
        <!-- Datos de corporación -->
        <div class="col-12 mb-2">
            <h5 class="text-uppercase small text-danger fw-bold mb-2">
                <i class="bi bi-building me-1"></i> Datos de corporación
            </h5>
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted text-uppercase">Estación / base</label>
            <input type="text" class="form-control" value="Estación de Bomberos Linares" disabled>
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted text-uppercase">Unidad / camión</label>
            <select name="unidad_id" class="form-select" required>
                <option value="">Selecciona una unidad</option>
                <?php mysqli_data_seek($unidades, 0); ?>
                <?php while ($u = mysqli_fetch_assoc($unidades)): ?>
                    <option value="<?php echo $u['id']; ?>"
                        <?php echo ($u['id'] == $servicio['unidad_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($u['nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted text-uppercase">Mando</label>
            <input type="text" name="mando" class="form-control"
                   value="<?php echo htmlspecialchars($servicio['mando']); ?>" required>
        </div>

        <!-- Tiempos del servicio -->
        <div class="col-12 mt-3">
            <h6 class="text-uppercase small text-danger fw-bold mb-2">
                <i class="bi bi-clock-history me-1"></i> Tiempos del servicio
            </h6>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted text-uppercase">Fecha del servicio</label>
            <input type="date" name="fecha_servicio" class="form-control"
                   value="<?php echo htmlspecialchars($servicio['fecha_servicio']); ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted text-uppercase">Hora reporte</label>
            <input type="time" name="hora_reporte" class="form-control"
                   value="<?php echo substr($servicio['hora_reporte'], 0, 5); ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted text-uppercase">Hora salida</label>
            <input type="time" name="hora_salida" class="form-control"
                   value="<?php echo substr($servicio['hora_salida'], 0, 5); ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted text-uppercase">Hora llegada</label>
            <input type="time" name="hora_llegada" class="form-control"
                   value="<?php echo $servicio['hora_llegada'] ? substr($servicio['hora_llegada'], 0, 5) : ''; ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted text-uppercase">Hora regreso</label>
            <input type="time" name="hora_regreso" class="form-control"
                   value="<?php echo $servicio['hora_regreso'] ? substr($servicio['hora_regreso'], 0, 5) : ''; ?>">
        </div>

        <!-- Datos del incidente -->
        <div class="col-12 mt-3">
            <h6 class="text-uppercase small text-danger fw-bold mb-2">
                <i class="bi bi-exclamation-triangle me-1"></i> Datos del incidente
            </h6>
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted text-uppercase">Tipo de incidente</label>
            <select name="tipo_incidente" class="form-select">
                <?php $ti = $servicio['tipo_incidente']; ?>
                <option value="" <?php echo $ti==''?'selected':''; ?>>Selecciona...</option>
                <option value="incendio"      <?php echo $ti=='incendio'?'selected':''; ?>>Incendio</option>
                <option value="rescate"       <?php echo $ti=='rescate'?'selected':''; ?>>Rescate</option>
                <option value="fuga_gas"      <?php echo $ti=='fuga_gas'?'selected':''; ?>>Fuga de gas</option>
                <option value="accidente_vial"<?php echo $ti=='accidente_vial'?'selected':''; ?>>Accidente vial</option>
                <option value="apoyo_medico"  <?php echo $ti=='apoyo_medico'?'selected':''; ?>>Apoyo médico</option>
                <option value="falsa_alarma"  <?php echo $ti=='falsa_alarma'?'selected':''; ?>>Falsa alarma</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted text-uppercase">Prioridad</label>
            <?php $pr = $servicio['prioridad']; ?>
            <select name="prioridad" class="form-select">
                <option value=""        <?php echo $pr==''?'selected':''; ?>>Normal</option>
                <option value="alta"    <?php echo $pr=='alta'?'selected':''; ?>>Alta</option>
                <option value="critica" <?php echo $pr=='critica'?'selected':''; ?>>Crítica</option>
            </select>
        </div>
        <div class="col-md-5">
            <label class="form-label small text-muted text-uppercase">Condiciones especiales</label>
            <input type="text" name="condiciones" class="form-control"
                   value="<?php echo htmlspecialchars($servicio['condiciones']); ?>">
        </div>

        <!-- Datos de quien reporta y personal -->
        <div class="col-12 mt-3">
            <h6 class="text-uppercase small text-danger fw-bold mb-2">
                <i class="bi bi-person-lines-fill me-1"></i> Datos de quien reporta y personal
            </h6>
        </div>
        <div class="col-md-5">
            <label class="form-label small text-muted text-uppercase">Nombre de quien reporta</label>
            <input type="text" name="nombre_reporta" class="form-control"
                   value="<?php echo htmlspecialchars($servicio['nombre_reporta']); ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted text-uppercase">Teléfono</label>
            <input type="text" name="telefono_reporta" class="form-control"
                   value="<?php echo htmlspecialchars($servicio['telefono_reporta']); ?>" required>
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted text-uppercase">Bomberos</label>
            <input type="number" name="bomberos" class="form-control" min="0"
                   value="<?php echo (int)$servicio['bomberos']; ?>" required>
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted text-uppercase">Turno</label>
            <select name="turno_numero" class="form-select" required>
                <option value="1" <?php echo $servicio['turno_numero']==1?'selected':''; ?>>Turno 1</option>
                <option value="2" <?php echo $servicio['turno_numero']==2?'selected':''; ?>>Turno 2</option>
                <option value="3" <?php echo $servicio['turno_numero']==3?'selected':''; ?>>Turno 3</option>
            </select>
        </div>

        <!-- Afectados y daños -->
        <div class="col-12 mt-3">
            <h6 class="text-uppercase small text-danger fw-bold mb-2">
                <i class="bi bi-people me-1"></i> Afectados y daños
            </h6>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted text-uppercase">Lesionados</label>
            <input type="number" name="lesionados" class="form-control" min="0"
                   value="<?php echo (int)$servicio['lesionados']; ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted text-uppercase">Fallecidos</label>
            <input type="number" name="fallecidos" class="form-control" min="0"
                   value="<?php echo (int)$servicio['fallecidos']; ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label small text-muted text-uppercase">Daños materiales</label>
            <input type="text" name="danios" class="form-control"
                   value="<?php echo htmlspecialchars($servicio['danios']); ?>">
        </div>

        <!-- Ubicación -->
        <div class="col-12 mt-3">
            <h6 class="text-uppercase small text-danger fw-bold mb-2">
                <i class="bi bi-geo-alt me-1"></i> Ubicación del incidente
            </h6>
        </div>
        <div class="col-md-6">
            <label class="form-label small text-muted text-uppercase">Calle</label>
            <input type="text" name="calle" class="form-control"
                   value="<?php echo htmlspecialchars($servicio['calle']); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label small text-muted text-uppercase">Cruce con</label>
            <input type="text" name="cruce" class="form-control"
                   value="<?php echo htmlspecialchars($servicio['cruce']); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label small text-muted text-uppercase">Colonia</label>
            <input type="text" name="colonia" class="form-control"
                   value="<?php echo htmlspecialchars($servicio['colonia']); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label small text-muted text-uppercase">Municipio</label>
            <input type="text" name="municipio" class="form-control"
                   value="<?php echo htmlspecialchars($servicio['municipio']); ?>">
        </div>

        <!-- Recursos y combustible -->
        <div class="col-12 mt-3">
            <h6 class="text-uppercase small text-danger fw-bold mb-2">
                <i class="bi bi-fuel-pump me-1"></i> Recursos y combustible
            </h6>
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted text-uppercase">Despachador</label>
            <input type="text" name="despachador" class="form-control"
                   value="<?php echo htmlspecialchars($servicio['despachador']); ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted text-uppercase">Tiempo motobomba (min)</label>
            <input type="number" name="tiempo_motobomba" class="form-control" min="0"
                   value="<?php echo (int)$servicio['tiempo_motobomba']; ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted text-uppercase">Litros de agua</label>
            <input type="number" name="litros_agua" class="form-control" min="0"
                   value="<?php echo (int)$servicio['litros_agua']; ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted text-uppercase">Kilometraje (salida)</label>
            <input type="number" name="kilometraje" class="form-control" min="0"
                   value="<?php echo (int)$servicio['kilometraje']; ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted text-uppercase">Nivel de combustible</label>
            <input type="text" name="nivel_combustible" class="form-control"
                   value="<?php echo htmlspecialchars($servicio['nivel_combustible']); ?>">
        </div>

        <!-- Descripción y acciones -->
        <div class="col-12 mt-3">
            <h6 class="text-uppercase small text-danger fw-bold mb-2">
                <i class="bi bi-journal-text me-1"></i> Descripción y acciones
            </h6>
        </div>
        <div class="col-12">
            <label class="form-label small text-muted text-uppercase">Descripción general</label>
            <textarea name="descripcion" class="form-control" rows="3"><?php
                echo htmlspecialchars($servicio['descripcion']);
            ?></textarea>
        </div>
        <div class="col-12">
            <label class="form-label small text-muted text-uppercase">Acciones realizadas</label>
            <textarea name="acciones" class="form-control" rows="3"><?php
                echo htmlspecialchars($servicio['acciones']);
            ?></textarea>
        </div>

        <!-- Cierre del reporte -->
        <div class="col-12 mt-3">
            <h6 class="text-uppercase small text-danger fw-bold mb-2">
                <i class="bi bi-pen me-1"></i> Cierre del reporte
            </h6>
        </div>
        <div class="col-md-6">
            <label class="form-label small text-muted text-uppercase">Responsable del reporte</label>
            <input type="text" name="responsable_reporte" class="form-control"
                   value="<?php echo htmlspecialchars($servicio['responsable_reporte']); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label small text-muted text-uppercase">Observaciones / seguimiento</label>
            <input type="text" name="observaciones" class="form-control"
                   value="<?php echo htmlspecialchars($servicio['observaciones']); ?>">
        </div>

        <div class="col-12 d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-success btn-soft">
                <i class="bi bi-save me-1"></i>
                Guardar cambios
            </button>
            <a href="servicios.php" class="btn btn-outline-secondary btn-soft">
                <i class="bi bi-x-circle me-1"></i>
                Cancelar
            </a>
        </div>
    </form>
</div>

<?php require_once "../includes/footer.php"; ?>

