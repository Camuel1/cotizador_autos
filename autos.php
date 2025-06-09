<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

include("db.php");  // Incluye la conexión mysqli

$sql = "SELECT * FROM autos";
$result = $conn->query($sql);

$autos = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $autos[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Autos Disponibles</title>
</head>
<body>
    <h1>Autos Disponibles</h1>
    <p><a href="index.php">← Volver al menú</a></p>

    <?php if (count($autos) > 0): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Precio</th>
                <th>Acción</th>
            </tr>
            <?php foreach ($autos as $auto): ?>
                <tr>
                    <td><?php echo htmlspecialchars($auto['marca']); ?></td>
                    <td><?php echo htmlspecialchars($auto['modelo']); ?></td>
                    <td><?php echo htmlspecialchars($auto['anio']); ?></td>
                    <td><?php echo number_format($auto['precio'], 0, ',', '.'); ?> CLP</td>
                    <td>
                        <form action="cotizar.php" method="get">
                            <input type="hidden" name="id" value="<?php echo $auto['id']; ?>">
                            <button type="submit">Cotizar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No hay autos disponibles en este momento.</p>
    <?php endif; ?>
</body>
</html>
