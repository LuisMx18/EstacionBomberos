<?php
$pageTitle = "Registrar Servicio";
require_once "../config/conexion.php";
require_once "../includes/auth.php";

// Obtener unidades para el select
$unidades = mysqli_query($con, "SELECT id, nombre FROM unidades ORDER BY nombre ASC");

// Procesar envío
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha_servicio   = $_POST['fecha_servicio'];
    $hora_reporte     = $_POST['hora_reporte'] ?: date('H:i:s');
    $hora_salida      = $_POST['hora_salida'] ?: date('H:i:s');
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

    // Inserta en servicios
    $sql = "INSERT INTO servicios (
                fecha_servicio, hora_reporte, hora_salida, hora_llegada, hora_regreso,
                nombre_reporta, telefono_reporta,
                unidad_id, mando, bomberos,
                lesionados, fallecidos, danios,
                descripcion, tipo_incidente, prioridad, condiciones,
                turno_numero,
                calle, cruce, colonia, municipio,
                despachador, responsable_reporte, observaciones,
                tiempo_motobomba, litros_agua,
                kilometraje, nivel_combustible,
                acciones
            ) VALUES (
                '$fecha_servicio',
                '$hora_reporte',
                '$hora_salida',
                " . ($hora_llegada ? "'$hora_llegada'" : "NULL") . ",
                " . ($hora_regreso ? "'$hora_regreso'" : "NULL") . ",
                '$nombre_reporta',
                '$telefono_reporta',
                $unidad_id,
                '$mando',
                $bomberos,
                $lesionados,
                $fallecidos,
                '$danios',
                '$descripcion',
                '$tipo_incidente',
                '$prioridad',
                '$condiciones',
                $turno_numero,
                '$calle',
                '$cruce',
                '$colonia',
                '$municipio',
                '$despachador',
                '$responsable_rep',
                '$observaciones',
                $tiempo_motobomba,
                $litros_agua,
                $kilometraje,
                '$nivel_combustible',
                '$acciones'
            )";

    mysqli_query($con, $sql);
    $servicio_id = mysqli_insert_id($con);

    // Opcional: registro inicial de combustible ligado al servicio
    $hora_salida_comb = $hora_salida;
    $sqlComb = "INSERT INTO combustible (
                    servicio_id, hora_salida, hora_regreso,
                    descripcion, kilometraje, nivel_combustible
                ) VALUES (
                    $servicio_id,
                    '$hora_salida_comb',
                    " . ($hora_regreso ? "'$hora_regreso'" : "NULL") . ",
                    'Registro inicial desde servicio',
                    $kilometraje,
                    '$nivel_combustible'
                )";
    mysqli_query($con, $sqlComb);

    header("Location: servicios.php");
    exit;
}

require_once "../includes/header.php";
?>

<div class="card card-soft mb-4 border-0 overflow-hidden animate__animated animate__fadeIn">
    <!-- Encabezado estilo reporte -->
    <div class="p-3" style="background: linear-gradient(90deg, #b91c1c, #ef4444); color:#fff;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-clipboard-pulse me-2"></i>
                    REPORTE OPERATIVO
                </h5>
                <small class="text-uppercase" style="letter-spacing: .1em;">
                    Cuerpo de Bomberos
                </small>
            </div>
            <div class="text-end">
                <div class="small text-uppercase">Fecha</div>
                <div class="fw-semibold">
                    <?php echo date('d/m/Y'); ?>
                </div>
                <div class="small text-uppercase mt-1">Folio de servicio</div>
                <div class="fw-bold bg-light text-danger px-2 py-1 rounded-3 d-inline-block">
                    FRAP-<?php echo date('Y'); ?>-XXXX
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de etapas -->
    <div class="bg-light px-3 py-2 border-bottom">
        <div class="row g-2 text-center">
            <div class="col-6 col-md">
                <div class="p-2 bg-white rounded-4 shadow-sm">
                    <div class="small text-muted text-uppercase">Despacho</div>
                    <div class="fw-bold fs-5">
                        <i class="bi bi-telephone-inbound me-1 text-danger"></i> --:--
                    </div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="p-2 bg-white rounded-4 shadow-sm">
                    <div class="small text-muted text-uppercase">Escena</div>
                    <div class="fw-bold fs-6">--:--</div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="p-2 bg-white rounded-4 shadow-sm">
                    <div class="small text-muted text-uppercase">Control</div>
                    <div class="fw-bold fs-6">--:--</div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="p-2 bg-white rounded-4 shadow-sm">
                    <div class="small text-muted text-uppercase">Traslado</div>
                    <div class="fw-bold fs-6">--:--</div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="p-2 bg-white rounded-4 shadow-sm">
                    <div class="small text-muted text-uppercase">Hospital</div>
                    <div class="fw-bold fs-6">--:--</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3 page-title">
            <h2 class="mb-0">
                <i class="bi bi-file-plus text-success me-2"></i>
                Registrar Servicio
            </h2>
            <a href="servicios.php" class="btn btn-outline-secondary btn-soft">
                <i class="bi bi-arrow-left-short me-1"></i>
                Volver a lista
            </a>
        </div>

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
                        <option value="<?php echo $u['id']; ?>">
                            <?php echo htmlspecialchars($u['nombre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted text-uppercase">Mando</label>
                <input type="text" name="mando" class="form-control" required>
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
                       value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted text-uppercase">Hora reporte</label>
                <input type="time" name="hora_reporte" class="form-control"
                       value="<?php echo date('H:i'); ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted text-uppercase">Hora salida</label>
                <input type="time" name="hora_salida" class="form-control"
                       value="<?php echo date('H:i'); ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted text-uppercase">Hora llegada</label>
                <input type="time" name="hora_llegada" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted text-uppercase">Hora regreso</label>
                <input type="time" name="hora_regreso" class="form-control">
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
                    <option value="">Selecciona...</option>
                    <option value="incendio">Incendio</option>
                    <option value="rescate">Rescate</option>
                    <option value="fuga_gas">Fuga de gas</option>
                    <option value="accidente_vial">Accidente vial</option>
                    <option value="apoyo_medico">Apoyo médico</option>
                    <option value="falsa_alarma">Falsa alarma</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted text-uppercase">Prioridad</label>
                <select name="prioridad" class="form-select">
                    <option value="">Normal</option>
                    <option value="alta">Alta</option>
                    <option value="critica">Crítica</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label small text-muted text-uppercase">Condiciones especiales</label>
                <input type="text" name="condiciones" class="form-control"
                       placeholder="Clima, riesgo eléctrico, materiales peligrosos, etc.">
            </div>

            <!-- Datos de quien reporta y personal -->
            <div class="col-12 mt-3">
                <h6 class="text-uppercase small text-danger fw-bold mb-2">
                    <i class="bi bi-person-lines-fill me-1"></i> Datos de quien reporta y personal
                </h6>
            </div>
            <div class="col-md-5">
                <label class="form-label small text-muted text-uppercase">Nombre de quien reporta</label>
                <input type="text" name="nombre_reporta" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted text-uppercase">Teléfono</label>
                <input type="text" name="telefono_reporta" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted text-uppercase">Bomberos</label>
                <input type="number" name="bomberos" class="form-control" min="0" value="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted text-uppercase">Turno</label>
                <select name="turno_numero" class="form-select" required>
                    <option value="1">Turno 1</option>
                    <option value="2">Turno 2</option>
                    <option value="3">Turno 3</option>
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
                <input type="number" name="lesionados" class="form-control" min="0" value="0">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted text-uppercase">Fallecidos</label>
                <input type="number" name="fallecidos" class="form-control" min="0" value="0">
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted text-uppercase">Daños materiales</label>
                <input type="text" name="danios" class="form-control"
                       placeholder="Pérdida total/parcial, áreas afectadas, etc.">
            </div>

            <!-- Ubicación -->
            <div class="col-12 mt-3">
                <h6 class="text-uppercase small text-danger fw-bold mb-2">
                    <i class="bi bi-geo-alt me-1"></i> Ubicación del incidente
                </h6>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted text-uppercase">Calle</label>
                <input type="text" name="calle" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted text-uppercase">Cruce con</label>
                <input type="text" name="cruce" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted text-uppercase">Colonia</label>
                <input type="text" name="colonia" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted text-uppercase">Municipio</label>
                <input type="text" name="municipio" class="form-control" value="Linares, N.L.">
            </div>

            <!-- Recursos y combustible -->
            <div class="col-12 mt-3">
                <h6 class="text-uppercase small text-danger fw-bold mb-2">
                    <i class="bi bi-fuel-pump me-1"></i> Recursos y combustible
                </h6>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted text-uppercase">Despachador</label>
                <input type="text" name="despachador" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted text-uppercase">Tiempo motobomba (min)</label>
                <input type="number" name="tiempo_motobomba" class="form-control" min="0" value="0">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted text-uppercase">Litros de agua</label>
                <input type="number" name="litros_agua" class="form-control" min="0" value="0">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted text-uppercase">Kilometraje</label>
                <input type="number" name="kilometraje" class="form-control" min="0" value="0">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted text-uppercase">Nivel combustible</label>
                <input type="text" name="nivel_combustible" class="form-control"
                       placeholder="Lleno, 3/4, 1/2, etc.">
            </div>

            <!-- Descripción y acciones -->
            <div class="col-12 mt-3">
                <h6 class="text-uppercase small text-danger fw-bold mb-2">
                    <i class="bi bi-journal-text me-1"></i> Descripción y acciones
                </h6>
            </div>
            <div class="col-12">
                <label class="form-label small text-muted text-uppercase">Descripción general</label>
                <textarea name="descripcion" class="form-control" rows="3"
                          placeholder="Describe brevemente el incidente..."></textarea>
            </div>
            <div class="col-12">
                <label class="form-label small text-muted text-uppercase">Acciones realizadas</label>
                <textarea name="acciones" class="form-control" rows="3"
                          placeholder="Ataque, rescate, ventilación, aseguramiento de área, etc."></textarea>
            </div>

            <!-- Cierre del reporte -->
            <div class="col-12 mt-3">
                <h6 class="text-uppercase small text-danger fw-bold mb-2">
                    <i class="bi bi-pen me-1"></i> Cierre del reporte
                </h6>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted text-uppercase">Responsable del reporte</label>
                <input type="text" name="responsable_reporte" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted text-uppercase">Observaciones / seguimiento</label>
                <input type="text" name="observaciones" class="form-control">
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                <button type="submit" class="btn btn-success btn-soft">
                    <i class="bi bi-check-circle me-1"></i>
                    Guardar servicio
                </button>
                <a href="servicios.php" class="btn btn-outline-secondary btn-soft">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
