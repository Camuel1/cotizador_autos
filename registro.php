<?php
session_start();

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
$mensaje = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    if (!$nombre || !$email || !$password) {
        $mensaje = "<p class='error'>Todos los campos son obligatorios.</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "<p class='error'>Email no válido.</p>";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");

        try {
            $stmt->execute([$nombre, $email, $hash]);
            $mensaje = "<p class='exito'>Usuario registrado con éxito.</p>";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $mensaje = "<p class='error'>El correo ya está registrado.</p>";
            } else {
                $mensaje = "<p class='error'>Error al registrar: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="registro.css">
</head>
<body>
    <div class="container">
        <h1>Registro de Usuario</h1>

        <?php if (!empty($mensaje)) echo $mensaje; ?>

        <form method="POST" action="">
            <input type="text" name="nombre" placeholder="Nombre completo" required><br>
            <input type="email" name="email" placeholder="Correo electrónico" required><br>
            <input type="password" name="password" placeholder="Contraseña" required><br>
            <button type="submit">Registrarse</button>
        </form>

        <p><a href="login.php">¿Ya tienes cuenta? Inicia sesión</a></p>
    </div>
</body>
</html>
