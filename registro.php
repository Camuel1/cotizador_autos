<?php
// Conexión a la base de datos RDS
$host = 'db-cotizador-autos1.cfzqjnadmfh0.us-east-1.rds.amazonaws.com';
$db   = 'cotizador_db';
$user = 'admin';
$pass = '31354134162'; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    // Validación básica
    if (!$nombre || !$email || !$password) {
        echo "<p style='color:red;'>Todos los campos son obligatorios.</p>";
        exit;
    }

    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color:red;'>Email no válido.</p>";
        exit;
    }

    // Encriptar la contraseña
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar en base de datos
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$nombre, $email, $hash]);
        echo "<p style='color:green;'>Usuario registrado con éxito.</p>";
    } catch (PDOException $e) {
        // Detectar si email ya existe
        if ($e->getCode() == 23000) {
            echo "<p style='color:red;'>El correo ya está registrado.</p>";
        } else {
            echo "<p style='color:red;'>Error al registrar: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!-- Formulario HTML -->
<form method="POST" action="">
    <input type="text" name="nombre" placeholder="Nombre completo" required><br>
    <input type="email" name="email" placeholder="Correo electrónico" required><br>
    <input type="password" name="password" placeholder="Contraseña" required><br>
    <button type="submit">Registrarse</button>
</form>
