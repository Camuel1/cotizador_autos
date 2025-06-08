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
    <script>
    // Función para formatear con puntos cada 3 dígitos al escribir
    function formatPrice(input) {
        let value = input.value.replace(/\./g, ''); // quitar puntos
        if (!isNaN(value) && value !== '') {
            // Añadir puntos
            input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        } else {
            input.value = '';
        }
    }

    // Al enviar, limpia el input para que no tenga puntos
    function cleanPrice() {
        let priceInput = document.getElementById('precio_max');
        priceInput.value = priceInput.value.replace(/\./g, '');
    }
    </script>
</head>
<body>
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
    </form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("db.php");

    $estilo = $_POST["estilo"];
    $caja = $_POST["caja"];
    $precio_max = trim($_POST["precio_max"]);

    // Limpiar puntos si es que llegan (por si JS no funcionó)
    $precio_max = str_replace('.', '', $precio_max);

    // Validar que si se ingresó precio, sea numérico
    if ($precio_max !== '' && !is_numeric($precio_max)) {
        echo "<p>Precio inválido.</p>";
        exit();
    }

    if ($precio_max === '') {
        // Si no hay precio máximo, no se filtra por precio
        $sql = "SELECT * FROM autos WHERE estilo = ? AND caja = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $estilo, $caja);
    } else {
        // Si hay precio máximo, filtrar por él
        $sql = "SELECT * FROM autos WHERE estilo = ? AND caja = ? AND precio <= ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssd", $estilo, $caja, $precio_max);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h3>Resultados Recomendados:</h3>";

    if ($result->num_rows > 0) {
        while ($auto = $result->fetch_assoc()) {
            echo "<p><strong>" . htmlspecialchars($auto['marca']) . " " . htmlspecialchars($auto['modelo']) . "</strong><br>";
            echo "Precio: $" . number_format($auto['precio'], 0, ',', '.') . "<br>";
            echo "Caja: " . htmlspecialchars($auto['caja']) . " - Estilo: " . htmlspecialchars($auto['estilo']) . "</p><hr>";
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
