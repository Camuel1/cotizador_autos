<?php
$host = "db-cotizador-autos1.cfzqjnadmfh0.us-east-1.rds.amazonaws.com";  // Aquí pones la endpoint de tu RDS
$user = "admin";                          // Tu usuario de AWS RDS
$pass = "31354134162";                    // Tu contraseña de AWS RDS
 $db = "cotizador_db";  // El nombre de tu base de datos en AWS
// Crear conexión
$conn = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
