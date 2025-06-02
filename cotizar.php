<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    // No está logueado, lo enviamos al login
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Página de Cotización</title>
</head>
<body>
    <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h2>
    <p>Aquí podrás cotizar autos según tus gustos.</p>

    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
