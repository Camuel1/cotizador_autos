<?php
include("db.php");

$sql = "
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS vehiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(100),
    modelo VARCHAR(100),
    anio INT,
    precio DECIMAL(10,2),
    imagen_url VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS cotizaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    vehiculo_id INT,
    pie DECIMAL(10,2),
    cuotas INT,
    valor_cuota DECIMAL(10,2),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(id)
);
";

if ($conn->multi_query($sql) === TRUE) {
    echo "✅ Tablas creadas correctamente.";
} else {
    echo "❌ Error al crear tablas: " . $conn->error;
}

$conn->close();
?>
