<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Recomendar Auto</title>
</head>
<body>
    <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?> | <a href="logout.php">Cerrar sesión</a></p>

    <h2>Recomendador de Autos</h2>
    <form method="POST" action="recomendar.php">
        <label>¿Qué tipo de auto prefieres?</label><br>
        <select name="estilo" required>
            <option value="velocidad">Velocidad</option>
            <option value="comodidad">Comodidad</option>
            <option value="economía">Economía</option>
        </select><br><br>

        <label>¿Qué tipo de transmisión prefieres?</label><br>
        <select name="caja" required>
            <option value="manual">Manual</option>
            <option value="automática">Automática</option>
        </select><br><br>

        <label>Tamaño del motor mínimo (ej: 1.0):</label><br>
        <input type="number" step="0.1" name="motor_min" required><br><br>

        <input type="submit" value="Ver Recomendaciones">
    </form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("db.php");

    $estilo = $_POST["estilo"];
    $caja = $_POST["caja"];
    $motor_min = $_POST["motor_min"];

    $sql = "SELECT * FROM autos WHERE estilo = ? AND caja = ? AND motor >= ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssd", $estilo, $caja, $motor_min);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h3>Resultados Recomendados:</h3>";

    if ($result->num_rows > 0) {
        while ($auto = $result->fetch_assoc()) {
            echo "<p><strong>{$auto['marca']} {$auto['modelo']}</strong><br>";
            echo "Precio: $" . number_format($auto['precio'], 0, ',', '.') . "<br>";
            echo "Caja: {$auto['caja']} - Motor: {$auto['motor']} - Puertas: {$auto['puertas']}</p><hr>";
        }
    } else {
        echo "<p>No se encontraron autos que coincidan con tus preferencias.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
</body>
</html>
