<?php
require_once "../config/conexion.php";
require_once "../includes/auth.php";

require_once __DIR__ . "/../vendor/dompdf/autoload.inc.php";

use Dompdf\Dompdf;
use Dompdf\Options;

date_default_timezone_set('America/Monterrey');

// Parámetros recibidos
$tipo       = $_GET['tipo']       ?? 'dia';      // dia | semana | mes | personalizado
$fecha_base = $_GET['fecha']      ?? date('Y-m-d');
$bombero_id = !empty($_GET['bombero_id']) ? (int) $_GET['bombero_id'] : null;

// Cálculo de rango de fechas
if ($tipo === 'personalizado') {
    // Desde el panel de reportes
    $inicio = $_GET['inicio'] ?? $fecha_base;
    $fin    = $_GET['fin']    ?? $fecha_base;
} else {
    // Modos día / semana / mes
    $inicio = $fin = $fecha_base;

    if ($tipo === 'semana') {
        $ts = strtotime($fecha_base);
        $inicio = date('Y-m-d', strtotime('monday this week', $ts));
        $fin    = date('Y-m-d', strtotime('sunday this week', $ts));
    } elseif ($tipo === 'mes') {
        $inicio = date('Y-m-01', strtotime($fecha_base));
        $fin    = date('Y-m-t', strtotime($fecha_base));
    }
}

// WHERE base
$where = "a.fecha BETWEEN '$inicio' AND '$fin'";
if ($bombero_id) {
    $where .= " AND a.bombero_id = $bombero_id";
}

// Consulta de asistencias
$sql = "
    SELECT a.*, b.nombre, b.puesto
    FROM asistencias a
    INNER JOIN bomberos b ON b.id = a.bombero_id
    WHERE $where
    ORDER BY a.fecha ASC, a.hora_entrada ASC
";
$res = mysqli_query($con, $sql);

// Textos del encabezado
if ($tipo === 'dia') {
    $periodo_texto = "Día: " . date('d/m/Y', strtotime($fecha_base));
} elseif ($tipo === 'semana') {
    $periodo_texto = "Semana del " . date('d/m/Y', strtotime($inicio)) .
                     " al " . date('d/m/Y', strtotime($fin));
} elseif ($tipo === 'mes') {
    $periodo_texto = "Mes: " . date('m/Y', strtotime($fecha_base));
} else { // personalizado
    $periodo_texto = "Del " . date('d/m/Y', strtotime($inicio)) .
                     " al " . date('d/m/Y', strtotime($fin));
}

if ($bombero_id) {
    $bom = mysqli_fetch_assoc(
        mysqli_query($con, "SELECT nombre FROM bomberos WHERE id = $bombero_id")
    );
    $titulo_bombero = "Bombero: " . $bom['nombre'];
} else {
    $titulo_bombero = "Todos los bomberos";
}

// Construir HTML
$html = '
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h1, h2, h3 { text-align: center; margin: 0; }
        .sub { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #444; padding: 4px; }
        th { background-color: #eee; }
        .center { text-align: center; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h2>Estación de Bomberos</h2>
    <h3>Reporte de asistencia</h3>
    <div class="sub">' . $periodo_texto . '<br>' . $titulo_bombero . '</div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Bombero</th>
                <th>Puesto</th>
                <th>Entrada</th>
                <th>Salida</th>
                <th>Horas</th>
                <th>Tarde</th>
            </tr>
        </thead>
        <tbody>';

if (mysqli_num_rows($res) == 0) {
    $html .= '
        <tr>
            <td colspan="7" class="center">Sin registros en el periodo.</td>
        </tr>';
} else {
    while ($a = mysqli_fetch_assoc($res)) {
        $fecha  = date('d/m/Y', strtotime($a['fecha']));
        $entr   = $a['hora_entrada'] ? substr($a['hora_entrada'], 0, 5) : '-';
        $sal    = $a['hora_salida']  ? substr($a['hora_salida'], 0, 5)  : '-';
        $horas  = !is_null($a['horas_turno']) ? $a['horas_turno'] . ' h' : '-';
        $tarde  = $a['llego_tarde'] ? 'Sí' : 'No';

        if (!is_null($a['horas_turno']) && $a['horas_turno'] < 8) {
            $horas .= ' (inc.)';
        }

        $html .= '
            <tr>
                <td class="center">'. $fecha .'</td>
                <td>'. htmlspecialchars($a['nombre']) .'</td>
                <td>'. htmlspecialchars($a['puesto']) .'</td>
                <td class="center">'. $entr .'</td>
                <td class="center">'. $sal .'</td>
                <td class="center">'. $horas .'</td>
                <td class="center">'. $tarde .'</td>
            </tr>';
    }
}

$html .= '
        </tbody>
    </table>

    <p style="margin-top:20px; font-size:10px;">
        Generado el '. date('d/m/Y H:i') .'.
    </p>
</body>
</html>';

// Configurar y generar PDF con Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$filename = "reporte_asistencia_" . $tipo . "_" . date('Ymd_His') . ".pdf";
$dompdf->stream($filename, ['Attachment' => true]);
exit;
