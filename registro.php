<?php
// Conexión a la base de datos RDS
$host = 'db-cotizador-autos1.cfzqjnadmfh0.us-east-1.rds.amazonaws.com';
$db   = 'cotizador_autos';
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
    $nombre = $_POST["nombre"] ?? '';
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';

    // Validación básica
    if (!$nombre || !$email || !$password) {
        echo "Todos los campos son obligatorios.";
        exit;
    }

    // Encriptar la contraseña
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar en base de datos
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$nombre, $email, $hash]);
        echo "Usuario registrado con éxito.";
    } catch (PDOException $e) {
        echo "Error al registrar: " . $e->getMessage();
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
