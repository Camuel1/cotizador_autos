<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

include("db.php");

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
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Autos Disponibles</title>
    <link rel="stylesheet" href="autos.css" />
</head>
<body>
    <div class="container">
        <h1>Autos Disponibles</h1>
        <a href="index.php" class="back-link">← Volver al menú</a>

        <?php if (count($autos) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Año</th>
                        <th>Precio</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
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
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay autos disponibles en este momento.</p>
        <?php endif; ?>
    </div>
</body>
</html>
