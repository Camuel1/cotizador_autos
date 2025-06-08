
<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cotizador de Autos</title>
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>!</h1>
    <p>Aquí puedes poner el contenido del cotizador de autos.</p>
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
