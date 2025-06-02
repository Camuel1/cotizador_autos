<?php
session_start();  // Inicia la sesión

include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $contraseña = $_POST["contraseña"];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($contraseña, $user['contraseña'])) {
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
<html>
<head>
    <title>Inicio de Sesión</title>
</head>
<body>
    <h2>Login</h2>

    <?php
    if (!empty($error)) {
        echo "<p style='color:red;'>$error</p>";
    }
    ?>

    <form method="POST" action="">
        <input type="email" name="email" placeholder="Correo electrónico" required><br><br>
        <input type="password" name="contraseña" placeholder="Contraseña" required><br><br>
        <input type="submit" value="Iniciar Sesión">
    </form>
</body>
</html>
