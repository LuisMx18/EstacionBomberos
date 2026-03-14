<?php
require_once "../config/conexion.php";
require_once "../includes/auth.php";
if (!puede_acceder('reportes')) { http_response_code(403); die("Acceso denegado."); }

require_once __DIR__ . "/../vendor/dompdf/autoload.inc.php";

use Dompdf\Dompdf;
use Dompdf\Options;

date_default_timezone_set('America/Monterrey');

$inicio    = $_GET['inicio'] ?? date('Y-m-01');
$fin       = $_GET['fin']    ?? date('Y-m-d');
$unidad_id = !empty($_GET['unidad_id']) ? (int) $_GET['unidad_id'] : null;

$where = "s.fecha_servicio BETWEEN '$inicio' AND '$fin'";
if ($unidad_id) {
    $where .= " AND s.unidad_id = $unidad_id";
}

$sql = "
    SELECT s.*, u.nombre AS unidad
    FROM servicios s
    INNER JOIN unidades u ON u.id = s.unidad_id
    WHERE $where
    ORDER BY s.fecha_servicio DESC, s.hora_reporte DESC
";
$res = mysqli_query($con, $sql);

$periodo_texto = "Del " . date('d/m/Y', strtotime($inicio)) .
                 " al " . date('d/m/Y', strtotime($fin));

if ($unidad_id) {
    $u = mysqli_fetch_assoc(mysqli_query($con, "SELECT nombre FROM unidades WHERE id = $unidad_id"));
    $titulo_unidad = "Unidad: " . $u['nombre'];
} else {
    $titulo_unidad = "Todas las unidades";
}

$html = '
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; }
        h2, h3 { text-align: center; margin: 0; }
        .sub { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #444; padding: 3px; }
        th { background-color: #eee; }
        .center { text-align: center; }
        .small { font-size: 8px; }
    </style>
</head>
<body>
    <h2>Estación de Bomberos</h2>
    <h3>Reporte operativo de servicios</h3>
    <div class="sub">' . $periodo_texto . '<br>' . $titulo_unidad . '</div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Unidad</th>
                <th>Tipo incidente</th>
                <th>Prioridad</th>
                <th>Mando</th>
                <th>Bomberos</th>
                <th>Les.</th>
                <th>Fall.</th>
                <th>Daños / Observaciones</th>
            </tr>
        </thead>
        <tbody>';

if (mysqli_num_rows($res) == 0) {
    $html .= '
        <tr><td colspan="10" class="center">Sin servicios en el periodo.</td></tr>';
} else {
    while ($s = mysqli_fetch_assoc($res)) {
        $fecha = date('d/m/Y', strtotime($s['fecha_servicio']));
        $hora  = substr($s['hora_reporte'], 0, 5);

        $tipo  = $s['tipo_incidente'] ?: '-';
        $prio  = $s['prioridad'] ?: 'Normal';
        $les   = isset($s['lesionados']) ? (int)$s['lesionados'] : 0;
        $fall  = isset($s['fallecidos']) ? (int)$s['fallecidos'] : 0;

        $danios = trim(($s['danios'] ?? '') . ' ' . ($s['observaciones'] ?? ''));
        if ($danios === '') {
            $danios = $s['descripcion'] ?: '-';
        }
        // Recortar texto muy largo
        if (mb_strlen($danios) > 120) {
            $danios = mb_substr($danios, 0, 117) . "...";
        }

        $html .= '
            <tr>
                <td class="center">'. $fecha .'</td>
                <td class="center">'. $hora .'</td>
                <td>'. htmlspecialchars($s['unidad']) .'</td>
                <td>'. htmlspecialchars($tipo) .'</td>
                <td class="center">'. htmlspecialchars($prio) .'</td>
                <td>'. htmlspecialchars($s['mando']) .'</td>
                <td class="center">'. (int)$s['bomberos'] .'</td>
                <td class="center">'. $les .'</td>
                <td class="center">'. $fall .'</td>
                <td class="small">'. htmlspecialchars($danios) .'</td>
            </tr>';
    }
}

$html .= '
        </tbody>
    </table>

    <p style="margin-top:20px; font-size:9px;">
        Generado el '. date('d/m/Y H:i') .'.
    </p>
</body>
</html>';

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$filename = "reporte_servicios_" . date('Ymd_His') . ".pdf";
$dompdf->stream($filename, ['Attachment' => true]);
exit;
