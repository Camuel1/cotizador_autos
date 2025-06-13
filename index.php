<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Menú Principal - Cotizador de Autos</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="container">

        <!-- Imagen banner con logos de las marcas -->
        <img src="img/logos/marcas_unidas.png" alt="Logos de Marcas" class="banner-logos">

        <h1>Bienvenido al Cotizador de Autos</h1>

        <?php if (!isset($_SESSION['usuario_id'])): ?>
            <p>Por favor, <a href="login.php">inicia sesión</a> o <a href="registro.php">regístrate</a> para continuar.</p>
            <button onclick="location.href='login.php'">Iniciar Sesión</button>
            <button onclick="location.href='registro.php'">Registrarse</button>
        <?php else: ?>
            <p>Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
            <button onclick="location.href='recomendar.php'">Recomendar</button>
            <button onclick="location.href='autos.php'">Autos</button>
            <button onclick="location.href='logout.php'">Cerrar sesión</button>
        <?php endif; ?>

    </div>
</body>
</html>
