<?php
$pageTitle = "Asistencia por RFID";
require_once "../config/conexion.php";
require_once "../includes/auth.php";
require_once "../includes/header.php";

use Dompdf\Dompdf; // ignorar si no usas dompdf aquí

date_default_timezone_set('America/Monterrey');

$mensaje = "";
$tipo_alerta = "info";
$detalle = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rfid'])) {
    $rfid = mysqli_real_escape_string($con, trim($_POST['rfid']));
    $fecha_hoy = date('Y-m-d');
    $hora_now  = date('H:i:s');

    // traer bombero con su mínimo de horas
    $q = mysqli_query(
        $con,
        "SELECT id, nombre, horas_turno_minimo
         FROM bomberos 
         WHERE rfid_codigo = '$rfid'
         LIMIT 1"
    );

    if ($bombero = mysqli_fetch_assoc($q)) {
        $bombero_id = (int) $bombero['id'];
        $min_horas_turno = (int)($bombero['horas_turno_minimo'] ?? 8);

        // buscar registro de asistencia del día
        $res = mysqli_query(
            $con,
            "SELECT * FROM asistencias
             WHERE bombero_id = $bombero_id
               AND fecha = '$fecha_hoy'
             LIMIT 1"
        );

        if (mysqli_num_rows($res) == 0) {
            // PRIMERA PASADA -> ENTRADA
            mysqli_query(
                $con,
                "INSERT INTO asistencias (
                    bombero_id, fecha,
                    hora_entrada, hora_salida,
                    horas_turno, llego_tarde,
                    hora_registro, turno_inicio, turno_fin,
                    horas_turno_minimo
                ) VALUES (
                    $bombero_id,
                    '$fecha_hoy',
                    '$hora_now',
                    NULL,
                    NULL,
                    0,
                    '$hora_now',
                    NULL,
                    NULL,
                    $min_horas_turno
                )"
            );

            $mensaje = "ENTRADA registrada para: " . $bombero['nombre'];
            $tipo_alerta = "success";
            $detalle = "Mínimo: {$min_horas_turno} h | Hora de entrada: " . substr($hora_now, 0, 5);

        } else {
            // YA HAY REGISTRO
            $asis = mysqli_fetch_assoc($res);
            $id_asistencia = (int) $asis['id'];
            $hora_entrada = $asis['hora_entrada'];

            // si en la asistencia no hubiera valor, toma el actual del bombero
            $min_horas_turno = (int)($asis['horas_turno_minimo'] ?? $min_horas_turno);

            if (is_null($asis['hora_salida'])) {
                // SEGUNDA PASADA -> SALIDA
                $seg_entrada = strtotime($hora_entrada);
                $seg_salida  = strtotime($hora_now);

                // Si cruzan medianoche
                if ($seg_salida < $seg_entrada) {
                    $seg_salida += 24 * 3600;
                }

                $horas_diferencia = ($seg_salida - $seg_entrada) / 3600;
                $horas_red = round($horas_diferencia, 2);

                $extras = 0;
                if ($horas_diferencia > $min_horas_turno) {
                    $extras = round($horas_diferencia - $min_horas_turno, 2);
                }

                mysqli_query(
                    $con,
                    "UPDATE asistencias
                     SET hora_salida = '$hora_now',
                         horas_turno = $horas_red,
                         horas_turno_minimo = $min_horas_turno
                     WHERE id = $id_asistencia"
                );

                if ($horas_diferencia >= $min_horas_turno) {
                    if ($extras > 0) {
                        $mensaje = "SALIDA registrada para: " . $bombero['nombre'] . " (turno completo + horas extra)";
                        $tipo_alerta = "success";
                        $detalle =
                            "Mínimo: {$min_horas_turno} h | Trabajadas: {$horas_red} h | Extras: {$extras} h. " .
                            "Entrada: " . substr($hora_entrada, 0, 5) .
                            " | Salida: " . substr($hora_now, 0, 5);
                    } else {
                        $mensaje = "SALIDA registrada para: " . $bombero['nombre'] . " (turno completo)";
                        $tipo_alerta = "success";
                        $detalle =
                            "Mínimo: {$min_horas_turno} h | Trabajadas: {$horas_red} h. " .
                            "Entrada: " . substr($hora_entrada, 0, 5) .
                            " | Salida: " . substr($hora_now, 0, 5);
                    }
                } else {
                    $faltan = round($min_horas_turno - $horas_diferencia, 2);
                    $mensaje = "SALIDA registrada con AVISO para: " . $bombero['nombre'];
                    $tipo_alerta = "warning";
                    $detalle =
                        "Mínimo: {$min_horas_turno} h | Trabajadas: {$horas_red} h (faltaron {$faltan} h). " .
                        "Entrada: " . substr($hora_entrada, 0, 5) .
                        " | Salida: " . substr($hora_now, 0, 5);
                }

            } else {
                // Ya tenía salida
                $mensaje = "Turno ya completado hoy para: " . $bombero['nombre'];
                $tipo_alerta = "secondary";
                $detalle = "Entrada: " . substr($asis['hora_entrada'], 0, 5) .
                           " | Salida: " . substr($asis['hora_salida'], 0, 5) .
                           (!is_null($asis['horas_turno']) ? " | Horas: " . $asis['horas_turno'] . " h" : "") .
                           " | Mínimo: " . ($asis['horas_turno_minimo'] ?? $min_horas_turno) . " h";
            }
        }
    } else {
        $mensaje = "Tarjeta no registrada: " . $rfid;
        $tipo_alerta = "danger";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="d-flex justify-content-center align-items-center mb-3 page-title">
            <h2 class="mb-0">
                <i class="bi bi-wifi text-danger me-2"></i>
                Asistencia por RFID
            </h2>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipo_alerta; ?> text-center animate__animated animate__fadeIn">
                <strong><?php echo htmlspecialchars($mensaje); ?></strong><br>
                <?php if ($detalle): ?>
                    <small><?php echo htmlspecialchars($detalle); ?></small>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="card card-soft animate__animated animate__fadeIn">
            <div class="card-header text-center">
                <i class="bi bi-card-heading me-1"></i>
                Pase la tarjeta por el lector
            </div>
            <div class="card-body">
                <form method="post" autocomplete="off">
                    <input type="text"
                           name="rfid"
                           id="rfid"
                           class="form-control text-center fs-3"
                           placeholder="Pase la tarjeta..."
                           autofocus>
                </form>
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        1ª pasada del día: <strong>ENTRADA</strong> (se toma la hora actual).<br>
                        2ª pasada del día: <strong>SALIDA</strong>. Se calcula si cumplió su turno mínimo
                        (8, 12 o 24 h según su configuración) y si hizo horas extra.<br>
                        Hora local Monterrey: <strong><?php echo date('d/m/Y H:i:s'); ?></strong>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const rfidInput = document.getElementById('rfid');
setInterval(() => {
    if (document.activeElement !== rfidInput) {
        rfidInput.focus();
        rfidInput.select();
    }
}, 500);
</script>

<?php require_once "../includes/footer.php"; ?>
