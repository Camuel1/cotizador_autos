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

        $cuotas_validas = [3, 6, 12, 24];
        if (!in_array($cuotas, $cuotas_validas)) {
            throw new Exception("Número de cuotas inválido.");
        }

        $medios_validos = ['tarjeta', 'transferencia', 'efectivo'];
        if (!in_array($medio_pago, $medios_validos)) {
            throw new Exception("Medio de pago inválido.");
        }

        if ($pie_pago < 0) {
            throw new Exception("El monto de anticipo no puede ser negativo.");
        }

        $precio_base = $auto['precio'];

        $interes = 0;
        if ($medio_pago === 'tarjeta') {
            $interes = 0.05;
        } elseif ($medio_pago === 'transferencia') {
            $interes = 0.02;
        }

        $total_con_interes = $precio_base * (1 + $interes);

        if ($medio_pago === 'efectivo') {
            if ($pie_pago > $total_con_interes) {
                throw new Exception("El monto del anticipo no puede ser mayor al total a pagar.");
            }
            $total_a_pagar = $total_con_interes - $pie_pago;
        } else {
            $total_a_pagar = $total_con_interes;
            $pie_pago = 0;
        }

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

<!DOCTYPE html>
<html>
<head>
    <title>Cotizar Auto</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="cotizar.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1>Cotizar: <?php echo htmlspecialchars($auto['marca'] . ' ' . $auto['modelo'] ?? ''); ?></h1>
    <p>Precio base: $<?php echo number_format($auto['precio'] ?? 0, 0, ',', '.'); ?></p>

    <?php if ($mensaje): ?>
        <p style="color: green;"><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="cuotas">Número de cuotas:</label>
        <select name="cuotas" id="cuotas" required>
            <option value="3" <?php if ($cuotas == 3) echo 'selected'; ?>>3 cuotas</option>
            <option value="6" <?php if ($cuotas == 6) echo 'selected'; ?>>6 cuotas</option>
            <option value="12" <?php if ($cuotas == 12) echo 'selected'; ?>>12 cuotas</option>
            <option value="24" <?php if ($cuotas == 24) echo 'selected'; ?>>24 cuotas</option>
        </select>

        <label for="medio_pago">Medio de pago:</label>
        <select name="medio_pago" id="medio_pago" required onchange="togglePiePago()">
            <option value="tarjeta" <?php if ($medio_pago == 'tarjeta') echo 'selected'; ?>>Tarjeta (+5% interés)</option>
            <option value="transferencia" <?php if ($medio_pago == 'transferencia') echo 'selected'; ?>>Transferencia (+2% interés)</option>
            <option value="efectivo" <?php if ($medio_pago == 'efectivo') echo 'selected'; ?>>Efectivo (sin interés)</option>
        </select>

        <div id="pie_pago_div" style="display: <?php echo ($medio_pago === 'efectivo') ? 'block' : 'none'; ?>;">
            <label for="pie_pago">Monto de anticipo (pie) en efectivo:</label>
            <input type="number" step="0.01" min="0" name="pie_pago" id="pie_pago" value="<?php echo htmlspecialchars($pie_pago); ?>">
        </div>

        <input type="submit" value="Guardar Cotización">
    </form>

    <p><a href="recomendar.php">Volver a recomendaciones</a></p>
    <p><a href="logout.php">Cerrar sesión</a></p>
</div>

<script>
function togglePiePago() {
    const medioPago = document.getElementById('medio_pago').value;
    const pieDiv = document.getElementById('pie_pago_div');
    if (medioPago === 'efectivo') {
        pieDiv.style.display = 'block';
    } else {
        pieDiv.style.display = 'none';
        document.getElementById('pie_pago').value = '';
    }
}
</script>
</body>
</html>
