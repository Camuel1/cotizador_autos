<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Menú Principal - Cotizador de Autos</title>
</head>
<body>
    <h1>Bienvenido al Cotizador de Autos</h1>

    <?php if (!isset($_SESSION['usuario_id'])): ?>
        <p style="color: red;">Por favor, <a href="login.php">inicia sesión</a> o <a href="registro.php">regístrate</a> para continuar.</p>
        <button onclick="location.href='login.php'">Iniciar Sesión</button>
        <button onclick="location.href='registro.php'">Registrarse</button>
    <?php else: ?>
        <p>Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
        <button onclick="location.href='recomendar.php'">Recomendar</button>
        <button onclick="location.href='autos.php'">Autos</button>
        <button onclick="location.href='logout.php'">Cerrar sesión</button>
    <?php endif; ?>

</body>
</html>
