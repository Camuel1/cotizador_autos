<!DOCTYPE html>
<html>
<head>
    <title>Registro de Usuario</title>
</head>
<body>
    <h2>Registro</h2>
    <form method="POST" action="registro.php">
        <input type="text" name="nombre" placeholder="Nombre" required><br><br>
        <input type="email" name="email" placeholder="Correo electrónico" required><br><br>
        <input type="password" name="contraseña" placeholder="Contraseña" required><br><br>
        <input type="submit" value="Registrarse">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        include("db.php");

        $nombre = $_POST["nombre"];
        $email = $_POST["email"];
        $contraseña = password_hash($_POST["contraseña"], PASSWORD_DEFAULT); // Encriptar

        $sql = "INSERT INTO usuarios (nombre, email, contraseña) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nombre, $email, $contraseña);

        if ($stmt->execute()) {
            echo "<p>Registro exitoso ✅</p>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
        $conn->close();
    }
    ?>
</body>
</html>
