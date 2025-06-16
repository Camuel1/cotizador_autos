<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require 'vendor/autoload.php'; // Dompdf
use Dompdf\Dompdf;
use Dompdf\Options;
include("db.php");

$error = "";
$mensaje = "";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception("ID de auto inválido.");
    }

    $auto_id = intval($_GET['id']);
    $sql = "SELECT * FROM autos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $auto_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        throw new Exception("Auto no encontrado.");
    }

    $auto = $result->fetch_assoc();
    $cuotas = 0;
    $medio_pago = "";
    $pie_pago = 0;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $cuotas = intval($_POST['cuotas'] ?? 0);
        $medio_pago = $_POST['medio_pago'] ?? '';
        $pie_pago = floatval($_POST['pie_pago'] ?? 0);

        $cuotas_validas = [12, 24, 36, 48];
        if (!in_array($cuotas, $cuotas_validas)) throw new Exception("Número de cuotas inválido.");

        $medios_validos = ['compra_inteligente', 'credito_banco', 'efectivo'];
        if (!in_array($medio_pago, $medios_validos)) throw new Exception("Medio de pago inválido.");

        if ($pie_pago < 0) throw new Exception("El monto del anticipo no puede ser negativo.");

        $precio_base = $auto['precio'];
        $tasas = [
            'compra_inteligente' => [12 => 0.07, 24 => 0.09, 36 => 0.11, 48 => 0.13],
            'credito_banco' => [12 => 0.11, 24 => 0.13, 36 => 0.15, 48 => 0.17],
            'efectivo' => [12 => 0.00, 24 => 0.00, 36 => 0.00, 48 => 0.00]
        ];

        $interes = $tasas[$medio_pago][$cuotas] ?? 0;
        $saldo_financiar = $precio_base - $pie_pago;
        $total_con_interes = $saldo_financiar * (1 + $interes);
        $total_a_pagar = $total_con_interes;
        $valor_cuota = $total_a_pagar / $cuotas;
        $interes_porcentaje = $interes * 100;

        $sql_insert = "INSERT INTO cotizaciones (usuario_id, auto_id, precio, cuotas, medio_pago, interes_total, total_a_pagar, pie_pago, fecha_cotizacion)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iiidssdd", $_SESSION['usuario_id'], $auto_id, $precio_base, $cuotas, $medio_pago, $interes_porcentaje, $total_a_pagar, $pie_pago);

        if ($stmt_insert->execute()) {
            $html = "
                <h2>Cotización de Auto</h2>
                <p><strong>Auto:</strong> {$auto['marca']} {$auto['modelo']} ({$auto['anio']})</p>
                <p><strong>Precio base:</strong> $" . number_format($precio_base, 0, ',', '.') . "</p>
                <p><strong>Medio de pago:</strong> {$medio_pago}</p>
                <p><strong>Cuotas:</strong> {$cuotas}</p>
                <p><strong>Interés:</strong> {$interes_porcentaje}%</p>
                <p><strong>Total a pagar:</strong> $" . number_format($total_a_pagar, 0, ',', '.') . "</p>
                <p><strong>Valor de cada cuota:</strong> $" . number_format($valor_cuota, 0, ',', '.') . "</p>
                <p><strong>Pie pago:</strong> $" . number_format($pie_pago, 0, ',', '.') . "</p>
            ";

            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $pdf_filename = 'cotizacion_' . time() . '.pdf';
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $pdf_filename . '"');
            echo $dompdf->output();
            exit;
        } else {
            throw new Exception("Error al guardar la cotización.");
        }

        $stmt_insert->close();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>