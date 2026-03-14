<?php
$pageTitle = "Editar Servicio";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_modulo('servicios');
validar_csrf();

// validar id
if (empty($_GET['id'])) {
    header("Location: servicios.php");
    exit;
}
$id = (int) $_GET['id'];

$unidades = $con->query("SELECT id, nombre FROM unidades ORDER BY nombre ASC");

$stmtS = $con->prepare("SELECT * FROM servicios WHERE id = ? LIMIT 1");
$stmtS->bind_param("i", $id);
$stmtS->execute();
$servicio = $stmtS->get_result()->fetch_assoc();
$stmtS->close();

if (!$servicio) {
    header("Location: servicios.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha_servicio   = trim($_POST['fecha_servicio'] ?? '');
    $hora_reporte     = trim($_POST['hora_reporte']   ?? '') ?: $servicio['hora_reporte'];
    $hora_salida      = trim($_POST['hora_salida']    ?? '') ?: $servicio['hora_salida'];
    $hora_llegada     = trim($_POST['hora_llegada']   ?? '') ?: null;
    $hora_regreso     = trim($_POST['hora_regreso']   ?? '') ?: null;
    $nombre_reporta   = trim($_POST['nombre_reporta']   ?? '');
    $telefono_reporta = trim($_POST['telefono_reporta'] ?? '');
    $unidad_id        = (int)($_POST['unidad_id']  ?? 0);
    $mando            = trim($_POST['mando']        ?? '');
    $bomberos         = (int)($_POST['bomberos']    ?? 1);
    $lesionados       = (int)($_POST['lesionados']  ?? 0);
    $fallecidos       = (int)($_POST['fallecidos']  ?? 0);
    $danios           = trim($_POST['danios']        ?? '');
    $descripcion      = trim($_POST['descripcion']   ?? '');
    $tipo_incidente   = trim($_POST['tipo_incidente']?? '');
    $prioridad        = trim($_POST['prioridad']     ?? '');
    $condiciones      = trim($_POST['condiciones']   ?? '');
    $turno_numero     = (int)($_POST['turno_numero'] ?? 1);
    $calle            = trim($_POST['calle']         ?? '');
    $cruce            = trim($_POST['cruce']         ?? '');
    $colonia          = trim($_POST['colonia']       ?? '');
    $municipio        = trim($_POST['municipio']     ?? '');
    $despachador      = trim($_POST['despachador']   ?? '');
    $responsable_rep  = trim($_POST['responsable_reporte'] ?? '');
    $observaciones    = trim($_POST['observaciones'] ?? '');
    $tiempo_motobomba = (int)($_POST['tiempo_motobomba'] ?? 0);
    $litros_agua      = (int)($_POST['litros_agua']      ?? 0);
    $kilometraje      = (int)($_POST['kilometraje']      ?? 0);
    $nivel_combustible= trim($_POST['nivel_combustible'] ?? '');
    $acciones         = trim($_POST['acciones']          ?? '');

    $stmt = $con->prepare("UPDATE servicios SET
        fecha_servicio=?, hora_reporte=?, hora_salida=?, hora_llegada=?, hora_regreso=?,
        nombre_reporta=?, telefono_reporta=?,
        unidad_id=?, mando=?, bomberos=?,
        lesionados=?, fallecidos=?, danios=?,
        descripcion=?, tipo_incidente=?, prioridad=?, condiciones=?,
        turno_numero=?, calle=?, cruce=?, colonia=?, municipio=?,
        despachador=?, responsable_reporte=?, observaciones=?,
        tiempo_motobomba=?, litros_agua=?, kilometraje=?, nivel_combustible=?, acciones=?
        WHERE id=?");

    $stmt->bind_param("sssssssisiiisssssisssssssiiiissi",
        $fecha_servicio, $hora_reporte, $hora_salida, $hora_llegada, $hora_regreso,
        $nombre_reporta, $telefono_reporta,
        $unidad_id, $mando, $bomberos,
        $lesionados, $fallecidos, $danios,
        $descripcion, $tipo_incidente, $prioridad, $condiciones,
        $turno_numero, $calle, $cruce, $colonia, $municipio,
        $despachador, $responsable_rep, $observaciones,
        $tiempo_motobomba, $litros_agua, $kilometraje, $nivel_combustible, $acciones,
        $id
    );
    $stmt->execute();
    $stmt->close();

    set_flash('success', 'Servicio actualizado correctamente.');
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

