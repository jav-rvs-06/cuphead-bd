<?php
require_once 'config.php';

// Este archivo crea un usuario administrador
// Ejecuta este archivo UNA VEZ visitándolo en el navegador: http://localhost/tu-proyecto/BD/crear-admin.php

// Primero, agregar las columnas si no existen
$conexion->query("ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS rol ENUM('usuario', 'admin') DEFAULT 'usuario'");
$conexion->query("ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS activo TINYINT(1) DEFAULT 1");

// Datos del admin
$nombre_admin = 'Administrador';
$correo_admin = 'admin@cuphead.com';
$contrasena_admin = 'admin123';

// Verificar si ya existe el admin
$stmt = $conexion->prepare("SELECT id FROM usuarios WHERE correo_electronico = ?");
$stmt->bind_param("s", $correo_admin);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "El usuario admin ya existe. Actualizando rol...<br>";
    $usuario = $result->fetch_assoc();
    $admin_id = $usuario['id'];
    
    // Actualizar rol y contraseña
    $hash = password_hash($contrasena_admin, PASSWORD_BCRYPT);
    $stmt = $conexion->prepare("UPDATE usuarios SET rol = 'admin', activo = 1, contrasena = ? WHERE id = ?");
    $stmt->bind_param("si", $hash, $admin_id);
    $stmt->execute();
    
    echo "Usuario admin actualizado correctamente.<br>";
} else {
    echo "Creando usuario admin...<br>";
    
    // Crear admin con contraseña hasheada
    $hash = password_hash($contrasena_admin, PASSWORD_BCRYPT);
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre_usuario, correo_electronico, contrasena, rol, activo) VALUES (?, ?, ?, 'admin', 1)");
    $stmt->bind_param("sss", $nombre_admin, $correo_admin, $hash);
    
    if ($stmt->execute()) {
        echo "Usuario admin creado correctamente.<br>";
    } else {
        echo "Error al crear admin: " . $stmt->error . "<br>";
    }
}

echo "<br><strong>Credenciales de acceso:</strong><br>";
echo "Correo: admin@cuphead.com<br>";
echo "Contraseña: admin123<br>";
echo "<br><em>IMPORTANTE: Por seguridad, elimina este archivo después de ejecutarlo.</em>";

$stmt->close();
$conexion->close();
?>
