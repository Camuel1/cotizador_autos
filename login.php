<?php
session_start();  // Inicia la sesión

include("db.php"); // Asegúrate que db.php usa mysqli y $conn

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $contraseña = $_POST["contraseña"];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Cambia 'password' si tu campo en BD tiene otro nombre
        if (password_verify($contraseña, $user['password'])) {
            // Guardamos datos del usuario en la sesión
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nombre'] = $user['nombre'];
            // Redirigimos a la página de recomendaciones
            header("Location: recomendar.php");
            exit();
        } else {
            $error = "Contraseña incorrecta ❌";
        }
    } else {
        $error = "Usuario no encontrado ❌";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Inicio de Sesión</title>
</head>
<body>
    <h2>Login</h2>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="email" name="email" placeholder="Correo electrónico" required><br><br>
        <input type="password" name="contraseña" placeholder="Contraseña" required><br><br>
        <input type="submit" value="Iniciar Sesión">
    </form>
</body>
</html>
