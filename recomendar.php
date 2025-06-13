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
<link rel="stylesheet" href="recomendar.css">
    <title>Recomendar Auto</title>
    <script>
    function formatPrice(input) {
        let value = input.value.replace(/\./g, '');
        if (!isNaN(value) && value !== '') {
            input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        } else {
            input.value = '';
        }
    }
    function cleanPrice() {
        let priceInput = document.getElementById('precio_max');
        priceInput.value = priceInput.value.replace(/\./g, '');
    }
    </script>
</head>
<body>
<div class="container">
    <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?> | <a href="logout.php">Cerrar sesión</a></p>

    <h2>Recomendador de Autos</h2>
    <form method="POST" action="recomendar.php" onsubmit="cleanPrice()">
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

        <label>Precio máximo (CLP) (opcional):</label><br>
        <input type="text" id="precio_max" name="precio_max" onkeyup="formatPrice(this)" placeholder="10.000.000"><br><br>

        <input type="submit" value="Ver Recomendaciones">
         <button type="button" onclick="window.location='recomendar.php';" style="margin-left: 10px; background-color: #555; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer;">Limpiar filtros</button> 
    </form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("db.php");

    $estilo = $_POST["estilo"];
    $caja = $_POST["caja"];
    $precio_max = trim($_POST["precio_max"]);

    $precio_max = str_replace('.', '', $precio_max);

    if ($precio_max !== '' && !is_numeric($precio_max)) {
        echo "<p style='color: #ff6600;'>Precio inválido.</p>";
        exit();
    }

    if ($precio_max === '') {
        $sql = "SELECT * FROM autos WHERE estilo = ? AND caja = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $estilo, $caja);
    } else {
        $sql = "SELECT * FROM autos WHERE estilo = ? AND caja = ? AND precio <= ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssd", $estilo, $caja, $precio_max);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h3>Resultados Recomendados:</h3>";

    if ($result->num_rows > 0) {
        while ($auto = $result->fetch_assoc()) {
            echo "<div class='auto-item'>";
            echo "<h4>" . htmlspecialchars($auto['marca']) . " " . htmlspecialchars($auto['modelo']) . "</h4>";
            echo "<p><strong>Precio:</strong> $" . number_format($auto['precio'], 0, ',', '.') . "</p>";
            echo "<p><strong>Caja:</strong> " . htmlspecialchars($auto['caja']) . " | <strong>Estilo:</strong> " . htmlspecialchars($auto['estilo']) . "</p>";
            echo '<a href="cotizar.php?id=' . $auto['id'] . '" class="btn-cotizar">Cotizar</a>';
            echo "</div><hr>";
            
        }
    } else {
        echo "<p>No se encontraron autos que coincidan con tus preferencias.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
</div>
</body>
</html>
